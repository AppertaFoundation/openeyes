<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "phrase_by_firm".
 *
 * The followings are the available columns in table 'phrase_by_firm':
 * @property string $id
 * @property string $name
 * @property string $phrase
 * @property string $section_id
 * @property string $display_order
 * @property string $firm_id
 *
 * The followings are the available model relations:
 * @property Firm $firm
 * @property Section $section
 */
class PhraseByFirm extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PhraseByFirm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'phrase_by_firm';
	}

	public function relevantSectionTypes()
	{
		return array('Letter');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phrase_name_id', 'validatorPhraseNameId'),
			array('section_id, display_order, firm_id', 'length', 'max'=>10),
			array('phrase, section_id, firm_id, phrase_name_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, phrase, section_id, display_order, firm_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	* @param string the name of the attribute to be validated
	* @param array options specified in the validation rule
	*/

	public function ValidatorPhraseNameId($attribute,$params)
	{
		// this phrase name id must not exist at this level (not select * from phrase_by_firm where section_id=x and firm_id=y)
		if (PhraseByFirm::model()->findByAttributes(array('section_id' => $this->section_id, 'firm_id' => $this->firm_id, 'phrase_name_id' => $this->phrase_name_id))) {
			if (!$this->id) {
				$this->addError($attribute,'That phrase name has already been overridden for this section (' . $this->section_id . ') and firm (' . $this->firm_id . ')');
			}
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
			'section' => array(self::BELONGS_TO, 'Section', 'section_id'),
			'name' => array(self::BELONGS_TO, 'PhraseName', 'phrase_name_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'phrase' => 'Phrase',
			'section_id' => 'Section',
			'display_order' => 'Display Order',
			'firm_id' => 'Firm',
			'phrase_name_id' => 'Name',
			'section_by_firm_id' => 'Section'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phrase',$this->phrase,true);
		$criteria->compare('section_id',$this->section_id,true);
		$criteria->compare('display_order',$this->display_order,true);
		$criteria->compare('firm_id',$this->firm_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Retrieves a list of phrase_name models that can be overridden by a user
	 */
	public function getOverrideableNames($sectionId, $firmId)
	{
		// we want the overrideable global phrase names plus the overrideable by-subspecialty phrase names together but then you shouldnt be able to override something that is already overridden which is why we want to subtract them
		$firm = Firm::model()->findByPk($firmId);
		$subspecialtyId = $firm->serviceSubspecialtyAssignment->subspecialty_id;

		$params[':sectionid'] = $sectionId;
		$params[':subspecialtyid'] = $subspecialtyId;
		$params[':firmid'] = $firmId;
		$sql = 'select t1.id, t1.name from (
				(
					-- set of phrase names associated with global phrases defined for the given section
					select phrase_name.id, phrase_name.name from phrase_name
					join phrase on phrase_name.id=phrase.phrase_name_id
					where phrase.section_id=:sectionid
				) union (
					-- set of phrase names associated with phrases by subspecialty defined for the given section and the subspecialty of the given firm
					select phrase_name.id, phrase_name.name from phrase_name
					join phrase_by_subspecialty on phrase_name.id=phrase_by_subspecialty.phrase_name_id
					where phrase_by_subspecialty.section_id=:sectionid
					and phrase_by_subspecialty.subspecialty_id=:subspecialtyid
				)
			) as t1 left join (
				-- set of phrase names associated with phrases by firm for the given section and firm; in short we are putting together the first two sets then subtracting this set
				select phrase_name.id, phrase_name.name from phrase_name
				join phrase_by_firm on
				phrase_name.id=phrase_by_firm.phrase_name_id
				and phrase_by_firm.firm_id=:firmid
			) as t2
			on t1.id=t2.id where t2.id is null';
		$results = PhraseName::model()->findAllBySql($sql, $params);

		return $results;
	}
}
