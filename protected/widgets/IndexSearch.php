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
//            Fetching the index_search PHP content from the event_type table
            $criteria = new CDbCriteria();
            $criteria->select = 'index_search_content';
            $criteria->addCondition("name =:event_name");
            $criteria->params[":event_name"] = $this->event_type;
            $php_output = EventType::model()->find($criteria);
//            Capturing the contents of the index_search column into a variable
            $render_content = ($php_output['index_search_content']);
//            This evaluates the generated php and HTML code obtained from the DB
            return eval("?>$render_content");
        } catch (Exception $e) {
          //view does not exist in DB
        }
    }
}
