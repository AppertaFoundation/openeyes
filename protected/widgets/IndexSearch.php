<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class IndexSearch extends BaseCWidget
{
    public $event_type = "examination";

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        try {
            return $this->render('IndexSearch_'.$this->event_type);
        } catch (Exception $e) {
          //view does not exist
        }
    }
}
