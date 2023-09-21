<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\tile\ItemFrame;
use pocketmine\tile\Sign;

class InteractCommand extends Command implements Listener {
    use PluginOwnedTrait;

    public function __construct(Plugin $owningPlugin) {
        parent::__construct("interact", "Enable or disable interaction with doors, trapdoors, and chests", null, [$owningPlugin]);
        $this->setPermission("protectionplus.interact");
        $owningPlugin->getServer()->getPluginManager()->registerEvents($this, $owningPlugin);
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
        $player->sendMessage("Interaction is now enabled!");
        $player->sendTitle("Interaction Enabled", "", 10, 40, 10);
        $this->getOwningPlugin()->setAllowInteraction($player->getName(), true);
    }

    private function disableInteraction(Player $player): void {
        $player->sendMessage("Interaction is now disabled!");
        $player->sendTitle("Interaction Disabled", "", 10, 40, 10);
        $this->getOwningPlugin()->setAllowInteraction($player->getName(), false);
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority MONITOR
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if (!$this->getOwningPlugin()->isInteractionAllowed($player->getName())) {
            $block = $event->getBlock();
            $id = $block->getId();

            $blockedBlocks = [
                // types of doors
                324, 330, 427, 428, 429, 430,
                // types of trapdoors
                96, 167, 183, 184, 185,
                // types of chests
                27, 28, 54, 146, 338,
            ];

            if (in_array($id, $blockedBlocks, true)) {
                $event->setCancelled();
                $player->sendMessage("You can't interact with this block while interaction is disabled.");
            } elseif ($block->getTile() instanceof ItemFrame || $block->getTile() instanceof Sign) {
                $event->setCancelled();
                $player->sendMessage("You can't interact with this entity while interaction is disabled.");
            }
        }
    }
}

