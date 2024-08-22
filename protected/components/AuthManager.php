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

    public const ADMIN_ROLE_NAME = "admin";

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

        // Always pass the data as the first parameter for a uniform calling convention.
        array_unshift($params, $data);

        return call_user_func_array(array($ruleSet, $rule), $params);
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

    /**
     * setOrUpdateAssignment
     *
     * To get around the fact that the AuthManager assign function only inserts rows of auth items
     * instead inserting or updating, we have this function.
     *
     * This is useful mainly for updating data in existing auth item entries.
     *
     * If there is an existing row, the bizRule value will need to match other an exception will be thrown.
     *
     *  @param $itemName string
     *  @param $usedId mixed
     *  @param $bizRule string|null
     *  @param $data mixed
     *  @return CAuthAssignment
     */
    public function setOrUpdateAssignment(string $itemName, $userId, ?string $bizRule = null, $data = null): CAuthAssignment
    {
        $auth = $this->getAuthAssignment($itemName, $userId);

        if (!$auth) {
            return $this->assign($itemName, $userId, $bizRule, $data);
        }

        if ($auth->bizRule !== $bizRule) {
            throw new Exception('The supplied bizRule "' . $bizRule . '" does not match the existing bizRule "' . $auth->bizRule . '"');
        }

        $auth->setData($data);

        return $auth;
    }

    /**
     * hasRole
     * 
     * Utility to return bool if user has specified role.
     * 
     * @param $user_id int
     * @param $target_role string
     * 
     * @return bool
     */
    public function hasRole($user_id, $target_role): bool
    {
        if(!$user_id) {
            throw new InvalidArgumentException('Cannot check if user has role of "' . $target_role . '" when no user supplied.');
        }
        if(!$target_role) {
            throw new InvalidArgumentException('Cannot check if user has role when no target role supplied.');
        }

        foreach ($this->getRoles($user_id) as $role) {
            if ($target_role === $role->name) {
                return true;
            }
        }

        return false;
    }

    /**
     * getAssignableRoles
     * 
     * Returns array of roles specified user can allocate
     * 
     * @param $user_id int
     * 
     * @return CAuthItem[]
     */
    public function getAssignableRoles($user_id) {
        $allRoles = $this->getRoles();
        
        if ($this->hasRole($user_id, self::ADMIN_ROLE_NAME)) {
            return $allRoles;
        } else {
            // only admin users can assign new admins
            return array_filter($allRoles, function ($role) {
                return $role->name !== self::ADMIN_ROLE_NAME;
            });
        }
    }
}
