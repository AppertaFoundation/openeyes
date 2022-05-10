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
 * @property string $lowest_setting_level
 */
class SettingMetadata extends BaseActiveRecordVersioned
{
    public static array $CONTEXT_CLASSES = [
        'SettingUser' => 'user_id',
        'SettingFirm' => 'firm_id',
        'SettingInstitutionSubspecialty' => ['institution_id', 'subspecialty_id'],
        'SettingSubspecialty' => 'subspecialty_id',
        'SettingSpecialty' => 'specialty_id',
        'SettingSite' => 'site_id',
        'SettingInstitution' => 'institution_id',
        'SettingInstallation' => null
    ];

    /**
     * static cache properties for performance improvements
     */
    protected static ?bool $sessionSiteValidated = null;
    protected static ?Site $sessionSite = null;
    protected static ?bool $sessionFirmValidated = null;
    protected static ?Firm $sessionFirm = null;
    protected static array $metadataCacheStore = [];

    /**
     * Returns the static model of the specified AR class.
     *
     * @return SettingMetadata the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function resetCache()
    {
        static::$sessionSiteValidated = null;
        static::$sessionFirmValidated = null;
        static::$metadataCacheStore = [];
        Yii::app()->settingCache->flush();
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
            array('element_type_id, display_order, field_type_id, key, name, default_value', 'required'),
            array('element_type_id, display_order, field_type_id, key, name, data, default_value', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_type_id, display_order, field_type_id, key, name, data, default_value', 'safe', 'on' => 'search'),
            array('default_value,', 'filter', 'filter' => function ($param) {
                return SettingMetadata::getPurifiedValue($param);
            }),
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

    /**
     * @param $key string Setting key
     * @param $value mixed Expected setting value.
     * @return bool
     */
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

    protected function getSettingValueWithConditions($model, $key, $conditions, $element_type)
    {
        $criteria = new CDbcriteria();

        foreach ($conditions as $condition_field => $condition_value) {
            if ($condition_field && $condition_value) {
                $criteria->addCondition($condition_field . ' = :' . $condition_field);
                $criteria->params[':' . $condition_field] = $condition_value;
            }
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

    /**
     * @param string|null $key Setting key
     * @param ElementType|null $element_type Element type the setting applies to
     * @param bool|false $return_object Whether or not to return the setting value or the Setting model instance.
     * @param string[]|null $allowed_classes The allowed setting model classes to query over.
     * @return array|CList|false|mixed|string|null
     */
    public function getSetting($key = null, $element_type = null, $return_object = false, $allowed_classes = null, $institution_id = null, $is_setting_page = false)
    {
        /***
         * NOTE: This entire class (together with the other Setting* classes) needs serious refactoring
         * It is extremely over complicated and has built up a lot of technical debt over the years.
         *
         * For now, minimal changes have been made to match existing behaviour, whist incorporating caching to
         * greatly speed up page loading times.
         ***/

        // initialise all the values needed to find the setting and set the cache id
        $user_id = Yii::app()->session['user']->id ?? null;
        $firm = static::firmForCurrentSession();
        $firm_id = $firm ? $firm->id : null;
        $subspecialty_id = $firm && $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        $specialty_id = $subspecialty_id ? $firm->serviceSubspecialtyAssignment->subspecialty->specialty_id : null;
        $site = static::siteForCurrentSession();
        $site_id = $site ? $site->id : null;
        $element_type_id = $element_type ? $element_type->id : null;
        // initialize is_admin as false
        $is_admin = false;

        // if yii command reference SettingsMetadata, there won't be a user
        if (property_exists(Yii::app(), 'user') && isset(Yii::app()->user)) {
            // only on the admin system settings page and with admin role, the user can view other institution settings
            $is_admin = Yii::app()->user->checkAccess('admin');
        }

         $institution_id = $is_setting_page && $is_admin ? ($institution_id ?? null) : ($site->institution_id ?? null);

        // Gets the last combined updated time of the settings_tables and uses as a cache dependency. The cache will be invalidated if the tables have been updated
        $debounce_val = Yii::app()->settingCache->get('SettingMetaDebounce');
        if ($debounce_val === false) {
            $dependency_sql = " SELECT sha1(GROUP_CONCAT(UPDATE_TIME)) 
                                FROM   information_schema.tables
                                WHERE  TABLE_SCHEMA = DATABASE()
                                AND TABLE_NAME IN (
                                    'setting_metadata'
                                    ,'setting_installation'
                                    ,'setting_institution'
                                    ,'setting_firm'
                                    ,'setting_institution_subspecialty'
                                    ,'setting_site'
                                    ,'setting_specialty'
                                    ,'setting_subspecialty'
                                    ,'setting_user'
                                )";

            $debounce_val = Yii::app()->db->createCommand($dependency_sql)->queryScalar();
            // add a debounce of a few seconds before poling for the setting cache dependency, to avoid a DB query for every run.
            Yii::app()->settingCache->set('SettingMetaDebounce', $debounce_val, 5);
        };

        // set the last update timestamp = to the latest debounce time
        if (Yii::app()->settingCache->get('SettingMetaLastUpdate') != $debounce_val) {
            Yii::app()->settingCache->set('SettingMetaLastUpdate', $debounce_val);
        }

        // Define the key for the cache item, then atempt to retrieve that key from the cache
        $id = $key . "_e:" . $element_type_id . "_u:" . $user_id . "_f:" . $firm_id . "_ins:" . $institution_id . "_sub:" . $subspecialty_id . "_sp:" . $specialty_id . "_si:" . $site_id . "_ad:" . $is_admin . "_iset:" . $is_setting_page . "_class:" . md5(serialize($allowed_classes)) . "_obj:" . $return_object;
        $value = Yii::app()->settingCache->get($id);
        if ($value === false) {
            if (!$key) {
                $key = $this->key;
            }

            // If value is set in the config params (file config), then it always overrides anything set in the database
            if (!empty(Yii::app()->params[$key] && !$return_object && empty($element_type))) {
                return Yii::app()->params[$key];
            }

            $metadata = static::cachedMetadata($key, $element_type ? $element_type->id : null);

            if (!$metadata) {
                return false;
            }

            // only on the admin system settings page and with admin role, the user can view other institution settings
            $institution_id = $is_setting_page && $is_admin ? ($institution_id ?? null) : ($site->institution_id ?? null);
            // Loop through all he permutations until a match is found.
            foreach (static::$CONTEXT_CLASSES as $class => $field) {
                if ($allowed_classes && !in_array($class, $allowed_classes, true)) {
                    continue;
                }
                if ($field) {
                    if (getType($field) === 'array') {
                        $fields = $field;
                        $conditions = [];

                        foreach ($fields as $field) {
                            if (${$field}) {
                                $conditions[$field] = ${$field};
                            }
                        }

                        if (count($conditions) === count($fields) && $setting = $this->getSettingValueWithConditions($class, $key, $conditions, $element_type)) {
                            if ($return_object) {
                                $value =  $setting;
                                break;
                            }

                            $value =  $this->parseSetting($setting, $metadata);
                            break;
                        }
                    } elseif (${$field} && $setting = $this->getSettingValue($class, $key, $field, ${$field}, $element_type)) {
                        if ($return_object) {
                            $value =  $setting;
                            break;
                        }

                        $value =  $this->parseSetting($setting, $metadata);
                        break;
                    }
                } elseif ($setting = $this->getSettingValue($class, $key, null, null, $element_type)) {
                    if ($return_object) {
                        $value =  $setting;
                        break;
                    }

                    $value =  $this->parseSetting($setting, $metadata);
                    break;
                }
            }

            // Set to the default value if no result has been found above
            if ($value === false) {
                if ($return_object) {
                    $value = false;
                } else {
                    $value =  $metadata->default_value;
                }
            }

            // Set a dependency on the SettingMetaLastUpdate not changing (i.e, the cache is invalidated if any of the setting_* tables receives an update)
            $dependency = new CExpressionDependency("Yii::app()->settingCache->get('SettingMetaLastUpdate') == '" . Yii::app()->settingCache->get('SettingMetaLastUpdate') . "'");
            Yii::app()->settingCache->set($id, $value, 1000, $dependency);
        }
        return $value;
    }

    /**
     * @param string|null $key Setting key
     * @param string[]|null $allowed_classes The allowed setting instance classes to use.
     * @return array|CList|false|mixed|string|null
     */
    public function getSettingName($key = null, $allowed_classes = null, $institution_id = null, $is_setting_page = false)
    {
        if (!$key) {
            $key = $this->key;
        }
        $value = $this->getSetting($key, null, false, $allowed_classes, $institution_id, $is_setting_page);
        if ($value === '') {
            $value = $this->default_value;
        }

        if (($data = @unserialize($this->data)) && array_key_exists($value, $data)) {
            return $data[$value];
        }

        return $value;
    }

    /**
     * @param $setting mixed The setting instance object.
     * @param $metadata SettingMetadata
     * @return false|string
     */
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
                        $substitutions[$key]['value'] !== '' &&
                        in_array($key, $data['substitutions'], true)
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
     * @param $setting
     * @param $metadata
     * @param $value
     */
    public function setSettingValue($setting, $metadata, $value)
    {
        if (@$data = unserialize($metadata->data)) {
            $value = self::getPurifiedValue($setting->value);

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

    /**
     * @param $html string
     * @param array $substitutions
     * @return false|string
     */
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

    /**
     * @param Patient|null $patient
     * @param Event|null $event
     * @param string $exam_event_name The name of the examination event type. This is generally used for testing purposes.
     * @return array[]
     * @throws Exception
     */
    public static function getPatientSubstitutions($patient = null, $event = null, $exam_event_name = 'Examination')
    {
        $patient_contact = null;

        if (isset($patient)) {
            $patient_contact = $patient->contact;
        }

        $last_exam = null;

        if (isset($patient)) {
            $last_exam = $patient->getLatestExaminationEvent($exam_event_name);
        }

        return array(
        'patient_full_name' => array('label' => 'Patient Full Name', 'value' => $patient ? self::makeSpan($patient->getFullName()) : null),
        'patient_first_name' => array('label' => 'Patient First Name', 'value' => $patient_contact ? self::makeSpan($patient_contact->first_name) : null),
        'patient_last_name' => array('label' => 'Patient Last Name', 'value' => $patient_contact ? self::makeSpan($patient_contact->last_name) : null),
        'patient_date_of_birth' => array('label' => 'Patient Date Of Birth', 'value' => $patient ? self::makeSpan(\Helper::convertMySQL2NHS($patient->dob)) : null),
        'patient_gender' => array('label' => 'Patient Sex', 'value' => $patient ? self::makeSpan($patient->getGenderString()) : null),
        'patient_nhs_num' => array('label' => 'Patient NHS Number', 'value' => $patient ? self::makeSpan($patient->getNhs(
            $event->institution_id ?? null,
            $event->site_id ?? null
        )) : null),
        'patient_hos_num' => array('label' => 'Patient Hospital Number', 'value' => $patient ? self::makeSpan($patient->getHos(
            $event->institution_id ?? null,
            $event->site_id ?? null
        )) : null),
        'patient_last_exam_date' => array('label' => 'Patient Last Examination Date', 'value' => $last_exam ? self::makeSpan(\Helper::convertMySQL2NHS($last_exam->event_date)) : null),
        );
    }

    public static function getCorrespondenceSubstitutions($element_letter = null, $recipient_address = null)
    {
        return array(
            'to_address' => array('label' => 'Recipient Address', 'value' => $recipient_address),
            'source_address' => array('label' => 'Source Address', 'value' => isset($element_letter) && !empty($element_letter->source_address) ? self::makeSpan($element_letter->source_address) : null),
            'cc_address' => array('label' => 'CC Address', 'value' => isset($element_letter) && !empty($element_letter->cc) ? self::makeSpan($element_letter->cc) : null),
            'correspondence_date' => array('label' => 'Correspondence Date', 'value' => isset($element_letter) && !empty($element_letter->event) ? self::makeSpan(\Helper::convertMySQL2NHS($element_letter->event->event_date)) : null),
        );
    }

    /**
     * @param DocumentTarget|null $doc_target
     * @return array[]
     */
    public static function getDocumentTargetSubstitutions($doc_target = null)
    {
        return array(
        'doc_target_address' => array('label' => 'Document Target Address', 'value' => isset($doc_target) && !empty($doc_target->address) ? self::makeSpan($doc_target->address) : null),
        );
    }

    /**
     * @param string|null $recipient_address
     * @return array[]
     */
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

    /**
     * @return string[][]
     */
    public function scopes()
    {
        return array(
        'byDisplayOrder' => array('order' => 'display_order DESC, name DESC'),
        );
    }

    /**
     * Retrieve the current site from cache
     */
    protected static function siteForCurrentSession()
    {
        if (is_null(static::$sessionSiteValidated)) {
            static::$sessionSiteValidated = true;
            static::$sessionSite = Site::model()
            ->with('institution')
            ->findByPk(Yii::app()->session['selected_site_id']);
        }

        return static::$sessionSite;
    }

    /**
     * Retrieve the current firm from cache
     */
    protected static function firmForCurrentSession()
    {
        if (is_null(static::$sessionFirmValidated)) {
            static::$sessionFirm = Firm::model()
            ->with('serviceSubspecialtyAssignment.subspecialty.specialty')
            ->findByPk(Yii::app()->session['selected_firm_id']);
            static::$sessionFirmValidated = true;
        }

        return static::$sessionFirm;
    }

    /**
     * Retrieve the SettingMetadata model from cache
     */
    protected static function cachedMetadata($key, $elementTypeId = null)
    {
        $cacheKey = "{$key}.{$elementTypeId}";
        if (!array_key_exists($cacheKey, static::$metadataCacheStore)) {
            static::$metadataCacheStore[$cacheKey] = $elementTypeId
            ? self::model()->find('element_type_id=? and `key`=?', [$elementTypeId, $key])
            : self::model()->find('element_type_id is null and `key`=?', [$key]);
        }
        return static::$metadataCacheStore[$cacheKey];
    }
}
