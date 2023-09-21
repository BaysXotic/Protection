<?php

declare(strict_types=1);

namespace Terpz710\ProtectionPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class ProtectCommand extends Command implements Listener {

    private $protectionActive = [];

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

        $world = $sender->getWorld()->getFolderName();
        $action = strtolower($args[0] ?? "");

        switch ($action) {
            case "on":
                $this->protectionActive[$world] = true;
                $sender->sendMessage("Block protection is now active in the $world.");
                break;
            case "off":
                if (isset($this->protectionActive[$world])) {
                    unset($this->protectionActive[$world]);
                    $sender->sendMessage("Block protection is now inactive in the $world.");
                } else {
                    $sender->sendMessage("Block protection is already inactive in the $world.");
                }
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
        $world = $player->getWorld()->getFolderName();
        if (isset($this->protectionActive[$world])) {
            $player->sendMessage("Block protection is active in this world. You cannot break blocks.");
            $event->cancel();
        }
        $this->handleBlockAction($event, $player);
    }

    /**
     * @param BlockPlaceEvent $event
     * @priority HIGHEST
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        if (isset($this->protectionActive[$world])) {
            $player->sendMessage("Block protection is active in this world. You cannot place blocks.");
            $event->cancel();
        }
        $this->handleBlockAction($event, $player);
    }

    /**
     * @param PlayerBucketEmptyEvent $event
     * @priority HIGHEST
     */
    public function onPlayerEmptyBucket(PlayerBucketEmptyEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        if (isset($this->protectionActive[$world])) {
            $player->sendMessage("Block protection is active in this world. You cannot empty buckets.");
            $event->cancel();
        }
    }

    /**
     * @param PlayerBucketFillEvent $event
     * @priority HIGHEST
     */
    public function onPlayerFillBucket(PlayerBucketFillEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        if (isset($this->protectionActive[$world])) {
            $player->sendMessage("Block protection is active in this world. You cannot fill buckets.");
            $event->cancel();
        }
    }

    /**
     * @param PlayerDropItemEvent $event
     * @priority HIGHEST
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld()->getFolderName();
        if (isset($this->protectionActive[$world])) {
            $player->sendMessage("Block protection is active in this world. You cannot drop items.");
            $event->cancel();
        }
    }

    /**
 * Handle block action and send a message to the player.
 *
 * @param $event
 * @param Player $player
 */
private function handleBlockAction($event, Player $player): void {
    if (isset($this->protectionActive[$player->getWorld()->getFolderName()])) {
        if ($event->isCancelled()) return;
        $event->cancel();
    }
}

/**
 * @param PlayerInteractEvent $event
 * @priority HIGHEST
 */
public function onPlayerInteract(PlayerInteractEvent $event): void {
    $player = $event->getPlayer();
    $world = $player->getWorld()->getFolderName();
    if (isset($this->protectionActive[$world]) && $player->getInventory()->getItemInHand()->getId() !== 0) {
        $player->sendMessage("Block protection is active in this world. You cannot use items.");
        $event->cancel();
        }
    }
}
