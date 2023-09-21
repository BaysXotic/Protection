<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use Terpz710\ProtectionPlus\Main;

class ProtectCommand extends Command implements Listener {

    private $plugin;

    public function __construct(PluginBase $plugin) {
        parent::__construct("protection", "Enable or disable protection");
        $this->setPermission("protectionplus.protection");
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /protection <on|off>");
                return false;
            }

            $subcommand = strtolower(array_shift($args));

            switch ($subcommand) {
                case "on":
                    $this->enableProtection($sender);
                    break;
                case "off":
                    $this->disableProtection($sender);
                    break;
                default:
                    $sender->sendMessage("Usage: /protection <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function enableProtection(Player $player): void {
        $this->plugin->setProtectionEnabled($player, true);
        $player->sendMessage("Protection is now enabled!");
        $player->sendTitle("Protection Enabled", "", 10, 40, 10);
    }

    private function disableProtection(Player $player): void {
        $this->plugin->setProtectionEnabled($player, false);
        $player->sendMessage("Protection is now disabled!");
        $player->sendTitle("Protection Disabled", "", 10, 40, 10);
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof Player && !$this->plugin->isProtectionEnabled($entity)) {
            $event->setCancelled(true);
        }
    }
}
