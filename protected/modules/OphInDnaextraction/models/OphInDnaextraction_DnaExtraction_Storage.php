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
            array('box_id, letter, number, display_order', 'required'),
            array('number', 'numerical', 'min' => 1),
            array('box_id','availabeStorage'),
            array('letter','letterValidation'),
            array('number','numberValidation'),
            array('box_id, letter, number, display_order','safe'),
        );
    }

    /**
     * Validate the letter attribute
     * @param type $attribute
     * @param type $params
     */
    public function letterValidation($attribute, $params)
    {
        $box = new OphInDnaextraction_DnaExtraction_Box();
        $boxRanges = $box->boxMaxValues($this->box_id);

        $this->setLetterRange($boxRanges['maxletter']);

        if ( !in_array($this->letter, $this->letterRange) ) {
            $this->addError($attribute, 'This letter is larger than maximum value.');
        }
    }

    /**
     * Validate the number attribute
     * @param type $attribute
     * @param type $params
     */
    public function numberValidation($attribute, $params)
    {
        $box = new OphInDnaextraction_DnaExtraction_Box();
        $boxRanges = $box->boxMaxValues($this->box_id);

        $this->setNumberRange($boxRanges['maxnumber']);

        if ( !in_array($this->number, $this->numberRange) ) {
            $this->addError($attribute, 'This number is larger than maximum value.');
        }
    }

    /*
     * Available storage in admin
     */
    public function availabeStorage($attribute, $params)
    {
        $availabeStorage = Yii::app()->db->createCommand()
            ->select('id')
            ->from('ophindnaextraction_storage_address')
            ->where('box_id =:box_id and letter =:letter and number =:number', array(':box_id' => $this->box_id, ':letter' => $this->letter, ':number' => $this->number))
            ->queryScalar();

        if ($availabeStorage) {
            $this->addError('Box', 'These parameters are already in use.');
        }
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

    /**
     * calculate the appropriate default displayorder for this record.
     *
     * @return int
     */
    public function calculateDefaultDisplayOrder()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'display_order desc';
        $criteria->limit = 1;
        return ($order = self::model()->find($criteria)) ? $order->display_order + 1 : 1;
    }

    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'extracted_by' => array(self::BELONGS_TO, 'User', 'extracted_by_id'),
            'box' => array(self::BELONGS_TO, 'OphInDnaextraction_DnaExtraction_Box', 'box_id'),
            'extraction' => array(self::HAS_ONE, 'Element_OphInDnaextraction_DnaExtraction', 'storage_id'),
        );
    }


    protected function beforeDelete()
    {

        if ($this->extraction != null) {
            return false;
        }
        return parent::beforeDelete();
    }

    /*
     * Set letter range
     */
    protected function setLetterRange($maxletter)
    {
        $this->letterRange = range('A', $maxletter);
    }

    /*
     * Set number range
     */
    protected function setNumberRange($maxnumber)
    {
        $this->numberRange = range('1', $maxnumber);
    }

    public function generateLetterArrays($box_id, $maxletter, $maxnumber)
    {
        $this->setLetterRange($maxletter);
        $this->setNumberRange($maxnumber);

        $result = array();
        $i = 0;
        foreach ($this->letterRange as $letter) {
            foreach ($this->numberRange as $number) {
                $result[$i]['box_id'] = $box_id;
                $result[$i]['letter'] = $letter;
                $result[$i]['number'] = $number;

                $i++;
            }
        }
        return $result;
    }

    public function getAllLetterNumberToBox($boxid)
    {
         $boxes = Yii::app()->db->createCommand()
            ->select('box_id, letter, number')
            ->from('ophindnaextraction_storage_address')
            ->where('box_id =:box_id', array(':box_id' => $boxid))
            ->order('box_id ASC, letter ASC, number ASC')
            ->queryAll();

        return $boxes;
    }


    public function getAvailableCombinedList($id = null)
    {
        //if id not exits, it means event is create, so we need only available box - letter - number combinations
        if ( $id == null) {
            $getAvailableBoxes = Yii::app()->db->createCommand()
                ->select("opaddress.id, CONCAT(opbox.value,' - ',opaddress.letter,' - ',opaddress.number ) AS value")
                ->from('ophindnaextraction_storage_address opaddress')
                ->join('ophindnaextraction_dnaextraction_box opbox', 'opaddress.box_id = opbox.id')
                ->where('opaddress.id NOT IN (SELECT storage_id FROM et_ophindnaextraction_dnaextraction WHERE id IS NOT NULL) ')
                ->order('opbox.value ASC, opaddress.letter ASC, opaddress.number ASC')
                ->queryAll();
        } else {
            //We need available boxes - letters - numbers combinations and the stored row
            $getAvailableBoxes = Yii::app()->db->createCommand()
                ->select("opaddress.id, CONCAT(opbox.value,' - ',opaddress.letter,' - ',opaddress.number ) AS value")
                ->from('ophindnaextraction_storage_address opaddress')
                ->join('ophindnaextraction_dnaextraction_box opbox', 'opaddress.box_id = opbox.id')
                ->where('opaddress.id NOT IN (SELECT storage_id FROM et_ophindnaextraction_dnaextraction WHERE storage_id != '.$id.') ')
                ->order('opbox.value ASC, opaddress.letter ASC, opaddress.number ASC')
                ->queryAll();
        }

        return $getAvailableBoxes;
    }

    public function attributeLabels()
    {
        return array(
            'box.value' => 'Box name',
            'box_id'    => 'Box name',
        );
    }
}
