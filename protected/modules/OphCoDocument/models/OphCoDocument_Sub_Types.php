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
 * This is the model class for table "ophcodocument_sub_types".
 *
 * The followings are the available columns in table 'anaesthetic_agent':
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 * @property bool $is_active
 * @property int $sub_type_event_icon_id
 * @property int $document_id
 */
class OphCoDocument_Sub_Types extends BaseActiveRecordVersioned
{
    public $image;
    protected $max_document_size = 10485760;
    protected $allowed_file_types = array();

    public function init()
    {
        $this->allowed_file_types = Yii::app()->params['OphCoDocument_Sub_Types']['allowed_file_types'];
        $this->max_document_size = Helper::return_bytes(ini_get('upload_max_filesize'));

        parent::init();
    }

    /**
     * Returns the static model of the specified AR class.
     * @return OphCoDocument_Sub_types|BaseActiveRecord the static model class
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
        return 'ophcodocument_sub_types';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('image', 'file', 'mimeTypes' => 'image/jpeg, image/png, application/pdf', 'safe' => true, 'allowEmpty' => true, 'maxSize'=> $this->getMaxDocumentSize(false)),
            array('name, display_order , is_active, sub_type_event_icon_id, document_id', 'safe'),
            array('name, display_order , is_active, sub_type_event_icon_id', 'required'),
            array('id, name, display_order , is_active, sub_type_event_icon_id, document_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'subTypeEventIcon' => array(self::HAS_ONE, 'EventIcon', 'sub_type_event_icon_id'),
            'templateImage' => array(self::BELONGS_TO, 'ProtectedFile', 'document_id')
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the allowed file types (extensions)
     * @return array
     */
    public function getAllowedFileTypes()
    {
        return array_keys($this->allowed_file_types);
    }

    /**
     * Returns the allowed file mime types
     * @return array
     */
    public function getAllowedFileMimeTypes()
    {
        return array_values($this->allowed_file_types);
    }

    /**
     * Returns the allowed file size in MB or bytes
     * @param bool $to_mb
     * @return int
     */
    public function getMaxDocumentSize($to_mb = true)
    {
        $size = $to_mb ? (number_format($this->max_document_size / 1048576, 0)) : $this->max_document_size;
        return $size;
    }

}
