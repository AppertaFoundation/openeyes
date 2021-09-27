<?php
class DefaultController extends BaseEventTypeController
{
    public function checkCreateAccess()
    {
        return $this->checkAccess('OprnCreateDA', $this->firm, $this->episode, $this->event_type);
    }

    public function checkEditAccess()
    {
        return $this->checkAccess('OprnEditDA', $this->event);
    }

    public function checkDeleteAccess()
    {
        return $this->checkAccess('OprnDeleteDA', Yii::app()->session['user'], $this->event);
    }
    public function checkRequestDeleteAccess()
    {
        return $this->checkEditAccess() && parent::checkRequestDeleteAccess();
    }

    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->showAllergyWarning();
    }
    protected function initActionCreate()
    {
        parent::initActionCreate();
        $this->showAllergyWarning();
    }

    protected function initActionView()
    {
        parent::initActionView();
        $this->showAllergyWarning();
    }

    /**
     * Set flash message for patient allergies.
     */
    protected function showAllergyWarning()
    {
        if ($this->patient->no_allergies_date) {
            Yii::app()->user->setFlash('info.allergies', $this->patient->getAllergiesString());
        } else {
            Yii::app()->user->setFlash('patient.allergies', $this->patient->getAllergiesString());
        }
    }

    /**
     * Need split event files.
     * @TODO: determine if this should be defined by controller property
     *
     * @param $action
     * @return bool
     * @throws \CHttpException
     */
    protected function beforeAction($action)
    {
        $this->jsVars['OE_MODEL_PREFIX'] = 'OEModule_OphDrPGDPSD_models_';
        $asset_path = Yii::app()->assetManager->getPublishedPathOfAlias('application.modules.OphCiExamination.assets');
        Yii::app()->clientScript->registerScriptFile("{$asset_path}/js/module.js", CClientScript::POS_END);
        Yii::app()->assetManager->registerCssFile('components/font-awesome/css/font-awesome.css', null, 10);
        if ($action->getId() === "print") {
            $newblue_path = 'application.assets.newblue';
            Yii::app()->assetManager->registerCssFile('dist/css/style_oe_print.3.css', $newblue_path, null);
        }
        return parent::beforeAction($action);
    }

    public function renderOpenElements($action, $form = null, $date = null)
    {
        if ($action === 'renderEventImage') {
            $action = 'view';
        }
        if ($action !== 'view' && $action !== 'createImage') {
            parent::renderOpenElements($action, $form, $date);

            return;
        }
        $elements = $this->getElements($action);
        $this->renderElements($elements, $action, $form, $date);
    }
}
