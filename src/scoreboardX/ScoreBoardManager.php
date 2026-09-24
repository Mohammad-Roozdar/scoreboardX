<?php

declare(strict_types=1);

namespace ScoreboardX;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

class ScoreboardManager {

    private Main $plugin;
    private PlaceholderParser $parser;

    public function __construct(Main $plugin, PlaceholderParser $parser) {
        $this->plugin = $plugin;
        $this->parser = $parser;
    }

    /**
     * Send or update the scoreboard for a player.
     */
public function update(Player $player): void {
    if (!$player->isOnline()) {
        return;
    }

    $objectiveName = "sbx_" . strtolower($player->getName());
    $title = $this->plugin->getAnimatedTitle();

    $parsedLines = [];
    $lineCount = 0;
    foreach ($this->plugin->getConfigLines() as $line) {
        if ($lineCount >= 15) break;
        $parsedLines[] = $this->parser->parse($line, $player);
        $lineCount++;
    }

    $session = $player->getNetworkSession();

    // ==== حذف آبجکتیو قبلی ====
    $removeObj = new RemoveObjectivePacket();
    $removeObj->objectiveName = $objectiveName;
    $session->sendDataPacket($removeObj);

    // ==== ساخت آبجکتیو جدید ====
    $packet = new SetDisplayObjectivePacket();
    $packet->displaySlot = "sidebar";
    $packet->objectiveName = $objectiveName;
    $packet->displayName = $title;
    $packet->criteriaName = "dummy";
    $packet->sortOrder = 0;
    $session->sendDataPacket($packet);

    // ==== اضافه کردن خطوط ====
    $entries = [];
    $count = count($parsedLines);
    foreach ($parsedLines as $index => $line) {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $objectiveName;
        $entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $entry->customName = $line . "§r";;
        $entry->score = $count - $index;
        $entry->scoreboardId = $index;
        $entries[] = $entry;
    }

    $scorePacket = new SetScorePacket();
    $scorePacket->type = SetScorePacket::TYPE_CHANGE;
    $scorePacket->entries = $entries;
    $session->sendDataPacket($scorePacket);
}

    /**
     * Fully remove the scoreboard from a player.
     */
    public function remove(Player $player): void {
        if (!$player->isOnline()) {
            return;
        }

        $objectiveName = "sbx_" . strtolower($player->getName());

        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = $objectiveName;
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    /**
     * These methods are kept empty so Main.php doesn't break.
     */
    public function clearCache(string $playerName): void {
        // Cache removed
    }

    public function clearAllCache(): void {
        // Cache removed
    }
}