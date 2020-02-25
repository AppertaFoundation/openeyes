<?php
//namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class IndexSearch extends BaseCWidget
{
    public $event_type = "Examination";

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        try {
////            Fetching the index_search PHP content from the event_type table
//            $criteria = new CDbCriteria();
//            $criteria->select = 'index_search_content';
//            $criteria->addCondition("name =:event_name");
//            $criteria->params[":event_name"] = $this->event_type;
//            $php_output = EventType::model()->find($criteria);
////            Capturing the contents of the index_search column into a variable
//            $render_content = ($php_output['index_search_content']);
////            This evaluates the generated php and HTML code obtained from the DB
/*            return eval("?>$render_content");*/
              $commandPath = Yii::app()->getBasePath().DIRECTORY_SEPARATOR.'commands';
              $runner = new CConsoleCommandRunner();
              $runner->addCommands($commandPath);
              if (Yii::app()->params['index_search_examination'] == null) {
                  $args = array('yiic', 'eyedrawconfigload', '--filename=' . Yii::app()->getBasePath() . '/config/core/OE_ED_CONFIG.xml');
                  ob_start();
                  $runner->run($args);
                  Yii::log('here');
              }
              $render_content = Yii::app()->params['index_search_examination'] ;
              return eval("?>$render_content");

        } catch (Exception $e) {
          //view does not exist in DB
        }
    }
}
