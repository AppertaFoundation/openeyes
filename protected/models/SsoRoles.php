<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model for table "sso_roles".
 *
 * The following are the variable columns in table 'sso_roles':
 *
 * @property int $id
 * @property int $name
 */

class SsoRoles extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return SsoRoles the static model class
     */
    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'sso_roles';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, name', 'safe', 'on' => 'search'),
            array('name', 'unique'),
        );
    }

    public function relations()
    {
        return array(
            'sso_roles_assignment' => array(self::HAS_MANY, 'SsoRolesAuthAssignment', 'sso_role_id'),
        );
    }

    public function saveRolesAuthAssignment($ssoName, $ssoAttributes = array(), $id = null)
    {
        $transaction = Yii::app()->db->beginTransaction();

        $this->name = $ssoName;

        try {
            if (!$id) {
                $this->insert();
                $id = $this->id;
            }

            SsoRolesAuthAssignment::model()->deleteAll('sso_role_id = :id', [':id' => $id]);
            if (!empty($ssoAttributes)) {
                foreach ($ssoAttributes as $attribute) {
                    $ssoRoleAssignment = new SsoRolesAuthAssignment();
                    $ssoRoleAssignment->sso_role_id = $id;
                    $ssoRoleAssignment->authitem_role = $attribute;
                    if (!$ssoRoleAssignment->insert()) {
                        throw new CDbException('Unable to save SSO Role Mappings');
                    }
                }
            }
            if ($this->save()) {
                Audit::add('SSO', 'SSO-role-modified', 'SSO Role "'. $this->name . '" was modified with values: '. implode(', ', $ssoAttributes));
                Yii::app()->user->setFlash('Success', 'SSO Role successfully updated');
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
        }
    }
}
