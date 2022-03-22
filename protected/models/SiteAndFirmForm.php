<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class SiteAndFirmForm extends CFormModel
{
    public $site_id;
    public $firm_id;

    public function rules()
    {
        return [
            ['firm_id, site_id', 'required'],
            ['site_id', 'isAccessible']
        ];
    }

    public function isAccessible($attribute, $params)
    {
        $user_auth = Yii::app()->session['user_auth'];
        $user = $user_auth->user;
        $inst_auth = $user_auth->institutionAuthentication;

        $filtered_sites = array_map(
            function ($site) {
                return $site->id;
            },
            array_filter(
                Institution::model()->getCurrent()->sites,
                function ($site) use ($user) {
                    return !UserAuthentication::userHasExactMatch($user, $site->institution_id, $site->id);
                }
            )
        );

        $selected_site = Site::model()->findByPk($this->site_id);
        if ($inst_auth->institution_id == $selected_site->institution_id && $inst_auth->site_id == $this->site_id) {
            return true;
        } elseif (!in_array($this->site_id, $filtered_sites)) {
            $this->addError('site_id', "This user cannot access this site with current credentials.");
            throw new CHttpException(403, 'Unable to change to selected site, user not authorized');
        }
        return false;
    }

    public function attributeLabels()
    {
        return array(
            'firm_id' => Firm::contextLabel(),
            'site_id' => 'Site',
        );
    }
}
