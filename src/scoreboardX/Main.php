<?php

declare(strict_types=1);

namespace ScoreboardX;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {

    /** @var array<string, bool> */
    private array $scoreboards = [];

    private Config $config;
    private PlaceholderParser $parser;
    private ScoreboardManager $manager;

    /** @var array<int, string> */
    private array $titleFrames = [];

    private int $titleFrameIndex = 0;

    protected function onEnable(): void {
        // Save default config
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();

        // Initialize parser and manager
        $this->parser = new PlaceholderParser($this);
        $this->manager = new ScoreboardManager($this, $this->parser);

        // Load title frames
        $frames = $this->config->get("title", []);
        $this->titleFrames = is_array($frames) ? ($frames["frames"] ?? []) : [];
        if (empty($this->titleFrames)) {
            $this->titleFrames = ["§6§lScoreboardX"];
        }

        // Register events
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // Main update task
        $interval = (int) $this->config->get("update-interval", 20);
        $interval = max(1, $interval); // Safety check

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if (!$player->isOnline()) continue; // Safety check

                // Skip if disabled for this player
                if (isset($this->scoreboards[$player->getName()]) && $this->scoreboards[$player->getName()] === false) {
                    continue;
                }
                
                $this->manager->update($player);
            }
        }), $interval);

        // Title animation task
        if ($this->config->getNested("title.animated", true)) {
            $speed = (int) $this->config->getNested("title.animation-speed", 20);
            $speed = max(1, $speed);

            $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
                $this->titleFrameIndex++;
                if ($this->titleFrameIndex >= count($this->titleFrames)) {
                    $this->titleFrameIndex = 0;
                }
            }), $speed);
        }

        $this->getLogger()->info(TF::GREEN . "ScoreboardX v1.0.0 enabled successfully.");
        $this->getLogger()->info(TF::GOLD . "§l§m----------------------");
        $this->getLogger()->info(TF::GOLD . "§lScoreboardX §fv1.0.0");
        $this->getLogger()->info(TF::WHITE . "Author: §bYourName");
        $this->getLogger()->info(TF::WHITE . "Download: §ahttps://github.com/YourRepo");
        $this->getLogger()->info(TF::GOLD . "§l§m----------------------");
    }

    protected function onDisable(): void {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $this->manager->remove($player);
        }
        $this->manager->clearAllCache();
        $this->getLogger()->info(TF::RED . "ScoreboardX disabled.");
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        // Default state from config
        $this->scoreboards[$player->getName()] = (bool) $this->config->get("enabled-by-default", true);

        // Delayed update (wait for client to fully join)
        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player): void {
            if ($player->isOnline()) {
                if ($this->scoreboards[$player->getName()] ?? true) {
                    $this->manager->update($player);
                }
            }
        }), 20);
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $name = $event->getPlayer()->getName();
        $this->manager->clearCache($name);
        unset($this->scoreboards[$name]);
    }

    /**
     * Get the current animated title.
     */
    public function getAnimatedTitle(): string {
        if (!$this->config->getNested("title.animated", true)) {
            $frames = $this->titleFrames;
            return $frames[0] ?? "§6ScoreboardX";
        }

        return $this->titleFrames[$this->titleFrameIndex] ?? "§6ScoreboardX";
    }

    /**
     * Get configured lines.
     * @return array<int, string>
     */
    public function getConfigLines(): array {
        $lines = $this->config->get("lines", []);
        return is_array($lines) ? $lines : [];
    }

    /**
     * Check if scoreboard is enabled for a player.
     */
    public function isEnabledFor(Player $player): bool {
        return $this->scoreboards[$player->getName()] ?? true;
    }

    /**
     * Enable or disable scoreboard for a player.
     */
    public function setEnabledFor(Player $player, bool $enabled): void {
        $this->scoreboards[$player->getName()] = $enabled;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() !== "scoreboard") {
            return false;
        }

        if (!$sender->hasPermission("scoreboardx.command")) {
            $sender->sendMessage(TF::RED . "You don't have permission to use this command.");
            return true;
        }

        if (count($args) === 0) {
            $sender->sendMessage(TF::YELLOW . "Usage: /scoreboard <on|off|reload|version> [player]");
            return true;
        }

        if (strtolower($args[0]) === "version") {
            $sender->sendMessage(TF::GOLD . "§l§m----------------------");
            $sender->sendMessage(TF::GOLD . "§lScoreboardX §fv1.0.0");
            $sender->sendMessage(TF::WHITE . "Author: §bYourName");
            $sender->sendMessage(TF::WHITE . "Download: §ahttps://github.com/YourRepo");
            $sender->sendMessage(TF::GOLD . "§l§m----------------------");
            return true;
        }

        $action = strtolower($args[0]);
        $target = null;

        if (isset($args[1])) {
            $target = $this->getServer()->getPlayerByPrefix($args[1]);
            if ($target === null) {
                $sender->sendMessage(TF::RED . "Player not found.");
                return true;
            }
        } elseif ($sender instanceof Player) {
            $target = $sender;
        } else {
            $sender->sendMessage(TF::RED . "Please specify a player name.");
            return true;
        }

        switch ($action) {
            case "on":
                $this->setEnabledFor($target, true);
                $this->manager->update($target);
                $target->sendMessage(TF::GREEN . "Scoreboard enabled.");
                if ($sender !== $target) {
                    $sender->sendMessage(TF::GREEN . "Scoreboard enabled for " . $target->getName() . ".");
                }
                break;

            case "off":
                $this->setEnabledFor($target, false);
                $this->manager->remove($target);
                $target->sendMessage(TF::RED . "Scoreboard disabled.");
                if ($sender !== $target) {
                    $sender->sendMessage(TF::RED . "Scoreboard disabled for " . $target->getName() . ".");
                }
                break;

            case "reload":
                // Remove all scoreboards
                foreach ($this->getServer()->getOnlinePlayers() as $p) {
                    $this->manager->remove($p);
                }
                $this->manager->clearAllCache();

                // Reload config
                $this->reloadConfig();
                $this->config = $this->getConfig();

                // Reload title frames
                $frames = $this->config->get("title", []);
                $this->titleFrames = is_array($frames) ? ($frames["frames"] ?? []) : [];
                if (empty($this->titleFrames)) {
                    $this->titleFrames = ["§6§lScoreboardX"];
                }
                $this->titleFrameIndex = 0;

                $sender->sendMessage(TF::GREEN . "ScoreboardX reloaded successfully.");
                break;

            default:
                $sender->sendMessage(TF::YELLOW . "Usage: /scoreboard <on|off|reload> [player]");
                break;
        }

        return true;
    }
}