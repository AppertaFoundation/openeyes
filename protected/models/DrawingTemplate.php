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
 * This is the model class for table "drawing_templates".
 *
 * The followings are the available columns in table 'drawing_templates':
 * @property integer $id
 * @property string $name
 * @property string $event_type_id
 * @property string $element_type_id
 * @property string $protected_file_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property ElementType $elementType
 * @property EventType $eventType
 * @property ProtectedFile $protectedFile
 */
class DrawingTemplate extends BaseActiveRecord
{
    public $image;
    protected int $max_document_size = 10485760;
    protected array $allowed_file_types = [];

    public function init()
    {
        $this->allowed_file_types = \Yii::app()->params['allowed_file_types']['DrawingTemplate'];
        $this->max_document_size = \Helper::return_bytes(ini_get('upload_max_filesize'));

        parent::init();
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable', // to use ->active()->findAll()
        );
    }


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'drawing_templates';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, protected_file_id', 'required'),
            array('name', 'length', 'max'=>50),
            array('image', 'file', 'mimeTypes' => 'image/jpeg, image/png', 'safe' => true, 'allowEmpty' => true, 'maxSize'=> $this->getMaxDocumentSize(false)),
            array('id, name, event_type_id, element_type_id, protected_file_id, display_order, active, last_modified_date, created_date, last_modified_user_id, created_user_id, image', 'safe'),
            array('id, name, event_type_id, element_type_id, protected_file_id, display_order, active, last_modified_date, created_date, last_modified_user_id, created_user_id', 'safe', 'on'=>'search'),
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
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'element_type' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
            'event_type' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'protected_file' => array(self::BELONGS_TO, 'ProtectedFile', 'protected_file_id'),
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
            'event_type_id' => 'Event Type',
            'element_type_id' => 'Element Type',
            'protected_file_id' => 'Template',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('event_type_id', $this->event_type_id, true);
        $criteria->compare('element_type_id', $this->element_type_id, true);
        $criteria->compare('protected_file_id', $this->protected_file_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DrawingTemplate the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Returns the allowed file types (extensions)
     * @return array
     */
    public function getAllowedFileTypes(): array
    {
        return array_keys($this->allowed_file_types);
    }

    /**
     * Returns the allowed file mime types
     * @return array
     */
    public function getAllowedFileMimeTypes(): array
    {
        return array_values($this->allowed_file_types);
    }

    /**
     * Returns the allowed file size in MB or bytes
     * @param bool $to_mb
     * @return int
     */
    public function getMaxDocumentSize($to_mb = true): int
    {
        $size = $to_mb ? (number_format($this->max_document_size / 1048576, 0)) : $this->max_document_size;
        return $size;
    }
}
