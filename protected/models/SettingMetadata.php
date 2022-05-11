<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2018
 * (C) Apperta Foundation, 2019-2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "setting_metadata".
 *
 * The followings are the available columns in table 'setting_metadata':
 *
 * @property string $id
 * @property string $element_type_id
 * @property string $display_order
 * @property string $field_type_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $default_value
 */
class SettingMetadata extends BaseActiveRecordVersioned
{
    public static $CONTEXT_CLASSES = [
        'SettingUser' => 'user_id',
        'SettingFirm' => 'firm_id',
        'SettingSubspecialty' => 'subspecialty_id',
        'SettingSpecialty' => 'specialty_id',
        'SettingSite' => 'site_id',
        'SettingInstitution' => 'institution_id',
        'SettingInstallation' => null
    ];

    /**
     * Returns the static model of the specified AR class.
     *
     * @return SettingMetadata the static model class
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
        return 'setting_metadata';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_type_id, display_order, field_type_id, key, name', 'required'),
            array('element_type_id, display_order, field_type_id, key, name, data, default_value', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_type_id, display_order, field_type_id, key, name, data, default_value', 'safe', 'on' => 'search'),
            array('default_value,', 'filter', 'filter' => function($param) { return SettingMetadata::getPurifiedValue($param); }),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_type' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
            'field_type' => array(self::BELONGS_TO, 'SettingFieldType', 'field_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public static function checkSetting($key, $value)
    {
        $setting_value = self::model()->findByAttributes(['key' => $key])->getSettingName();
        if (is_string($setting_value)) {
            $setting_value = strtolower($setting_value);
        }

        if (!empty(Yii::app()->params[$key])) {
            $setting_value = strtolower(Yii::app()->params[$key]);
        }
        return $setting_value === $value;
    }

    protected function getSettingValue($model, $key, $condition_field, $condition_value, $element_type)
    {
        $criteria = new CDbcriteria();

        if ($condition_field && $condition_value) {
            $criteria->addCondition($condition_field . ' = :' . $condition_field);
            $criteria->params[':' . $condition_field] = $condition_value;
        }

        $criteria->addCondition('`key`=:key');
        $criteria->params[':key'] = $key;

        if ($element_type) {
            $criteria->addCondition('element_type_id=:eti');
            $criteria->params[':eti'] = $element_type->id;
        } else {
            $criteria->addCondition('element_type_id is null');
        }

        return $model::model()->find($criteria);
    }

    public function getSetting($key = null, $element_type = null, $return_object = false, $allowed_classes = null)
    {
        if (!$key) {
            $key = $this->key;
        }

        // If value is set in the config params (file config), then it always overrides anything set in the database
        if (!empty(Yii::app()->params[$key] && !$return_object && empty($element_type))) {
            return Yii::app()->params[$key];
        }

        if ($element_type) {
            $metadata = self::model()->find('element_type_id=? and `key`=?', array($element_type->id, $key));
        } else {
            $metadata = self::model()->find('element_type_id is null and `key`=?', array($key));
        }

        if (!$metadata) {
            return false;
        }

        $user_id = Yii::app()->session['user'] ? Yii::app()->session['user']->id : null;
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $firm_id = $firm ? $firm->id : null;
        $subspecialty_id = $firm ? $firm->subspecialtyID : null;
        $specialty_id = $firm && $firm->specialty ? $firm->specialty->id : null;
        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
        $site_id = $site ? $site->id : null;
        $institution_id = $site ? $site->institution_id : null;

        foreach (static::$CONTEXT_CLASSES as $class => $field) {
            if ($allowed_classes && !in_array($class, $allowed_classes)) {
                continue;
            }
            if ($field) {
                if (${$field}) {
                    if ($setting = $this->getSettingValue($class, $key, $field, ${$field}, $element_type)) {
                        if ($return_object) {
                            return $setting;
                        }

                        return $this->parseSetting($setting, $metadata);
                    }
                }
            } else {
                if ($setting = $this->getSettingValue($class, $key, null, null, $element_type)) {
                    if ($return_object) {
                        return $setting;
                    }

                    return $this->parseSetting($setting, $metadata);
                }
            }
        }

        if ($return_object) {
            return false;
        }

        return $metadata->default_value;
    }

    public function getSettingName($key = null, $allowed_classes = null)
    {
        if (!$key) {
            $key = $this->key;
        }

        $value = $this->getSetting($key, null, false, $allowed_classes);

        if ($value == '') {
            $value = $this->default_value;
        }

        if ($data = @unserialize($this->data)) {
            if (key_exists($value, $data)) {
                return $data[$value];
            }
        }

        return $value;
    }

    public function parseSetting($setting, $metadata)
    {
        if (@$data = unserialize($metadata->data)) {
            if (isset($data['model'])) {
                $model = $data['model'];

                return $model::model()->findByPk($setting->value);
            }

            //remove following block and apply manually per event
            if (isset($data['substitutions'])) {
                $substitutions = self::getSessionSubstitutions();

                //todo: pull into own function where substitutions can be passed in as an argument

                $dom = new DOMDocument();
                @$dom->loadHTML($setting->value, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
                $xpath = new DOMXPath($dom);
                $nodes = $xpath->query("//span[@data-substitution]");

                foreach ($nodes as $node) {
                    $key = $node->getAttribute('data-substitution');

                    if (
                        array_key_exists($key, $substitutions) &&
                        isset($substitutions[$key]['value']) &&
                        $substitutions[$key]['value'] != ''
                    ) {
                        $sub = $substitutions[$key]['value'];
                    } else {
                        $sub = '<span>[' . $key . ']</span>';
                    }

                    self::substituteNode($dom, $sub, $node);
                }

                return $dom->saveHTML();
            }
        }

        return $setting->value;
    }

    /**
     * Sets a setting's value after performing some processing operations such as stripping values of HTML substitutions
     */
    public function setSettingValue($setting, $metadata, $value)
    {
        if (@$data = unserialize($metadata->data)) {
            $value = self::getPurifiedValue($value);

            if ($metadata->field_type->name === 'HTML') {
                $value = $this->stripSubstitutions($value);
            }
        }

        $setting->value = $value;
    }

    public static function getPurifiedValue($value)
    {
        $purifier_options = HTMLPurifier_Config::createDefault();
        $purifier_options->set('Cache.SerializerPath', Yii::app()->getRuntimePath());

        $po_html = $purifier_options->getHTMLDefinition(true);
        $po_html->addAttribute('span', 'data-substitution', 'CDATA');
        $po_html->addAttribute('span', 'contenteditable', 'Enum#true,false');

        $purifier = new \HTMLPurifier($purifier_options);

        return $purifier->purify($value);
    }

    public function stripSubstitutions($value)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($value, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//span[@data-substitution]");

        foreach ($nodes as $node) {
            $key = $node->getAttribute('data-substitution');

            // Strip substitution contents to avoid saving sensitive information in the settings value
            $sub = '<span>[' . $key . ']</span>';

            self::substituteNode($dom, $sub, $node);
        }

        return $dom->saveHTML();
    }

    public static function performSubstitutions($html, $substitutions = array())
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//span[@data-substitution]");

        foreach ($nodes as $node) {
            $key = $node->getAttribute('data-substitution');

            if (
                array_key_exists($key, $substitutions) &&
                isset($substitutions[$key]['value']) &&
                !empty($substitutions[$key]['value'])
            ) {
                $sub = $substitutions[$key]['value'];
            } else {
                $sub = '<span>[' . $key . ']</span>';
            }

            self::substituteNode($dom, $sub, $node);
        }

        return $dom->saveHTML();
    }

    private static function substituteNode($dom, $sub, $node)
    {
        while ($node->hasChildNodes()) {
            $node->removeChild($node->firstChild);
        }

        $sub_dom = new DomDocument();
        @$sub_dom->loadHTML($sub, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $sub_node = $dom->importNode($sub_dom->documentElement, true);

        $node->appendChild($sub_node);
    }

    /**
     * Returns an array describing the substitutions to perform in a HTML setting with the 'substitutions' data key set
     * This will return every possible HTML substitution inferrable from session data
     * @return array
     */
    public static function getSessionSubstitutions()
    {
        $session = Yii::app()->session;

        $user = isset($session) ? $session['user'] : null;
        $firm = isset($user) ? Firm::model()->findByPk($user->last_firm_id) : null;
        $site = isset($user) ? Site::model()->findByPk($user->last_site_id) : null;

        $site_contact = isset($site) ? $site->contact : null;
        $site_address = isset($site_contact) ? $site_contact->address : null;
        $institution = isset($site) ? $site->institution : null;

        $logo_helper = new LogoHelper();
        $logos = $logo_helper->getLogoURLs(isset($site) ? $site->id : null);

        return array(
            'user_name' => array('label' => 'User Name', 'value' => $user ? self::makeSpan($user->getFullName()) : null),
            'user_title' => array('label' => 'User Title', 'value' => $user->contact ? self::makeSpan($user->contact->title) : null),
            'firm_name' => array('label' => 'Firm Name', 'value' => $firm ? self::makeSpan($firm->name) : null),
            'site_name' => array('label' => 'Site Name', 'value' => $site ? self::makeSpan($site->name) : null),
            'site_address' => array('label' => 'Site Address', 'value' => $site_address ? self::makeSpan($site_address->getSummary()) : null),
            'site_phone' => array('label' => 'Site Phone', 'value' => isset($site) && !empty($site->telephone) ? self::makeSpan($site->telephone) : null),
            'site_fax' => array('label' => 'Site Fax', 'value' => isset($site) && !empty($site->fax) ? self::makeSpan($site->fax) : null),
            'site_email' => array('label' => 'Site Email', 'value' => !empty($site_address->email) ? self::makeSpan($site_address->email) : null),
            'site_city' => array('label' => 'Site City', 'value' => !empty($site_address->city) ? self::makeSpan($site_address->city) : null),
            'site_postcode' => array('label' => 'Site Postcode', 'value' => $site_address ? self::makeSpan($site_address->postcode) : null),
            'primary_logo' => array('label' => 'Primary Logo', 'value' => isset($logos['primaryLogo']) ? self::makeImg($logos['primaryLogo']) : null),
            'secondary_logo' => array('label' => 'Secondary Logo', 'value' => isset($logos['secondaryLogo']) ? self::makeImg($logos['secondaryLogo']) : null),
            'current_date' => array('label' => 'Current Date', 'value' => self::makeSpan(date('d M Y'))),
        );
    }

    public static function getPatientSubstitutions($patient = null)
    {
        $patient_contact = null;

        if (isset($patient)) {
            $patient_contact = $patient->contact;
        }

        $last_exam = null;

        if (isset($patient)) {
            $last_exam = $patient->getLatestExaminationEvent();
        }

        return array(
            'patient_full_name' => array('label' => 'Patient Full Name', 'value' => $patient ? self::makeSpan($patient->getFullName()) : null),
            'patient_first_name' => array('label' => 'Patient First Name', 'value' => $patient_contact ? self::makeSpan($patient_contact->first_name) : null),
            'patient_last_name' => array('label' => 'Patient Last Name', 'value' => $patient_contact ? self::makeSpan($patient_contact->last_name) : null),
            'patient_date_of_birth' => array('label' => 'Patient Date Of Birth', 'value' => $patient ? self::makeSpan(\Helper::convertMySQL2NHS($patient->dob)) : null),
            'patient_gender' => array('label' => 'Patient Gender', 'value' => $patient ? self::makeSpan($patient->getGenderString()) : null),
            'patient_nhs_num' => array('label' => 'Patient NHS Number', 'value' => $patient ? self::makeSpan($patient->nhs_num) : null),
            'patient_hos_num' => array('label' => 'Patient Hospital Number', 'value' => $patient ? self::makeSpan($patient->hos_num) : null),
            'patient_last_exam_date' => array('label' => 'Patient Last Examination Date', 'value' => $last_exam ? self::makeSpan(\Helper::convertMySQL2NHS($last_exam->event_date)) : null),
        );
    }

    public static function getCorrespondenceSubstitutions($element_letter = null, $recipient_address = null)
    {
        return array(
            'to_address' => array('label' => 'Recipient Address', 'value' => $recipient_address),
            'correspondence_date' => array('label' => 'Correspondence Date', 'value' => isset($element_letter) && !empty($element_letter->event) ? self::makeSpan(\Helper::convertMySQL2NHS($element_letter->event->event_date)) : null),
        );
    }

    public static function getDocumentTargetSubstitutions($doc_target = null)
    {
        return array(
            'doc_target_address' => array('label' => 'Document Target Address', 'value' => isset($doc_target) && !empty($doc_target->address) ? self::makeSpan($doc_target->address) : null),
        );
    }

    public static function getRecipientAddressSubstitution($recipient_address = null)
    {
        return array(
            'recipient_address' => array('label' => 'Recipient Address', 'value' => isset($recipient_address) && !empty($recipient_address) ? self::makeSpan($recipient_address) : null),
        );
    }

    private static function makeSpan($text)
    {
        return '<span>' . $text . '</span>';
    }

    private static function makeImg($src)
    {
        return '<img style="width:100%; display: block;" src="' . $src . '">';
    }

    public function scopes()
    {
        return array(
            'byDisplayOrder' => array('order' => 'display_order DESC, name DESC'),
        );
    }
}
