<?php

namespace OEModule\OphCiExamination\models;

class Element_OphCiExamination_OptomComments extends \BaseEventTypeElement
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_optom_comments';
    }
}
