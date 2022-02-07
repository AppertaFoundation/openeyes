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

/**
 * The followings are the available columns in table '':.
 *
 * @property string $id
 * @property int $event_id
 * @property bool $use_nickname
 * @property string $body
 * @property int $subspecialty_id
 * @property int $firm_id
 * @property int $site_id
 *
 * The followings are the available model relations:
 * @property Event $event
 * @property Subspecialty $subspecialty
 */
class LetterMacro extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION | ReferenceData::LEVEL_SITE | ReferenceData::LEVEL_SUBSPECIALTY | ReferenceData::LEVEL_FIRM;
    }

    protected function mappingColumn(int $level): string
    {
        return 'letter_macro_id';
    }

    // temp field for validation purpose
    public $levels = array();

    // turning on the options will automatically handle the relationships
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     * @return LetterMacro|BaseActiveRecord the static model class
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
        return 'ophcocorrespondence_letter_macro';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, recipient_id, use_nickname, body, cc_patient, cc_doctor, display_order, cc_optometrist,  cc_drss, episode_status_id, letter_type_id', 'safe'),
            // safe relationships for auto update relations
            array('institutions, sites, subspecialties, firms', 'safe'),
            array('name, use_nickname, body, cc_patient, cc_doctor', 'required'),
            array('levels', 'validateLevels'),
            array('episode_status_id', 'default', 'setOnEmpty' => true, 'value' => null),
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
            'institutions' => array(self::MANY_MANY, 'Institution', 'ophcocorrespondence_letter_macro_institution(letter_macro_id,institution_id)'),
            'sites' => array(self::MANY_MANY, 'Site', 'ophcocorrespondence_letter_macro_site(letter_macro_id,site_id)'),
            'subspecialties' => array(self::MANY_MANY, 'Subspecialty', 'ophcocorrespondence_letter_macro_subspecialty(letter_macro_id,subspecialty_id)'),
            'firms' => array(self::MANY_MANY, 'Firm', 'ophcocorrespondence_letter_macro_firm(letter_macro_id,firm_id)'),
            'episode_status' => array(self::BELONGS_TO, 'EpisodeStatus', 'episode_status_id'),
            'recipient' => array(self::BELONGS_TO, 'LetterRecipient', 'recipient_id'),
            'letter_type' => array(self::BELONGS_TO, 'LetterType', 'letter_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'use_nickname' => 'Use nickname',
            'cc_patient' => 'CC patient',
            'cc_doctor' => 'CC doctor',
            'cc_drss' => 'CC DRSS',
            'cc_optometrist' => 'CC Optometrist',
            'site_id' => 'Site',
            'subspecialty_id' => 'Subspecialty',
            'firm_id' => Firm::contextLabel(),
            'episode_status_id' => 'Episode status',
            'recipient_id' => 'Default recipient',
            'letter_type_id' => 'Letter Type'
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
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function validateLevels($attr, $params)
    {
        foreach ($this->{$attr} as $level => $val) {
            if ($val) {
                return true;
            }
        }

        $this->addError($attr, 'Institution, Site, Subspecialty, Firm - At least one entry is needed');
    }

    // assign values to the relationships
    public function beforeSave()
    {
        $this->deleteMappings(ReferenceData::LEVEL_INSTITUTION);
        $this->deleteMappings(ReferenceData::LEVEL_SITE);
        $this->deleteMappings(ReferenceData::LEVEL_SUBSPECIALTY);
        $this->deleteMappings(ReferenceData::LEVEL_FIRM);
        foreach ($this->levels as $level => $vals) {
            $instances = array();
            switch ($level) {
                case 'institutions':
                    $this->createMappings(ReferenceData::LEVEL_INSTITUTION, $vals);
                    break;
                case 'sites':
                    $this->createMappings(ReferenceData::LEVEL_SITE, $vals);
                    break;
                case 'subspecialties':
                    $this->createMappings(ReferenceData::LEVEL_SUBSPECIALTY, $vals);
                    break;
                case 'firms':
                    $this->createMappings(ReferenceData::LEVEL_FIRM, $vals);
                    break;
            }
        }
        return parent::beforeSave();
    }

    public function afterSave()
    {
        if (isset(
            $_POST['OEModule_OphCoCorrespondence_models_MacroInitAssociatedContent'],
            $_POST['OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod']
        )) {
            $post_associated_content = $_POST['OEModule_OphCoCorrespondence_models_MacroInitAssociatedContent'];
            $post_init_method = $_POST['OEModule_OphCoCorrespondence_models_OphcorrespondenceInitMethod'];

            $order = 1;
            foreach ($post_associated_content as $key => $pac) {
                if (isset($pac['id']) && ($pac['id'] > 0)) {
                    $criteria = new CDbCriteria();
                    $criteria->addCondition('id = ' . $pac['id']);
                    $criteria->addCondition('macro_id = ' . $this->id);
                    $associated_content = MacroInitAssociatedContent::model()->find($criteria);

                    $method = 'update';
                } else {
                    $associated_content = new MacroInitAssociatedContent();
                    $method = 'save';
                }

                $associated_content->macro_id = $this->id;
                $associated_content->is_system_hidden = (isset($pac['is_system_hidden']) ? 1 : 0);
                $associated_content->is_print_appended = (isset($pac['is_print_appended']) ? 1 : 0);
                $associated_content->init_method_id = $post_init_method[$key]['method_id'];
                $associated_content->short_code = $post_init_method[$key]['short_code'];
                $associated_content->display_order = $order;
                $associated_content->display_title = $post_init_method[$key]['title'];

                $associated_content->{$method}();
                $order++;
            }
        }

        if (isset($_POST['delete_associated'])) {
            foreach ($_POST['delete_associated'] as $key => $da) {
                if ($da['delete'] > 0) {
                    $criteria = new CDbCriteria();
                    $criteria->addCondition('id = ' . $da['delete']);
                    $criteria->addCondition('macro_id = ' . $this->id);
                    MacroInitAssociatedContent::model()->deleteAll($criteria);
                }
            }
        }
    }

    /**
     * @param $patient
     * @throws Exception
     */
    public function substitute($patient)
    {
        $this->body = OphCoCorrespondence_Substitution::replace($this->body, $patient);
    }
}
