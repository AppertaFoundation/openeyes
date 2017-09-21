<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class FeatureHelp extends BaseCWidget
{
    public $steps = "examination";

    public function init()
    {
      parent::init();
    }

    public function run()
    {
      $this->render('FeatureHelp');
      $stepsJSON = json_encode($this->steps);
      echo "<script>
      var featureHelp = new Steps();
      var newSteps = {$stepsJSON};
      featureHelp.addSteps(newSteps);
      </script>";
    }
}
