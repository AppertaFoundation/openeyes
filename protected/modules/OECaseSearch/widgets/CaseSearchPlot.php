<?php


class CaseSearchPlot extends BaseCWidget
{
    public array $variable_data = array();
    public array $variables = array();
    public int $total_patients = 0;
    public string $list_selector;
    public string $display;
    private string $display_theme;
    private string $newblue_path;
    private string $oePlotlyPath;

    public function init()
    {
        $this->newblue_path = Yii::getPathOfAlias('application.assets.newblue');
        $this->oePlotlyPath = Yii::app()->assetManager->getPublishedUrl($this->newblue_path, true) . '/plotlyJS/oePlotly_v1.js';

        if (isset(Yii::app()->params['image_generation']) && Yii::app()->params['image_generation']) {
            $this->display_theme = 'dark';
        } else {
            $user_theme = SettingUser::model()->find('user_id = :user_id AND `key` = "display_theme"', array(":user_id"=>Yii::app()->user->id));
            $this->display_theme = $user_theme ? (string) SettingMetadata::model()->getSetting('display_theme') : Yii::app()->params['image_generation'];
        }
    }

    /**
     * @throws CException
     */
    public function run()
    {
        /**
         * @var $assetManager AssetManager
         */
        $assetManager = Yii::app()->assetManager;
        if (!$assetManager->getClientScript()->isScriptFileRegistered($assetManager->getPublishedUrl('js/CaseSearchPlot.js', true))) {
            $assetManager->registerScriptFile('js/CaseSearchPlot.js', 'application.modules.OECaseSearch.widgets', 10);
        }
        if (!$assetManager->getClientScript()->isScriptFileRegistered($assetManager->getPublishedUrl($this->newblue_path, true)  . '/plotlyJS/oePlotly_v1.js')) {
            Yii::app()->clientScript->registerScriptFile($this->oePlotlyPath);
        }
        $this->render('CaseSearchPlot', array(
            'display_theme' => $this->display_theme,
        ));
    }
}
