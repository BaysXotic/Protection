<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwnedTrait;

class ProtectCommand extends Command implements Listener {
    use PluginOwnedTrait;

    public function __construct(Plugin $owningPlugin) {
        parent::__construct("protection", "Enable or disable protection", null, [$owningPlugin]);
        $this->setPermission("protectionplus.protection");
        $owningPlugin->getServer()->getPluginManager()->registerEvents($this, $owningPlugin);
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
        $player->sendMessage("Protection is now enabled!");
        $player->sendTitle("Protection Enabled", "", 10, 40, 10);
        $this->getOwningPlugin()->setProtectionEnabled($player, true);
    }

    private function disableProtection(Player $player): void {
        $player->sendMessage("Protection is now disabled!");
        $player->sendTitle("Protection Disabled", "", 10, 40, 10);
        $this->getOwningPlugin()->setProtectionEnabled($player, false);
    }

    /**
     * @param BlockBreakEvent $event
     * @priority MONITOR
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->getOwningPlugin()->isProtectionEnabled($player)) {
            $event->setCancelled(true);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority MONITOR
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->getOwningPlugin()->isProtectionEnabled($player)) {
            $event->setCancelled(true);
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     * @priority MONITOR
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->getOwningPlugin()->isProtectionEnabled($player)) {
            $event->setCancelled(true);
        }
    }
}
