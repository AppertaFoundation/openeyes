<?php

class DefaultController extends BaseEventTypeController
{
    /**
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('Create', 'Update', 'View', 'Print', 'Delete'),
                'roles' => array('OprnEditGeneticResults'),
            ),
            array('allow',
                'actions' => array('View', 'Print'),
                'roles' => array('OprnViewGeneticResults'),
            ),
        );
    }

    public function beforeAction($action)
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.Genetics.assets.js'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath . '/gene_validation.js');

        return parent::beforeAction($action);
    }

    /**
     * @param BaseEventTypeElement $element
     * @return bool
     */
    public function isRequiredInUI(BaseEventTypeElement $element)
    {
        return true;
    }
}
