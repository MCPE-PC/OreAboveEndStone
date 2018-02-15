<?php

/**
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace mcpepc\OreAboveEndStone;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;

class OreAboveEndStone extends PluginBase implements Listener {
	public function getMineralFromOre(int $ore_id): Item {
		$mineral_id = 4;
		$mineral_meta = 0;
		$mineral_count = 1;
		if ($ore_id === 16) {
			$mineral_id = 263;
		} else if ($ore_id === 15) {
			$mineral_id = 265;
		} else if ($ore_id === 14) {
			$mineral_id = 266;
		} else if ($ore_id === 21) {
			$mineral_id = 351;
			$mineral_meta = 4;
			$mineral_count = mt_rand(1, 4);
		} else if ($ore_id === 74) {
			$mineral_id = 331;
			$mineral_count = mt_rand(1, 4);
		} else if ($ore_id === 56) {
			$mineral_id = 264;
		} else if ($ore_id === 129) {
			$mineral_id = 388;
		} else if ($ore_id !== 1) {
			$mineral_id = $ore_id;
		}
		return Item::get($mineral_id, $mineral_meta, $mineral_count);
	}
	public function getRandomOreBlock(): Block {
		$ore_id = 1;
		for (;;) {
			if (mt_rand(1, 3) === 1) {
				break;
			} else if (mt_rand(1, 5) === 1) {
				$ore_id = 16;
				break;
			} else if (mt_rand(1, 10) === 1) {
				$ore_id = 15;
				break;
			} else if (mt_rand(1, 25) <= 2) {
				$ore_id = 14;
				break;
			} else if (mt_rand(1, 20) === 1) {
				$ore_id = 21;
				break;
			} else if (mt_rand(1, 20) === 1) {
				$ore_id = 74;
				break;
			} else if (mt_rand(1, 100) === 1) {
				$ore_id = 56;
				break;
			} else if (mt_rand(1, 100) === 1) {
				$ore_id = 129;
				break;
			}
		}
		return Block::get($ore_id);
	}
	public function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onBlockPlace(BlockPlaceEvent $event): void {
		$block = $event->getBlock();
		if (!$event->isCancelled()) {
			if ($block->getId() === 121) {
				$block->getLevel()->setBlock(new Position($block->getX(), $block->getY() + 1, $block->getZ(), $block->getLevel()), self::getRandomOreBlock());
			} else if ($block->getLevel()->getBlock(new Position($block->getX(), $block->getY() - 1, $block->getZ(), $block->getLevel()))->getId() === 121) {
				$event->setCancelled(true);
				$block->getLevel()->setBlock(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()), self::getRandomOreBlock());
			}
		}
	}
	public function onBlockBreak(BlockBreakEvent $event): void {
		$block = $event->getBlock();
		if ($block->getLevel()->getBlock(new Position($block->getX(), $block->getY() - 1, $block->getZ(), $block->getLevel()))->getId() === 121) {
			$event->setCancelled(true);
			$block->getLevel()->setBlock(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()), self::getRandomOreBlock());
			$event->getPlayer()->getInventory()->addItem(self::getMineralFromOre($block->getId()));
		}
	}
}
