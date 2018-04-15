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

class OreAboveEndStoneLang {
	const FALLBACK_LANGUAGE = 'eng';
	protected $lang = [];
	protected $langName;
	function __construct(string $lang, string $path, string $fallback = self::FALLBACK_LANGUAGE) {
		if (\file_exists($path . 'lang_' . $lang . '.properties')) {
			$this->langName = $lang;
			$this->lang = $this->parseProperties(\file_get_contents($path . 'lang_' . $lang . '.properties'));
		} else {
			$this->langName = $fallback;
			$this->lang = $this->parseProperties(\file_get_contents($path . 'lang_' . $fallback . '.properties'));
		}
	}
	function get(string $id): string {
		return $this->lang[$id];
	}
	private function parseProperties(string $content): array {
		$properties = [];
		if(\preg_match_all('/([a-zA-Z0-9\-_\.]*)=([^\r\n]*)/u', $content, $matches) > 0){
			foreach($matches[1] as $index => $key){
				$value = \str_replace('\\', '', trim($matches[2][$index]));
				switch(\strtolower($value)){
					case 'on':
					case 'true':
					case 'yes':
						$value = true;
						break;
					case 'off':
					case 'false':
					case 'no':
						$value = false;
						break;
				}
				$properties[$key] = $value;
			}
		}
		return $properties;
	}
}
