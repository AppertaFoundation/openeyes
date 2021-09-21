<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "et_ophcocvi_consentsig".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
 * @property integer $is_patient
 * @property string $signature_date
 * @property string $representative_name
 * @property integer $signature_file_id
 *
 * The followings are the available model relations:
 *
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property ProtectedFile $signature_file
 */
use \OptomPortalConnection;

class Element_OphCoCvi_ConsentSignature extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     * @return the static model class
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
        return 'et_ophcocvi_consentsig';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, is_patient, signature_date, representative_name, signature_file_id, ', 'safe'),
            //array('is_patient', 'required'),
            array(
                'id, event_id, is_patient, signature_date, representative_name, signature_file_id, ',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'signature_file' => array(self::BELONGS_TO, 'ProtectedFile', 'signature_file_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'is_patient' => 'Patient',
            'signature_date' => 'Signature date',
            'representative_name' => 'Representative name',
            'signature_file_id' => 'Signature File',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('is_patient', $this->is_patient);
        $criteria->compare('signature_date', $this->signature_date);
        $criteria->compare('representative_name', $this->representative_name);
        $criteria->compare('signature_file_id', $this->signature_file_id);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }


    protected function afterSave()
    {

        return parent::afterSave();
    }

    /**
     *  Checks if a patient signature file is already attached to the event
     */
    public function checkSignature()
    {
        return ($this->signature_file_id)?'true':false;
    }

    /**
     * @param $text
     * @param $key
     * @return string
     */
    protected function decryptSignature($text, $key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypt = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, $iv));
        if (\Yii::app()->params['no_md5_verify']) {
            return $decrypt;
        }
        return \Helper::md5Verified($decrypt);
    }

    /**
     * @return string
     */
    public function getDecryptedSignature()
    {
        if ($this->signature_file) {
            return file_get_contents($this->signature_file->getPath());
        }
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        return md5($this->event->episode->patient_id.$this->event_id.$this->event->episode->id);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function saveSignatureImageFromPortal()
    {
        try {
            $portalConnection = new OptomPortalConnection();

            if ($portalConnection) {
                $signatureData = $portalConnection->signatureSearch(
                    null,
                    \Yii::app()->moduleAPI->get('OphCoCvi')->getUniqueCodeForCviEvent($this->event)
                );
            }
        } catch (\Exception $e) {
            //pass
        }
        // add this to list all available data!
        //$signatureData = $portalConnection->signatureSearch();

        if (is_array($signatureData) && isset($signatureData["image"]) && $portalConnection) {
            $imageFile = $portalConnection->createNewSignatureImage($signatureData["image"], $this->event->episode->patient->id);
            // save successful so we can attach the signature file to the event consent signature model
            if ($imageFile) {
                $this->signature_file_id = $imageFile->id;
                return $this->save();
            }
        }
        return false;
    }

    /**
     * @return resource
     */
    public function getSignatureBox()
    {
        $QRContent = "@code:"
            . \Yii::app()->moduleAPI->get('OphCoCvi')->getUniqueCodeForCviEvent($this->event)
            . "@key:" . $this->getEncryptionKey();

        $QRHelper = new \SignatureQRCodeGenerator();
        return $QRHelper->generateQRSignatureBox($QRContent, true, array("x"=>1000,"y"=>600), 200);
    }

    /**
     * This will always return an image for use in the signature placeholder. It will first try to load the signature from the portal
     * and otherwise will return the signature capture box.
     *
     * This should be used with caution as it will not respect a signature having been deleted on the server side, but remaining on the portal.
     *
     * @TODO: re-factor so that signature retrieval is always explicit.
     * @return resource
     */
    public function loadSignatureFromPortal()
    {
        if ($this->saveSignatureImageFromPortal()) {
            $signature = imagecreatefromstring($this->getDecryptedSignature());
        } else {
            $signature = $this->getSignatureBox();
        }
        return $signature;
    }

    /**
     * Returns an associative array of the data values for printing
     */
    public function getStructuredDataForPrint()
    {
        $result = array();
        $result['patientOrRepresentative'] = array(
            array($this->is_patient ? 'X' : '',''),
            array($this->is_patient ? '' : 'X','')
        );

        $result['signatureDate'] = \Helper::convertMySQL2NHS($this->signature_date);
        $result['representativeName'] = $this->is_patient ? '' : $this->representative_name;

        return $result;
    }

    public function getContainer_form_view()
    {
        return '//patient/element_container_form_no_bin';
    }
}
