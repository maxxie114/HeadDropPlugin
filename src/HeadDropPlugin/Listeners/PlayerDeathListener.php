<?php
/**
 * MIT License
 *
 * Copyright (c) 2019 maxxie115
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace HeadDropPlugin\Listeners;

use HeadDropPlugin\HeadDropPlugin;
use onebone\economyapi\EconomyAPI;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use \pocketmine\event\Listener;
use \pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;

class PlayerDeathListener implements Listener {

    /** @var HeadDropPlugin */
    private $plugin;

    /** @var EconomyAPI */
    private $economyapi;

    public function __construct(HeadDropPlugin $plugin) {
        $this->economyapi = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event) {
        //TODO set drop playerHead with the skin and with the playername
        $drops = $event->getDrops();
        $drops[] = self::getPlayerHeadItem($event->getPlayer()->getName());
        $event->setDrops($drops);
        // As soon as the player died, the attacker get a certain percentage of the money
        // check if the attacker is a player
        $ev = $event->getPlayer()->getLastDamageCause(); // EntityDamageEvent
        if($ev instanceof EntityDamageByEntityEvent) {
            $attacker = $ev->getDamager();
            if($attacker instanceof Player) {
                $balance = $this->getDeathPlayerBalance($event->getPlayer()->getName());
                $attackerOldBalance = $this->economyapi->myMoney($attacker->getName());
                $attackerNewBalance = $attackerOldBalance + $balance;
                $this->economyapi->setMoney($attacker,$attackerNewBalance);

                // deduct from the victim's balance
                $victimOldBalance = $this->economyapi->myMoney($event->getPlayer()->getName());
                $victimNewBalance = $victimOldBalance - $balance;
                $this->economyapi->setMoney($event->getPlayer()->getName(), $victimNewBalance);
            }
        }
    }


    /**
     * @param Skin $skin
     * @param string $name
     * @return Item
     */
    public static function getPlayerHeadItem(string $name) : Item {
        /*
        $tag = new CompoundTag();
        // $tag2 = $tag->setString('Name', $name);
        $tag3 = $tag->setByteArray('Data', $skin->getSkinData());
        $item->setCustomBlockData($tag3);
        */
        $item = ItemFactory::get(Item::MOB_HEAD, 3);
        $item->setCustomName("head of ". $name);
        return $item;
    }

    /**
     * This function return the final balance the dead player lost after deduction by percentage
     *
     * @param String $name
     * @return int $finalBalance
     */
    public function getDeathPlayerBalance(String $name): int {
        $balance = $this->economyapi->myMoney($name);
        $percentage = $this->plugin->conf['percentage'];
        $finalBalance = $balance * ($percentage / 100);
        return $finalBalance;
    }

}