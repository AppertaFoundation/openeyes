<?php

namespace OEModule\OphCoCvi\models;

use OEModule\OphCoCvi\widgets\PatientSignatureCaptureElement;

/**
 * This is the model class for table "et_ophcocvi_patient_signature"
 *
 * The followings are the available columns in table 'et_ophcocvi_patient_signature':
 * @property string $relationship_status
 * @property int $consented_to_gp
 * @property int $consented_to_la
 * @property int $consented_to_rcop
 * @property string $consented_for
 *
 * The followings are the available model relations:
 * @property \OEModule\OphCoCvi\models\OphCoCvi_ConsentConsignee[] $consentConsignees
 */

class Element_OphCoCvi_PatientSignature extends \Element_PatientSignature implements SignatureInterface
{
    public $required_checkbox_visible = false;
    public $signatory_required = 1;

    public function getWidgetClass()
    {
        return PatientSignatureCaptureElement::class;
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophcocvi_patient_signature';
	}

    public function rules()
    {
        return array_merge(
            parent::rules(),
            array (
                array('relationship_status', 'validateRelationshipStatus'),
                array('consented_to_gp, consented_to_la, consented_to_rcop', 'validateConsent')
            )
        );
    }

    public static function isDocmanEnabled()
    {
        return isset(\Yii::app()->params["cvi_docman_delivery_enabled"]) && \Yii::app()->params["cvi_docman_delivery_enabled"];
    }

    public static function isRCOPDeliveryEnabled()
    {
        return isset(\Yii::app()->params["cvi_rcop_delivery_enabled"]) && \Yii::app()->params["cvi_rcop_delivery_enabled"];
    }

    public static function isLADeliveryEnabled()
    {
        return isset(\Yii::app()->params["cvi_la_delivery_enabled"]) && \Yii::app()->params["cvi_la_delivery_enabled"];
    }

    public function validateConsent($attribute, $params)
    {
        if($this->isSigned() && (is_null($this->$attribute) || $this->$attribute === "") ) {
            $this->addError($attribute, $this->getAttributeLabel($attribute)." must not be empty");
        }
    }

    public function validateRelationshipStatus($attribute, $params)
    {
        if($this->signatory_person == self::SIGNATORY_PERSON_PARENT && !$this->relationship_status) {
            $this->addError("relationship_status", "Relation to patient must not be empty");
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
            return array_merge(parent::relations(), array(
                'signature_file' => array(self::BELONGS_TO, 'ProtectedFile', 'protected_file_id'),
                'demographics_event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'demographics_element' => array(
                    self::HAS_ONE,
                    'OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics_V1',
                    array('id' => 'event_id'),
                    'through' => 'demographics_event'
                ),
            ));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
            return array_merge(parent::attributeLabels(), array(
                "relationship_status" => "Relationship to patient",
                "consented_for" => "Consented for",
                'consented_to_gp' => 'GP',
                'consented_to_la' => 'Local Authority',
                'consented_to_rcop' => 'Royal College of Ophthalmologists',
            ));
	}

    public function getAdditionalFields()
    {
        if($this->getWidget()->getInViewMode() && $this->signatory_person != self::SIGNATORY_PERSON_PARENT) {
            return array();
        }
        else {
            return array(
                "relationship_status"
            );
        }
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Element_OphCoCvi_PatientSignature the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getSignatoryPersonOptions()
    {
        return array(
            self::SIGNATORY_PERSON_PATIENT => 'Patient',
            self::SIGNATORY_PERSON_REPRESENTATIVE => 'Patient\'s representative',
            self::SIGNATORY_PERSON_PARENT => 'Parent/Guardian',
        );
    }

    /**
     *  Checks if a patient signature file is already attached to the event
     */
    public function checkSignature()
    {
        if ($this->protected_file_id) {
            if ( is_null($this->consented_to_gp) || is_null($this->consented_to_la) && is_null($this->consented_to_rcop)) {
                return false;
            }
            return true;
        }
        return false;
    }
    
    
     /**
     * @return string|null
     */
    public function getPatientSignature()
    {
        if($this->signature_file)
        {
            return file_get_contents ($this->signature_file->getPath());
        }

        return null;
    }
  
      /*
     * Get elements for CVI PDF
     * 
     * @return array
     */
    public function getElementsForCVIpdf()
    {
        $gpFields = $this->setGpFieldsToPDF();
        $laFields = $this->setLaFieldsToPDF();
        
        $elements = [
            'An accessible signed copy of the CVI form to the patient (or parent/guardian if the patient is a chi' => '',   //Values: "0","Off", "Yes"
            'Pages 1-5 to the patient’s local council if the patient (or parent/guardian if the patient is a chil' => ( (bool)$this->consented_to_la == true) ? "Yes" : "Off", 
            'Pages 1-5 to the patient’s GP, if the patient (or parent/guardian if the patient is a child) consent' => ( (bool)$this->consented_to_gp == true ) ? "Yes" : "Off",
            'Pages 1-6 to The Royal College of Ophthalmologists' => ( (bool)$this->consented_to_rcop == true ) ? "Yes" : "Off",                                                     //Values: "0","Off", "Yes"
            'Consent_to_GP' => (bool)$this->consented_to_gp,
            'Consent_to_Local_Council' => (bool)$this->consented_to_la,
            'Consent_to_RCO' => (bool)$this->consented_to_rcop,
            'PatientSignature' => $this->getPatientSignature(),
            'signed_by' => $this->setSignedByText(),
            
        ];
        
        return array_merge($elements, $gpFields, $laFields);
    }
    
    private function setGpFieldsToPDF()
    {
        
        if($this->consented_to_gp){
            $gpAddress = $this->demographics_element->getAddressFormatForPDF( $this->demographics_element->gp_address );
            
            return [
                'GP_name' => $this->demographics_element->gp_name,            
                'GP_Address' => $gpAddress['address1'],         
                'GP_Address_Line_2' => $gpAddress['address2'],  
                'GP_postcode_1' => $this->demographics_element->gp_postcode,      
                'GP_postcode_2' => $this->demographics_element->gp_postcode_2nd,      
                'GP_Telephone' => $this->demographics_element->gp_telephone,  
            ];
        } 
        
        return [
            'GP_name' => 'N/A',            
            'GP_Address' => 'N/A',
            'GP_Address_Line_2' => 'N/A',
            'GP_postcode_1' => 'N/A',
            'GP_postcode_2' => 'N/A',
            'GP_Telephone' => 'N/A',
        ];
    }
    
    private function setLaFieldsToPDF()
    {
        if( $this->consented_to_la ){
            $laAddress = $this->demographics_element->getAddressFormatForPDF( $this->demographics_element->la_address );
            
            return [                
                'Council_Name' => $this->demographics_element->la_name,      
                'Council_Address' => $laAddress['address1'],      
                'Council_Address2' => $laAddress['address2'],      
                'Council_Postcode1' => $this->demographics_element->la_postcode,       
                'Council_Postcode2' => $this->demographics_element->la_postcode_2nd,      
                'Council_Telephone' => $this->demographics_element->la_telephone, 
            ];
        }
        
        return [
            'Council_Name' => 'N/A',            
            'Council_Address' => 'N/A',
            'Council_Address2' => 'N/A',
            'Council_Postcode1' => 'N/A',
            'Council_Postcode2' => 'N/A',
            'Council_Telephone' => 'N/A',
        ]; 
    }
    
    /**
     * 
     * @return string
     */
    private function setSignedByText()
    {   
        $result = '';
        
        switch($this->signatory_person){
            case '1':
                $result = "Patient\n".$this->demographics_element->getCompleteName()."\n";
                break;
            case '2':
                $result = "Patient's representative\n";
                
                if($this->signatory_name){
                    $result .= $this->signatory_name."\n";
                }
                break;
            case '5':
                $result = "Parent/Guardian";
                
                if($this->relationship_status){
                    $result .= " (".$this->relationship_status.")";
                }
                
                $result .= "\n";
                
                if($this->signatory_name){
                    $result .= $this->signatory_name."\n";
                } 

                break;
        }
        
        if( $this->signature_date ){
            $time = strtotime($this->signature_date);
            if($time) {
                $result .= "Date: ".date("j M Y, H:i", $time);
            } 
        }

        return $result;
    }

    public function getConsented_for()
    {
        $items = array();
        foreach (array("gp", "la", "rcop") as $item) {
            if($this->{"consented_to_$item"}) {
                $items[] = $this->getAttributeLabel("consented_to_$item");
            }
        }

        return empty($items) ? "-" : implode(", ", $items);
    }
}
