<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class NewFeatureHelp extends BaseCWidget
{
    public $splash_screen = array();
    public $tours = array();
    public $download_links = array();

    protected function loadConfig()
    {
        $config = require Yii::getPathOfAlias('application.tours') . '/common.php';
        $current_url = Yii::app()->request->requestUri;
        foreach ($config as $tour) {
            if (!isset($tour['url_pattern']) || preg_match($tour['url_pattern'], $current_url) > 0) {
                if (isset($tour['auto']) && $tour['auto'] && !$this->splash_screen) {
                    $this->splash_screen = $tour;
                } else {
                    $this->tours[] = $tour;
                }
            }
        }

    }

    public function run()
    {
        $this->loadConfig();
        $this->render('NewFeatureHelp');
    }
}
