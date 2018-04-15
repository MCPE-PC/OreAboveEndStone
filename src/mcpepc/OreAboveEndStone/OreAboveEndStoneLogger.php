<?php

/**
 * @author MCPE_PC <maxpjh0528@naver.com> (https://www.mcpepc.ml)
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General License for more details.
 *
 *    You should have received a copy of the GNU General License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace mcpepc\OreAboveEndStone;

use LogLevel;
use pocketmine\utils\MainLogger;

class OreAboveEndStoneLogger implements \Logger {
	private static $logger = null;
	static function getLogger(): OreAboveEndStoneLogger {
		return static::$logger;
	}
	function registerStatic() {
		if (static::$logger === null) {
			static::$logger = $this;
		}
	}
	function emergency($message) {
		$this->log(LogLevel::EMERGENCY, $message);
	}
	function alert($message) {
		$this->log(LogLevel::ALERT, $message);
	}
	function critical($message) {
		$this->log(LogLevel::CRITICAL, $message);
	}
	function error($message) {
		$this->log(LogLevel::ERROR, $message);
	}
	function warning($message) {
		$this->log(LogLevel::WARNING, $message);
	}
	function notice($message) {
		$this->log(LogLevel::NOTICE, $message);
	}
	function info($message) {
		$this->log(LogLevel::INFO, $message);
	}
	function debug($message) {
		$this->log(LogLevel::DEBUG, $message);
	}
	function log($level, $message) {
		MainLogger::getLogger()->log($level, '[OreAboveEndStone] ' . $message);
	}
	function logException(\Throwable $e, $trace = null){
		MainLogger::getLogger()->logException($e, $trace);
	}
}
