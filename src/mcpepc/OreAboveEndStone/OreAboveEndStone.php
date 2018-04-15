<?php

/**
 * @author MCPE_PC <maxpjh0528@naver.com> (https://www.mcpepc.ml)
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
	private $enabled = true;
	private static $instance = null;
	protected $lang;
	private $notifier;
	static function getInstance(): self {
		return static::$instance;
	}
	function getLanguage(): OreAboveEndStoneLang {
		return $this->lang;
	}
	function getMessage(string $key, array $replaces = ['plugin' => 'OreAboveEndStone', 'author' => 'MCPE_PC']): string {
		return OreAboveEndStoneAPI::replace($this->getLanguage()->get($key), $replaces);
	}
	/**
	 * @ignoreCancelled
	*/
	function onBlockPlace(BlockPlaceEvent $event): void {
		$block = $event->getBlock();
		if ($this->enabled) {
			if ($block->getId() === 121) {
				$block->getLevel()->setBlock(new Position($block->getX(), $block->getY() + 1, $block->getZ(), $block->getLevel()), OreAboveEndStoneAPI::getRandomOre());
			} else if ($block->getLevel()->getBlock(new Position($block->getX(), $block->getY() - 1, $block->getZ(), $block->getLevel()))->getId() === 121) {
				$event->setCancelled();
				$block->getLevel()->setBlock(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()), OreAboveEndStoneAPI::getRandomOre());
			}
		}
	}
	function onBlockBreak(BlockBreakEvent $event): void {
		$block = $event->getBlock();
		if ($block->getLevel()->getBlock(new Position($block->getX(), $block->getY() - 1, $block->getZ(), $block->getLevel()))->getId() === 121 && $this->enabled) {
			$event->setCancelled();
			$block->getLevel()->setBlock(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()), OreAboveEndStoneAPI::getRandomOre());
			$event->getPlayer()->getInventory()->addItem(OreAboveEndStoneAPI::getMineralFromOre($block->getId()));
		}
	}
	function onDisable(): void {
		OreAboveEndStoneLogger::getLogger()->info($this->getMessage('plugin-disabled'));
		$this->getConfig()->save();
	}
	function onEnable(): void {
		$this->updateConfig();
		if (self::CONFIG_VERSION < $this->getConfig()->get('config-version', self::CONFIG_VERSION)) {
			OreAboveEndStoneLogger::getLogger()->critical($this->getMessage('incompatible-config', ['reason' => $this->getLanguage()->get('old-config')]));
			$this->getServer()->getPluginManager()->disablePlugin($this);
		} else if (self::CONFIG_VERSION > $this->getConfig()->get('config-version', self::CONFIG_VERSION)) {
			OreAboveEndStoneLogger::getLogger()->critical($this->getMessage('incompatible-config', ['reason' => $this->getLanguage()->get('old-plugin')]));
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		OreAboveEndStoneLogger::getLogger()->info($this->getMessage('plugin-enabled'));
	}
	function onLoad(): void {
		include_once($this->getFile() . 'vendor/autoload.php');
		$this->saveDefaultConfig();
		$this->saveResource('ore.json');
		$this->registerStatic();
		(new OreAboveEndStoneAPI)->registerStatic();
		(new OreAboveEndStoneLogger)->registerStatic();
		$this->enabled = $this->getConfig()->get('enable');
		$this->lang = new OreAboveEndStoneLang($this->getServer()->getProperty('settings.language'), $this->getFile() . 'resources/');
	}
	function registerStatic() {
		if (static::$instance === null) {
			static::$instance = $this;
		}
	}
	protected function updateConfig(): bool {
		if (self::CONFIG_VERSION >= $this->getConfig()->get('config-version', self::CONFIG_VERSION) &&  1 <= $this->getConfig()->get('config-version', self::CONFIG_VERSION)) {
			$this->getConfig()->setAll(\array_merge((new Config($this->getFile() . 'resources/config.yml', Config::YAML))->getAll(), $this->getConfig()->getAll()));
			$this->getConfig()->set('config-version', self::CONFIG_VERSION);
			return true;
		} else {
			return false;
		}
	}
}
