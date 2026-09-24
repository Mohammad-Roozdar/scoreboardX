<?php

declare(strict_types=1);

namespace ScoreboardX;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class PlaceholderParser {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Parse all placeholders in a given string for a player.
     */
    public function parse(string $text, Player $player): string {
        $session = $player->getNetworkSession();
        $ping = $session->getPing();

        // Color code for ping
        if ($ping < 50) {
            $pingColor = TF::GREEN;
        } elseif ($ping < 150) {
            $pingColor = TF::YELLOW;
        } else {
            $pingColor = TF::RED;
        }

        $position = $player->getPosition();
        $server = Server::getInstance();

        // Calculate TPS
        $tps = $server->getTicksPerSecond();
        $tpsColor = $tps >= 18 ? TF::GREEN : ($tps >= 14 ? TF::YELLOW : TF::RED);

        $replacements = [
            "{player}" => $player->getName(),
            "{online}" => (string) count($server->getOnlinePlayers()),
            "{max}"    => (string) $server->getMaxPlayers(),
            "{ping}"   => $pingColor . $ping,
            "{world}"  => $player->getWorld()->getDisplayName(),
            "{x}"      => (string) round($position->getX()),
            "{y}"      => (string) round($position->getY()),
            "{z}"      => (string) round($position->getZ()),
            "{line}"   => TF::GRAY . str_repeat("-", 16),
            "{tps}"    => $tpsColor . round($tps, 1),
            "{time}"   => date("H:i:s"),
            "{date}"   => date("Y/m/d"),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}