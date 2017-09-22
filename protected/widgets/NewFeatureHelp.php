<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class NewFeatureHelp extends BaseCWidget
{
    public $splash_screen = array();
    public $tours = array();
    public $download_links = array();

    public function init()
    {
      parent::init();
    }

    public function run()
    {
      $this->render('NewFeatureHelp');
      $splash_screen_JSON = json_encode($this->splash_screen);
      $tours_JSON = json_encode($this->tours);
      $download_links_JSON = json_encode($this->download_links);
      echo "<script>
      var splash_screen = {$splash_screen_JSON};
      var tours = {$tours_JSON};
      var download_links = {$download_links_JSON};
      var newFeatureHelpController = new NewFeatureHelpController(splash_screen, tours, download_links);
      </script>";
    }
}
