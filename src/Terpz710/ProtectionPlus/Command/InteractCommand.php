<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\block\Block;
use Terpz710\ProtectionPlus\Main;
use pocketmine\event\player\PlayerInteractEvent;

class InteractCommand extends Command implements Listener {

    private $plugin;
    private $interactionEnabled;

    public function __construct(PluginBase $plugin) {
        parent::__construct("interact", "Enable or disable interaction with chests, doors, and players");
        $this->setPermission("protectionplus.interact");
        $this->plugin = $plugin;
        $this->interactionEnabled = true;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (!$this->testPermission($sender)) {
                $sender->sendMessage("You do not have permission to use this command.");
                return true;
            }

            if (empty($args)) {
                $sender->sendMessage("Usage: /interact <on|off>");
                return false;
            }

            $subcommand = strtolower(array_shift($args));

            switch ($subcommand) {
                case "on":
                    $this->enableInteraction($sender);
                    break;
                case "off":
                    $this->disableInteraction($sender);
                    break;
                default:
                    $sender->sendMessage("Usage: /interact <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    private function enableInteraction(Player $player): void {
        $this->interactionEnabled = true;
        $player->sendMessage("Interaction with chests, doors, and players is now enabled.");
        $player->sendTitle("Interaction Enabled", "", 10, 40, 10);
    }

    private function disableInteraction(Player $player): void {
        $this->interactionEnabled = false;
        $player->sendMessage("Interaction with chests, doors, and players is now disabled.");
        $player->sendTitle("Interaction Disabled", "", 10, 40, 10);
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        
        if (!$this->interactionEnabled) {
            if ($block->getId() === Block::CHEST || $block->getId() === Block::TRAPPED_CHEST) {
                $event->setCancelled(true);
                $player->sendMessage("Chest interaction is currently disabled.");
                $player->sendTitle("Interaction Disabled", "Chest interaction is disabled", 10, 40, 10);
            } elseif ($block->isInteractable() || $event->getEntity() instanceof Player) {
                $event->setCancelled(true);
                $player->sendMessage("Interaction with this block or player is currently disabled.");
                $player->sendTitle("Interaction Disabled", "Block/player interaction is disabled", 10, 40, 10);
            }
        }
    }
}
