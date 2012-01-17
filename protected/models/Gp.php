<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

/**
 * This is the model class for table "Gp".
 *
 * The followings are the available columns in table 'Gp':
 * @property string $id
 * @property string $obj_prof
 * @property string $nat_id
 * @property string $contact_id
 *
 * The followings are the available model relations:
 * @property Contact $contact
 */
class Gp extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Gp the static model class
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
		return 'Gp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('obj_prof, nat_id, contact_id', 'required'),
			array('obj_prof, nat_id', 'length', 'max'=>20),
			array('contact_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, obj_prof, nat_id, contact_id', 'safe', 'on'=>'search'),
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
			'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'obj_prof' => 'Obj Prof',
			'nat_id' => 'Nat',
			'contact_id' => 'Contact',
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
		$criteria->compare('obj_prof',$this->obj_prof,true);
		$criteria->compare('nat_id',$this->nat_id,true);
		$criteria->compare('contact_id',$this->contact_id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	protected function afterFind() {
		parent::afterFind();
		if(Yii::app()->params['use_pas'] && strtotime($this->last_modified_date) < (time() - self::PAS_CACHE_TIME)) {
			$gp_service = new GpService($this);
			$gp_service->loadFromPas();
		}
	}
	
}