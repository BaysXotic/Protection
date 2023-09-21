<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockInteractEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class InteractCommand extends Command implements Listener {

    private $interactionActive = [];

    public function __construct(PluginBase $plugin) {
        parent::__construct("interact", "Toggle block interaction");
        $this->setPermission("protectionplus.interact");
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
                    $this->interactionActive[$world] = true;
                    $sender->sendMessage("Block interaction is now active in the $world.");
                    break;
                case "off":
                    unset($this->interactionActive[$world]);
                    $sender->sendMessage("Block interaction is now inactive in the $world.");
                    break;
                default:
                    $sender->sendMessage("Usage: /interact <on|off>");
            }
        } else {
            $sender->sendMessage("This command can only be used in-game");
        }
        return true;
    }

    public function onInteract(BlockInteractEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        $block = $event->getBlock();

        if (isset($this->interactionActive[$world])) {
            $allowedBlockTypes = [
                VanillaBlocks::CRAFTING_TABLE(),
                VanillaBlocks::CHEST(),
                VanillaBlocks::BREWING_STAND(),
          
            ];

            if (!in_array($block->getType(), $allowedBlockTypes)) {
                $player->sendMessage("Block interaction is active in this world. You cannot interact with this block.");
                $event->cancel();
            }
        }
    }
}
