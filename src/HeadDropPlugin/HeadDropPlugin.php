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

declare(strict_types=1);

namespace HeadDropPlugin;

/* TODO
 * 1. When player died, their head with their skin will drop #DONE
 * 2. The head will be named by their username #DONE
 * 3. When a player killed the player, a customizable percentage of the balance will be added into the winner's account #DONE
 * 4. command /heads show the top 10 richest players with the amount of money player can get after killing them #SKIP
 * 5. command /sellhead will sell all head in the inventory
 */
use pocketmine\plugin\PluginBase;
use HeadDropPlugin\Commands\HeadsCommand;
use HeadDropPlugin\Listeners\PlayerDeathListener;


class HeadDropPlugin extends PluginBase {

    /** @var array */
    public $conf;

    public function onEnable(): void {
        $this->saveDefaultConfig();

        $this->getLogger()->info("HeadDrop Plugin enabled");
        $this->registerAllEvents();

        $this->conf = $this->getConfig()->getAll();
    }

    public function onDisable(): void {
        $this->getLogger()->info("HeadDrop Plugin Disabled");
    }

    /**
     * This function register all the Events
     */
    public function registerAllEvents() {
        $this->getServer()->getPluginManager()->registerEvents(new PlayerDeathListener($this), $this);
    }
}



