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
 * This is the model class for table "ophtrconsent_signature".
 *
 * The followings are the available columns in table 'ophtrconsent_signature':
 * @property integer $id
 * @property integer $element_id
 * @property integer $type
 * @property string $signature_file_id
 * @property string $signed_user_id
 * @property string $signatory_role
 * @property string $signatory_name
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property int $initiator_element_type_id
 * @property int $initiator_row_id
 *
 * The followings are the available model relations:
 * @property Element_OphTrConsent_Esign $element
 * @property ProtectedFile $signatureFile
 * @property User $signedUser
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property ElementType $initiatorElementType
 */
class OphTrConsent_Signature extends BaseSignature
{
    /** @var string|null The signatory's id if applicable */
    public ?string $user_id = null;

    /** @var bool Whether the signature must be collected before the Consent Form is finalized */
    public bool $is_mandatory = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtrconsent_signature';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, type', 'required'),
            array('element_id, id, type, initiator_element_type_id, initiator_row_id', 'numerical', 'integerOnly'=>true),
            array('signatory_role, signatory_name', 'length', 'max'=>64),
            array('last_modified_date, created_date, date, time, type, signature_file_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, signature_file_id, signed_user_id, signatory_role, signatory_name, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'element' => array(self::BELONGS_TO, Element_OphTrConsent_Esign::class, 'element_id'),
            'signatureFile' => array(self::BELONGS_TO, ProtectedFile::class, 'signature_file_id'),
            'signedUser' => array(self::BELONGS_TO, User::class, 'signed_user_id'),
            'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'initiatorElementType' => array(self::BELONGS_TO, ElementType::class, 'initiator_element_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => "Id",
            'element_id' => 'Element',
            'signature_file_id' => 'Signature File',
            'signed_user_id' => 'Signed User',
            'signatory_role' => 'Signatory Role',
            'signatory_name' => 'Signatory Name',
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

        $criteria->compare('element_id', $this->element_id);
        $criteria->compare('signature_file_id', $this->signature_file_id, true);
        $criteria->compare('signed_user_id', $this->signed_user_id, true);
        $criteria->compare('signatory_role', $this->signatory_role, true);
        $criteria->compare('signatory_name', $this->signatory_name, true);
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
     * @return OphTrConsent_Signature the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Returns an HTML img tag containing the signature image
     * or an empty string in case of failure
     *
     * @return string
     * @throws CException
     */
    public function getSignatureImage() : string
    {
        if ($thumb = $this->signatureFile->getThumbnail("150x50")) {
            $data = file_get_contents($thumb["path"]);
            if ($data !== false) {
                $img = base64_encode($data);
                return "<img style=\"max-height:100px;\" src=\"data:{$this->signatureFile->mimetype};base64,$img\"/>";
            }
        }
        // Display nothing in case of failure
        return "";
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        if ($this->initiator_element_type_id) {
            /** @var \OEModule\OphTrConsent\models\RequiresSignature $initiator */
            $initiator = $this->element->event->getElementByClass($this->initiatorElementType->class_name);
            $initiator->afterSignedCallback($this->initiator_row_id, $this->id);
        }

        parent::afterSave();
    }
}
