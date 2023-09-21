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

class PvPCommand extends Command implements Listener {

    private $plugin;

    public function __construct(PluginBase $plugin) {
        parent::__construct("pvp", "Enable or disable PvP");
        $this->setPermission("protectionplus.pvp");
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
        $this->plugin->setPvPEnabled($player, true);
        $player->sendMessage("PvP is now enabled!");
        $player->sendTitle("PvP Enabled", "", 10, 40, 10);
    }

    private function disablePvP(Player $player): void {
        $this->plugin->setPvPEnabled($player, false);
        $player->sendMessage("PvP is now disabled!");
        $player->sendTitle("PvP Disabled", "", 10, 40, 10);
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof Player && !$this->plugin->isPvPEnabled($entity)) {
            $event->setCancelled(true);
        }
    }
}
