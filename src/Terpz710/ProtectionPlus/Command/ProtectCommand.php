<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwnedTrait;

class PvPCommand extends Command {
    use PluginOwnedTrait;

    public function __construct(Plugin $owningPlugin) {
        parent::__construct("pvp", "Enable or disable PvP protection", null, [$owningPlugin]);
        $this->setPermission("protectionplus.pvp");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /pvp <on|off>");
                return false;
            }

            $subcommand = strtolower(array_shift($args));

            switch ($subcommand) {
                case "on":
                    $this->enablePvP($sender);
                    break;
                case "off":
                    $this->disablePvP($sender);
                    break;
                default:
                    $sender->sendMessage("Usage: /pvp <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function enablePvP(Player $player): void {
        $player->setPvP(true);
        $player->sendMessage("PvP protection is now disabled!");
        $player->sendTitle("PvP Enabled", "", 10, 40, 10);
    }

    private function disablePvP(Player $player): void {
        $player->setPvP(false);
        $player->sendMessage("PvP protection is now enabled!");
        $player->sendTitle("PvP Disabled", "", 10, 40, 10);
    }
}
