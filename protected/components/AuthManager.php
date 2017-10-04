<?php
/**
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Overridden version of {@link CDbAuthManager} to allow defining business rules in a slightly more structured manner.
 */
class AuthManager extends CDbAuthManager
{
    private $rulesets = array();
    private $user_assignments = array();

    /**
     * AuthManager constructor.
     */
    public function __construct()
    {
        $this->registerRuleset('core', new AuthRules());

        if (isset(Yii::app()->params['additional_rulesets'])) {
            foreach (Yii::app()->params['additional_rulesets'] as $r) {
                $this->registerRuleset($r['namespace'], new $r['class']());
            }
        }
    }

    /**
     * Override of parent to utlise the namespaced rulesets that have been defined.
     *
     * @param string $bizRule
     * @param array  $params
     * @param mixed  $data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function executeBizRule($bizRule, $params, $data)
    {
        if (!$bizRule) {
            return true;
        }

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

        return call_user_func_array(array($ruleSet, $rule), array_merge((array) $data, $params));
    }

    /**
     * Mechanism to store ruleset objects against a "namespace" for use when executing business rules.
     *
     * @param string $namespace Name of module
     * @param object $ruleset   Object on which the rule methods are defined
     */
    public function registerRuleset($namespace, $ruleset)
    {
        $this->rulesets[$namespace] = $ruleset;
    }

    /**
     * Caching wrapper on the auth assignments for a user.
     *
     * @param mixed $user_id
     *
     * @return mixed
     */
    public function getAuthAssignments($user_id)
    {
        if (!isset($this->user_assignments[$user_id])) {
            $this->user_assignments[$user_id] = parent::getAuthAssignments($user_id);
        }

        return $this->user_assignments[$user_id];
    }
}
