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
     * Prevent starting tours even if 'auto' flag is true in the protected/tour/(common|local).php
     * @var bool
     */
    public $auto_start = true;

    /**
     * Resolves loading of the configuration
     *
     * NB does not support module based tours at the moment, given that all modules are a
     * part of the full application, and this is an entirely new piece of functionality.
     * This was also implemented with considerable time constraints, so supporting multi-level
     * tours seemed an unnecessary complication.
     */
    protected function loadConfig()
    {
        $config = array();
        foreach (array('common', 'local') as $filename) {
            $path = Yii::getPathOfAlias('application.tours') . "/{$filename}.php";
            if (file_exists($path)) {
                $config = array_merge($config, require $path);
            }
        }

        $disable_auto_feature_tours = ( null !== \SettingMetadata::model()->getSetting('disable_auto_feature_tours')) ? \SettingMetadata::model()->getSetting('disable_auto_feature_tours') : "off";
        if ($disable_auto_feature_tours === "on" || $disable_auto_feature_tours === true) {
            $this->auto_start = false;
        }

        $this->mapConfigToTours($config);
    }

    /**
     * @param $config
     */
    protected function mapConfigToTours($config)
    {
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
            if ($a['position'] === $b['position']) {
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
        if ($tour['auto']) {
            if ($user_state->completed) {
                $tour['auto'] = false;
            } elseif ($user_state->sleep_until) {
                $now = new DateTime();
                $cmp = DateTime::createFromFormat('Y-m-d H:i:s', $user_state->sleep_until);
                $tour['auto'] = $now >= $cmp;
            }
        }
    }

    public function run()
    {
        $this->loadConfig();
        $this->render('NewFeatureHelp');
    }
}
