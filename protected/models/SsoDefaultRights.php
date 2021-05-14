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
 * This is the model for table "sso_default_user_rights".
 *
 * The following are the variable columns in table 'sso_default_user_rights':
 *
 * @property int $id
 * @property string $source
 * @property int $global_firm_rights
 * @property int $is_consultant
 * @property int $is_surgeon
 * @property int $default_enabled
 */

class SsoDefaultRights extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return SsoDefaultRights the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'sso_default_user_rights';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, source, global_firm_rights, is_consultant, is_surgeon, default_enabled', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'sso_default_firms' => array(self::HAS_MANY, 'SsoDefaultFirms', 'sso_user_id'),
            'sso_default_roles' => array(self::HAS_MANY, 'SsoDefaultRoles', 'sso_user_id')
        );
    }

    public function saveDefaultRights($attributes = array())
    {
        $this->default_enabled = $attributes['default_enabled'];
        $this->id = $attributes['id'];
        $this->global_firm_rights = $attributes['global_firm_rights'];
        $this->is_consultant = $attributes['is_consultant'];
        $this->is_surgeon = $attributes['is_surgeon'];

        if (!array_key_exists('sso_default_firms', $attributes) || $attributes['sso_default_firms'] == '') {
            $attributes['sso_default_firms'] = array();
        }
        $firms = $attributes['sso_default_firms'];

        if ($this->default_enabled && !$this->global_firm_rights && count($firms) === 0) {
            throw new FirmSaveException();
        }

        if (!array_key_exists('sso_default_roles', $attributes)) {
            $attributes['sso_default_roles'] = array();
        }
        $roles = $attributes['sso_default_roles'];

        $transaction = Yii::app()->db->beginTransaction();

        try {
            SsoDefaultFirms::model()->deleteAll('sso_user_id = :sso_user_id', array('sso_user_id' => $this->id));
            foreach ($firms as $firm) {
                $ssoFirms = new SsoDefaultFirms();
                $ssoFirms->sso_user_id = $this->id;
                $ssoFirms->firm_id = $firm;
                if (!$ssoFirms->insert()) {
                    throw new CDbException('Unable to save default SSO firms');
                }
            }

            SsoDefaultRoles::model()->deleteAll('sso_user_id = :sso_user_id', array('sso_user_id' => $this->id));
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    $ssoRoles = new SsoDefaultRoles();
                    $ssoRoles->sso_user_id = $this->id;
                    $ssoRoles->roles = $role;
                    if (!$ssoRoles->insert()) {
                        throw new CDbException('Unable to save default SSO roles');
                    }
                }
            }

            $this->save();
            Audit::add('SSO', 'admin', serialize($attributes));
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
        }
    }
}
