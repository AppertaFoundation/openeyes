<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class HieIntegration
 *
 * Integration component for Hie - provides link to HIE based on module
 * configuration.
 */

class HieIntegration extends \CApplicationComponent
{
    /**
     * @var string
     */
    public $hie_usr_org = '';

    /**
     * @var string
     */
    public $hie_usr_fac = '';

    /**
     * @var string
     */
    public $hie_external = '';

    /**
     * @var string
     */
    public $hie_org_user = '';

    /**
     * @var string
     */
    public $hie_org_pass = '';

    /**
     * @var string
     */
    public $hie_aes_encryption_password = '';

    /**
     * @var string
     */
    public $hie_remote_url = '';

    /**
     * @var User
     */
    public $user;

    /**
     * @var Patient
     */
    public $patient;

    /**
     * @var Array
     */
    private $data = [];

    public function getConfigValue($value)
    {
        $app = Yii::app();
        return $app->params[$value];
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $config_keys = [
            'hie_usr_org',
            'hie_usr_fac',
            'hie_external',
            'hie_org_user',
            'hie_org_pass',
            'hie_aes_encryption_password',
            'hie_remote_url'
        ];

        foreach ($config_keys as $value) {
            if (strlen($this->{$value} = $this->getConfigValue($value)) === 0) {
                throw new Exception($value. ' is not set.');
            }
        }

        $user_id = \Yii::app()->session['user']->id;
        $this->user = \User::model()->findByPk($user_id);
    }

    /**
     * Collect and Generate data structure to Url encrypt
     *
     * @param Patient $patient
     * @param User $user
     * @param $nhs_number
     * @return array
     * @throws Exception
     */
    private function generateDataToEncryptedUrl(\Patient $patient, \User $user, $nhs_number)
    {
        $pat_cmrn = str_replace(' ', '', $nhs_number );
        $pat_fname = $patient->first_name;
        $pat_lname = $patient->last_name;
        $pat_dob = str_replace('-', '', $patient->dob);

        $usr_name = $user->first_name . ' ' . $user->last_name;
        $usr_dsplynm = $user->first_name . ', ' . $user->last_name;
        $usr_position = $user->getHieAccessLevel()->hieAccessLevel->name;

        $permission = 'Yes';
        $emerg_reason = '';

        if (strtolower($permission) === 'other' || strtolower($permission) === 'emergency') {
            $reason = 'Reason'; /* If it will be used, please fill it with the correct value. */
            $emerg_reason = substr( $reason, 0, 50);
        }

        date_default_timezone_set('UTC');

        $data = [
            "USR_NAME" => $usr_name,
            "USR_POSITION" => $usr_position,
            'USR_DSPLYNM' => $usr_dsplynm,
            "USR_ORG" => $this->hie_usr_org,
            "USR_FAC" => $this->hie_usr_fac,
            "PAT_CMRN" => $pat_cmrn, // NHS number
            "PAT_FNAME" => strtoupper($pat_fname),
            "PAT_LNAME" => strtoupper($pat_lname),
            "PAT_DOB" => $pat_dob,
            "EXPIRATION" => date("Y-m-d\TH:i:s"),
            "PERMISSION" => $permission,
            "EXTERNAL" => $this->hie_external,
            "ORG_USER" => $this->hie_org_user,
            "ORG_PASS" => $this->hie_org_pass,
        ];

        if ($emerg_reason !== '') {
            $data['EMERG_REASON'] = $emerg_reason;
        }

        $this->data = $data;
    }

    /**
     * Getter for collected data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Genarate encrypted url
     *
     * @param Array $data
     * @return string Url with encrypted parameters
     */
    private function getUrl($data)
    {
        $params = urldecode(http_build_query($data));
        $password = $this->hie_aes_encryption_password;
        $aesStr = AesCtr::encrypt($params, $password, 256);

        return $this->hie_remote_url . "?" . $aesStr;
    }

    /**
     * @param Patient $patient
     * @param string $nhs_number
     * @return string
     * @throws Exception
     */
    public function generateHieUrl(\Patient $patient, $nhs_number)
    {
        $this->patient = $patient;
        $this->generateDataToEncryptedUrl($patient, $this->user, $nhs_number);
        return $this->getUrl($this->getData());

    }

}