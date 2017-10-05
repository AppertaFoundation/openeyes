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
        $user_id = Yii::app()->user->id;
        foreach ($config as $tour) {
            if (!isset($tour['url_pattern']) || preg_match($tour['url_pattern'], $current_url) > 0) {
                $this->updateTourState($tour, $user_id);
                if (isset($tour['auto']) && $tour['auto'] && !$this->splash_screen) {
                    $this->splash_screen = $tour;
                } else {
                    $this->tours[] = $tour;
                }
            }
        }

    }

    /**
     * @param $tour
     * @param $user_id
     */
    protected function updateTourState(&$tour, $user_id)
    {
        $user_state = UserFeatureTourState::model()->findOrCreate($user_id, $tour['id']);
        $tour['completed'] = $user_state->completed;
    }

    public function run()
    {
        $this->loadConfig();
        $this->render('NewFeatureHelp');
    }
}
