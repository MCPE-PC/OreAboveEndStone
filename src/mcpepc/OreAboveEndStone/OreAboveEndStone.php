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
use pocketmine\utils\Config;

class OreAboveEndStone extends PluginBase implements Listener {
	const CONFIG_VERSION = 1;
	private $code = '';
	private $enabled = true;
	protected $language = [];
	public function getMineralFromOre(int $ore_id): Item {
		$mineral_id = 4;
		$mineral_damage = 0;
		$mineral_count = 1;
		if ($ore_id === 16) {
			$mineral_id = 263;
		} else if ($ore_id === 15) {
			$mineral_id = 265;
		} else if ($ore_id === 14) {
			$mineral_id = 266;
		} else if ($ore_id === 21) {
			$mineral_id = 351;
			$mineral_damage = 4;
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
		return Item::get($mineral_id, $mineral_damage, $mineral_count);
	}
	// TODO: Suppport block and chance customizing
	public function getRandomOre(): Block {
		$ore_id = 1;
		$ore_damage = 0;
		eval($this->code);
		return Block::get($ore_id, $ore_damage);
	}
	public function onBlockPlace(BlockPlaceEvent $event): void {
		$block = $event->getBlock();
		if (!$event->isCancelled() && $this->enabled) {
			if ($block->getId() === 121) {
				$block->getLevel()->setBlock(new Position($block->getX(), $block->getY() + 1, $block->getZ(), $block->getLevel()), self::getRandomOre());
			} else if ($block->getLevel()->getBlock(new Position($block->getX(), $block->getY() - 1, $block->getZ(), $block->getLevel()))->getId() === 121) {
				$event->setCancelled(true);
				$block->getLevel()->setBlock(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()), self::getRandomOre());
			}
		}
	}
	public function onBlockBreak(BlockBreakEvent $event): void {
		$block = $event->getBlock();
		if ($block->getLevel()->getBlock(new Position($block->getX(), $block->getY() - 1, $block->getZ(), $block->getLevel()))->getId() === 121 && $this->enabled) {
			$event->setCancelled(true);
			$block->getLevel()->setBlock(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()), self::getRandomOre());
			$event->getPlayer()->getInventory()->addItem(self::getMineralFromOre($block->getId()));
		}
	}
	public function onDisable(): void {
		$this->getServer()->getLogger()->info(self::repl($this->language['plugin-disabled']));
		$this->getConfig()->save();
	}
	public function onEnable(): void {
		$this->getServer()->getLogger()->info(self::repl($this->language['plugin-enabled']));
		self::updateConfig();
		if (self::CONFIG_VERSION < $this->getConfig()->get('config-version', self::CONFIG_VERSION)) {
			$this->getServer()->getLogger()->critical(self::repl($this->language['incompatible-config'], ['reason' => $this->language['old-config']]));
			$this->getServer()->getPluginManager()->disablePlugin($this);
		} else if (self::CONFIG_VERSION > $this->getConfig()->get('config-version', self::CONFIG_VERSION)) {
			$this->getServer()->getLogger()->critical(self::repl($this->language['incompatible-config'], ['reason' => $this->language['old-plugin']]));
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onLoad(): void {
		$this->saveDefaultConfig();
		$this->saveResource('ore.json');
		$ore_json = json_decode(file_get_contents($this->getDataFolder() . 'ore.json'), true);
		foreach ($ore_json as $data) {
			$exp = isset($data['chance']['exp']) ? explode(' ', $data['chance']['exp']) : ['===', '1'];
			$this->code .= ($this->code === '' ? '' : ' else ') . 'if (mt_rand(' . $data['chance']['min'] . ', ' . $data['chance']['max'] . ') ' . $exp[0] . ' ' . $exp[1] . ') {$ore_id = ' . $data['id'] . ';$ore_damage = ' . $data['damage'] . ';break;}';
		}
		$this->code = 'for (;;) {' . $this->code . '}';
		$this->enabled = $this->getConfig()->get('enable');
		$this->language = (new Config($this->getFile() . 'resources/lang_' . $this->getConfig()->get('language', 'en') . '.properties', Config::PROPERTIES, (new Config($this->getFile() . 'resources/lang_en.properties', Config::PROPERTIES))->getAll()))->getAll();
	}
	public function repl(string $subject, array $vars = ['plugin' => 'OreAboveEndStone', 'author' => 'MCPE_PC']): string {
		$result = $subject;
		foreach ($vars as $search => $replace) {
			$result = str_replace('{' . $search . '}', $replace, $result);
		}
		return $result;
	}
	protected function updateConfig(): bool {
		if (self::CONFIG_VERSION >= $this->getConfig()->get('config-version', self::CONFIG_VERSION) &&  1 <= $this->getConfig()->get('config-version', self::CONFIG_VERSION)) {
			$this->getConfig()->setAll(array_merge((new Config($this->getFile() . 'resources/config.yml', Config::YAML))->getAll(), $this->getConfig()->getAll()));
			$this->getConfig()->set('config-version', self::CONFIG_VERSION);
			return true;
		} else {
			return false;
		}
	}
}
