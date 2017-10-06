<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class NewFeatureHelp extends BaseCWidget
{
    public $splash_screen = array();
    public $tours = array();
    public $download_links = array();

    /**
     * Resolves loading of the configuration
     */
    protected function loadConfig()
    {
        $config = require Yii::getPathOfAlias('application.tours') . '/common.php';
        $current_url = Yii::app()->request->requestUri;
        $user_id = Yii::app()->user->id;

        foreach ($config as $tour) {
            if (!isset($tour['url_pattern']) || preg_match($tour['url_pattern'], $current_url) > 0) {
                $this->updateTourState($tour, $user_id);
                $this->tours[] = $tour;
            }
        }

        $this->sortTours();
    }

    /**
     * Perform a sort on all the loaded tours in the widget
     */
    protected function sortTours()
    {
        usort($this->tours, function ($a, $b) {
            if($a['position'] === $b['position']){
                return strcasecmp($a['name'], $b['name']);
            }
            return $a['position'] - $b['position'];
        });
    }

    /**
     * @param $tour
     * @param $user_id
     */
    protected function updateTourState(&$tour, $user_id)
    {
        $user_state = UserFeatureTourState::model()->findOrCreate($user_id, $tour['id']);
        // don't want a tour the user has already seen to auto start
        if ($tour['auto'] && $user_state->completed) {
            $tour['auto'] = false;
        }
    }

    public function run()
    {
        $this->loadConfig();
        $this->render('NewFeatureHelp');
    }
}
