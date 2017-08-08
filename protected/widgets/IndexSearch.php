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
      return $this->render('IndexSearch_examination');
    }
}
