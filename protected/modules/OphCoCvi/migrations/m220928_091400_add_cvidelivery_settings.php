<?php

/**
 * (C) Copyright Apperta Foundation 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m220928_091400_add_cvidelivery_settings extends OEMigration
{
    private $settings;

    public function setUp() {

        $this->settings = array(
            array(
                'key' => 'cvi_docman_delivery_enabled',
                'name' => 'CVI delivery enable to send via docman',
                'default_value' => 'off',
                'value' => strtolower(getenv("CVI_DOCMAN_DELIVERY_ENABLED")) == 'true' ? 'On' : 'Off',
                'type' => 'Radio buttons',
                'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',            
            ),
            array(
                'key' => 'cvi_rcop_delivery_enabled',
                'name' => 'CVI delivery enable to send to RCOP',
                'default_value' => 'off',
                'value' => strtolower(getenv("CVI_RCOP_DELIVERY_ENABLED")) == 'true' ? 'On' : 'Off',
                'type' => 'Radio buttons',
                'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            ),
            array(
                'key' => 'cvi_la_delivery_enabled',
                'name' => 'CVI delivery enable to send to LA',
                'default_value' => 'off',
                'value' => strtolower(getenv("CVI_LA_DELIVERY_ENABLED")) == "true" ? 'On' : 'Off',
                'type' => 'Radio buttons',
                'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            ),
            array(
                'key' => 'cvi_eclo_notification_email',
                'name' => 'CVI sending email to ECLO',
                'default_value' => 'off',
                'value' => 'off',
                'type' => 'Radio buttons',
                'data' => 'a:2:{s:2:"on";s:2:"On";s:3:"off";s:3:"Off";}',
            ),
            array(
                'key' => 'cvi_eclo_sender_email',
                'name' => 'CVI sender of ECLO email',
                'default_value' => '',
                'value' => 'noreply@openeyes.org.uk',
                'type' => 'Text Field',
                'data' => '',
            ),
            array(
                'key' => 'cvi_eclo_target_email',
                'name' => 'CVI target of ECLO email',
                'default_value' => '',
                'value' => '',
                'type' => 'Text Field',
                'data' => '',
            ),
        );
    }

    public function up()
    {
        $this->setUp();
        foreach ($this->settings as $setting) {
            $this->insert('setting_metadata', array(
                'key' => $setting['key'],
                'name' => $setting['name'],
                'element_type_id' => null,
                'default_value' => '',
                'field_type_id' => $this->getSettingFieldIdByName($setting['type']),
                'data' => $setting['data'],
            ));
            $this->insert('setting_installation', array(
                'key' => $setting['key'],
                'value' => $setting['value'],
            ));
        };
    }

    public function down()
    {
    }
}
