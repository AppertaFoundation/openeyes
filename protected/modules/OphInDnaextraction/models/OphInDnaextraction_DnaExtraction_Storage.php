<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OphInDnaextraction_DnaExtraction_Storage
 *
 * @author Irvine
 */
class OphInDnaextraction_DnaExtraction_Storage extends BaseEventTypeElement
{
    var $letterRange;
    var $numberRange;
    /**
     * Returns the static model of the specified AR class.
     *
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
        return 'ophindnaextraction_storage_address';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('box_id', 'required'),
            array('letter', 'required'),
            array('number', 'required'),
            array('number', 'numerical', 'min' => 1),
            array('box_id, letter, number','availabeStorage'),
            array('box_id, letter, number','safe'),
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
    
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'extracted_by' => array(self::BELONGS_TO, 'User', 'extracted_by_id'),
            'box' => array(self::BELONGS_TO, 'OphInDnaextraction_DnaExtraction_Box', 'box_id')
        );
    }
    
    public function availabeStorage( $attribute, $params )
    {
        $availabeStorage = Yii::app()->db->createCommand()
            ->select('id')
            ->from('ophindnaextraction_storage_address')
            ->where('box_id =:box_id and letter =:letter and number =:number', array(':box_id' => $this->box_id, ':letter' => $this->letter, ':number' => $this->number))
            ->queryScalar();
        
        if((int)$availabeStorage > 0){
            $this->addError($attribute, 'This parameters are already in use.');
        }
        
        $validParams = $this->letterNumberValidation();
       
        if( $validParams !== TRUE){
            $this->addError($attribute, $validParams);
        }
    }
    
    public function setLetterRange( $maxletter )
    {
        $this->letterRange = range('A' , $maxletter);
    }
    
    public function setNumberRange( $maxnumber )
    {
        $this->numberRange = range('1' , $maxnumber);
    }
    
    protected function letterNumberValidation()
    {
        
        $boxRanges = OphInDnaextraction_DnaExtraction_Box::availableBoxes($this->box_id);
        if($boxRanges['maxletter'] == NULL){
            return 'You have not specified a maximum letter value to '.$boxRanges['value'].' box.';
        }
        if($boxRanges['maxnumber'] == NULL){
            return 'You have not specified a maximum number value to '.$boxRanges['value'].' box.';
        }
        
        $this->setLetterRange( $boxRanges['maxletter'] );
        $this->setNumberRange( $boxRanges['maxnumber'] );
       
       
        $validLetter = FALSE;
        foreach ($this->letterRange as $char) {
            if($this->letter == $char){
                $validLetter = TRUE;
            }
        }
        if($validLetter == FALSE){
            return 'This letter is larger than maximum value.';
        }
        
        $validNumber = FALSE;
        foreach ($this->numberRange as $number) {
           if($this->number == $number){
                $validNumber = TRUE;
            }
        }
        if($validNumber == FALSE){
            return 'This number is larger than maximum value.';
        }
        
        return TRUE;
    }
    
    public function getBoxLastRow( $boxid )
    {
        $boxRow = Yii::app()->db->createCommand()
            ->select('id, box_id, letter, number')
            ->from('ophindnaextraction_storage_address')
            ->where('box_id =:box_id', array(':box_id' => $boxid))
            ->order('id DESC')
            ->limit('1')
            ->queryRow();
        
        return $boxRow;
    }
    
   
    
    public function attributeLabels()
    {
        return array(
            'box.value' => 'Box name',
            'box_id'    => 'Box name',
        );
    }
}
