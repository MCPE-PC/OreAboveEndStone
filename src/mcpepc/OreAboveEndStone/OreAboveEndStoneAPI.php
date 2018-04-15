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
use pocketmine\item\Item;

class OreAboveEndStoneAPI {
	private static $randomOreCode = null;
	function registerStatic() {
		if (static::$randomOreCode === null) {
			$oreCustomizeJson = json_decode(file_get_contents(OreAboveEndStone::getInstance()->getDataFolder() . 'ore.json'), true);
			foreach ($oreCustomizeJson as $oreCustomizeData) {
				$exp = isset($customizeData['chance']['exp']) ? explode(' ', $customizeData['chance']['exp']) : ['===', '1'];
				static::$randomOreCode = static::$randomOreCode . (static::$randomOreCode === null ? '' : ' else ') . 'if (mt_rand(' . $oreCustomizeData['chance']['min'] . ', ' . $oreCustomizeData['chance']['max'] . ') ' . $exp[0] . ' ' . $exp[1] . ') {$oreId = ' . $oreCustomizeData['id'] . ';$oreDamage = ' . $oreCustomizeData['damage'] . ';break;}';
			}
			static::$randomOreCode = 'for (;;) {' . static::$randomOreCode . '}';
		}
	}
	static function getMineralFromOre(int $oreId): Item {
		$mineralId = 4;
		$mineralDamage = 0;
		$mineralCount = 1;
		if ($oreId === 16) {
			$mineralId = 263;
		} else if ($oreId === 15) {
			$mineralId = 265;
		} else if ($oreId === 14) {
			$mineralId = 266;
		} else if ($oreId === 21) {
			$mineralId = 351;
			$mineralDamage = 4;
			$mineralCount = mt_rand(1, 4);
		} else if ($oreId === 74) {
			$mineralId = 331;
			$mineralCount = mt_rand(1, 4);
		} else if ($oreId === 56) {
			$mineralId = 264;
		} else if ($oreId === 129) {
			$mineralId = 388;
		} else if ($oreId !== 1) {
			$mineralId = $oreId;
		}
		return Item::get($mineralId, $mineralDamage, $mineralCount);
	}
	static function getRandomOre(): Block {
		$oreId = 1;
		$oreDamage = 0;
		eval(static::$randomOreCode);
		return Block::get($oreId, $oreDamage);
	}
	static function replace(string $subject, array $replaces): string {
		foreach ($replaces as $search => $replace) {
			$subject = str_replace('{' . $search . '}', $replace, $subject);
		}
		return $subject;
	}
}
