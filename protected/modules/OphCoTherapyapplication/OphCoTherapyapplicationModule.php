<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the module class for $this->moduleSuffix.
 *
 * The followings are the available columns in table:
 *
 * @property string $moduleShortSuffix
 */
class OphCoTherapyapplicationModule extends BaseEventTypeModule
{
    // this property is really only relevant to gii auto-generation, specifically
    // for updates to the module through gii
    public $moduleShortSuffix;
    public $default_parameter_settings = array(
        'OphCoTherapyapplication_applicant_email' => 'Applicant Email Address Not Set',
        'OphCoTherapyapplication_chief_pharmacist' => 'No Chief Pharmacist Set',
        'OphCoTherapyapplication_chief_pharmacist_contact' => 'No Chief Pharmacist Contact Details Set',
        'OphCoTherapyapplication_email_size_limit' => '10MB',
        'OphCoTherapyapplication_cc_application' => false,
        'OphCoTherapyapplication_compliant_email_subject' => 'Therapy NOTIFICATION',
        'OphCoTherapyapplication_noncompliant_email_subject' => 'Therapy APPLICATION',
    );

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'OphCoTherapyapplication.models.*',
            'OphCoTherapyapplication.components.*',
            'OphCoTherapyapplication.services.*',
            'OphCoTherapyapplication.helpers.*',
            'OphTrIntravitrealinjection.models.*',
        ));

        $this->moduleShortSuffix = 'TherapyA';

        foreach ($this->default_parameter_settings as $k => $v) {
            if (!isset(Yii::app()->params[$k])) {
                Yii::app()->params[$k] = $v;
            }
        }
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }
}
