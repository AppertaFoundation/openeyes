<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Overridden version of {@link CDbAuthManager} to allow defining business rules in a slightly more structured manner.
 */
class AuthManager extends CDbAuthManager
{
	private $rulesets = array();
	private $user_assignments = array();

	public function __construct()
	{
		$this->registerRuleset('core', new AuthRules);
	}

	/**
	 * @param string $bizRule
	 * @param array $params
	 * @param mixed $data
	 * @return bool
	 */
	public function executeBizRule($bizRule, $params, $data)
	{
		if (!$bizRule) return true;

		$bits = explode('.', $bizRule, 2);

		if (count($bits) == 1) {
			$namespace = 'core';
			$rule = $bizRule;
		} else {
			$namespace = $bits[0];
			$rule = $bits[1];
		}

		if (!isset($this->rulesets[$namespace])) {
			throw new Exception("Unknown ruleset '{$namespace}' for business rule '{$bizRule}'");
		}

		$ruleSet = $this->rulesets[$namespace];

		if (!method_exists($ruleSet, $rule)) {
			throw new Exception("Undefined business rule: '{$bizRule}'");
		}

		unset($params['userId']);

		return call_user_func_array(array($ruleSet, $rule), array_merge((array)$data, $params));
	}

	/**
	 * @param string $namespace Name of module
	 * @param object $object Object on which the rule methods are defined
	 */
	public function registerRuleset($namespace, $ruleset)
	{
		$this->rulesets[$namespace] = $ruleset;
	}

	public function getAuthAssignments($user_id)
	{
		if (!isset($this->user_assignments[$user_id])) {
			$this->user_assignments[$user_id] = parent::getAuthAssignments($user_id);
		}
		return $this->user_assignments[$user_id];
	}
}
