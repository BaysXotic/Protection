<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class ProtectCommand extends Command implements Listener {

    private $protectionActive = false;

    public function __construct(PluginBase $plugin) {
        parent::__construct("protection", "Toggle block protection");
        $this->setPermission("protectionplus.protect");
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command");
                return true;
            }

            $action = strtolower($args[0] ?? "");

            switch ($action) {
                case "on":
                    $this->protectionActive = true;
                    $sender->sendMessage("Block protection is now active.");
                    break;
                case "off":
                    $this->protectionActive = false;
                    $sender->sendMessage("Block protection is now inactive.");
                    break;
                default:
                    $sender->sendMessage("Usage: /protection <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game");
        }
        return true;
    }

    /**
     * @param BlockBreakEvent $event
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();

        if ($this->protectionActive) {
            if (!$player->hasPermission("protectionplus.bypass")) {
             if($player->getWorld()->getWorldByName) {
                $player->sendMessage("Block protection is active. You cannot break blocks.");
                $event->setCancelled(true);
                }
            }
        }
    }
}
