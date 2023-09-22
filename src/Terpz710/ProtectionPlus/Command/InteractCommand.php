<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\BaseInventory;
use pocketmine\inventory\PlayerCraftingInventory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\plugin\PluginBase;

class InteractCommand extends Command implements Listener {

    private $interactionEnabled = true;

    public function __construct(PluginBase $plugin) {
        parent::__construct("interaction", "Toggle block interaction");
        $this->setPermission("protectionplus.interaction");
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command");
                return true;
            }

            if (isset($args[0])) {
                $action = strtolower($args[0]);
                switch ($action) {
                    case "on":
                        if (!$this->interactionEnabled) {
                            $this->interactionEnabled = true;
                            $sender->sendMessage("Block interaction is now active.");
                        } else {
                            $sender->sendMessage("Block interaction is already active.");
                        }
                        break;
                    case "off":
                        if ($this->interactionEnabled) {
                            $this->interactionEnabled = false;
                            $sender->sendMessage("Block interaction is now inactive.");
                        } else {
                            $sender->sendMessage("Block interaction is already inactive.");
                        }
                        break;
                    default:
                        $sender->sendMessage("Usage: /interaction <on|off>");
                        return false;
                }
            } else {
                $sender->sendMessage("Usage: /interaction <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game");
        }
        return true;
    }

    /**
     * @param InventoryOpenEvent $event
     * @priority HIGHEST
     */
    public function onInventoryOpen(InventoryOpenEvent $event): void {
        $player = $event->getPlayer();
        $inventory = $event->getInventory();

        if (!$this->interactionEnabled && ($inventory instanceof PlayerCraftingInventory || $inventory instanceof BaseInventory)) {
            $player->sendMessage("Inventory interaction is blocked.");
            $event->cancel();
        }
    }
}
