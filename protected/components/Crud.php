<?php

/**
 * Class Crud
 *
 * Allows generic crud options for models
 */
class Crud extends Admin
{

    /**
     * Crud constructor.
     *
     * @param BaseActiveRecord $model
     * @param BaseController   $controller
     */
    public function __construct(BaseActiveRecord $model, BaseController $controller)
    {
        parent::__construct($model, $controller);
        Yii::app()->assetManager->registerScriptFile('/js/handleButtons.js');
    }

    /**
     * @param $type
     *
     * @throws Exception
     */
    protected function audit($type, $data = null)
    {
        Audit::add('crud-'.$this->modelName, $type, $data);
    }
}