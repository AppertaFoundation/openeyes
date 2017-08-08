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
      //or just render
      //can use $this->event_type
      return $this->render('IndexSearch_examination');
    }
}
