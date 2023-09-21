<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class ProtectCommand extends Command implements Listener {

    private $protectionActive = [];
    private $plugin;

    public function __construct(PluginBase $plugin) {
        parent::__construct("protection", "Toggle block protection");
        $this->setPermission("protectionplus.protect");
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command");
                return true;
            }

            $world = $sender->getWorld()->getFolderName();
            $action = strtolower($args[0] ?? "");

            switch ($action) {
                case "on":
                    $this->protectionActive[$world] = true;
                    $sender->sendMessage("Block protection is now active in the world $world.");
                    break;
                case "off":
                    unset($this->protectionActive[$world]);
                    $sender->sendMessage("Block protection is now inactive in the world $world.");
                    break;
                default:
                    $sender->sendMessage("Usage: /protection <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game");
        }
        return true;
    }

    protected function checkBlockPlaceBreak(Player $player): bool {
        $world = $player->getWorld()->getFolderName();
        if (!isset($this->wcfg[$world])) return true;
        if ($this->wcfg[$world] !== "protect") return false; // LOCKED!
        return $this->owner->canPlaceBreakBlock($player, $world);
    }

    /**
     * @param BlockBreakEvent $event
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();

        if (isset($this->protectionActive[$world])) {
            if (!$player->hasPermission("protectionplus.bypass")) {
                if ($this->checkBlockPlaceBreak($player)) {
                    $player->sendMessage("Block protection is active in the world $world. You cannot break blocks.");
                    $event->setCancelled(true);
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority HIGHEST
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();

        if (isset($this->protectionActive[$world])) {
            if (!$player->hasPermission("protectionplus.bypass")) {
                if ($this->checkBlockPlaceBreak($player)) {
                    $player->sendMessage("Block protection is active in the world $world. You cannot place blocks.");
                    $event->setCancelled(true);
                }
            }
        }
    }
}
