<?php

class CaseSearchController extends BaseModuleController
{
    /**
     * @var null|Trial $trialContext
     */
    public $trialContext;

    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + addParameter',
            'ajaxOnly + clear',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'addParameter', 'clear'),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Primary case search action.
     * @param $trial_id integer The Trial that this case search is in context of
     */
    public function actionIndex($trial_id = null)
    {
        $valid = true;
        $parameters = array();
        $auditValues = array();
        $fixedParameters = $this->module->getFixedParams();
        $ids = array();
        if (isset($_SESSION['last_search'])) {
            $ids = $_SESSION['last_search'];
        }

        $this->trialContext = null;
        if ($trial_id !== null) {
            $this->trialContext = Trial::model()->findByPk($trial_id);
        }

        $criteria = new CDbCriteria();

        foreach ($this->module->getConfigParam('parameters') as $group) {
            foreach ($group as $parameter) {
                $paramName = $parameter . 'Parameter';
                if (isset($_POST[$paramName])) {
                    foreach ($_POST[$paramName] as $id => $param) {
                        $newParam = new $paramName;
                        $newParam->attributes = $_POST[$paramName][$id];
                        if (!$newParam->validate()) {
                            $valid = false;
                        }
                        $parameters[$id] = $newParam;
                    }
                }
            }
        }

        foreach ($fixedParameters as $parameter) {
            if (isset($_POST[get_class($parameter)])) {
                foreach ($_POST[get_class($parameter)] as $id => $param) {
                    $parameter->attributes = $_POST[get_class($parameter)][$id];
                    if (!$parameter->validate()) {
                        $valid = false;
                    }
                }
            }
        }

        // This can always run as there will always be at least 1 fixed parameter included in the search. Just as long as it is valid!
        if ($valid && !empty($parameters)) {
            $mergedParams = array_merge($parameters, $fixedParameters);
            $this->actionClear();
            /**
             * @var $searchProvider SearchProvider
             */
            $searchProvider = $this->module->getSearchProvider('mysql');
            $results = $searchProvider->search($mergedParams);

            if (count($results) === 0) {
                /**
                 * @var $param CaseSearchParameter
                 */
                foreach ($mergedParams as $param) {
                    $auditValues[] = $param->getAuditData();
                }
                Audit::add('case-search', 'search-results', implode(' AND ', $auditValues) . '. No results', null, array('module' => 'OECaseSearch'));
            }

            $ids = array();

            // deconstruct the results list into a single array of primary keys.
            foreach ($results as $result) {
                $ids[] = $result['id'];
            }

            // Only copy to the $_SESSION array if it isn't already there - Shallow copy is done at the start if it is already set.
            if (!isset($_SESSION['last_search']) || empty($_SESSION['last_search'])) {
                $_SESSION['last_search'] = $ids;
            }
        }

        // If there are no IDs found, pass -1 as the value (as this will not match with anything).
        $criteria->compare('t.id', empty($ids) ? -1 : $ids);
        $criteria->with = 'contact';
        $criteria->order = 'last_name, first_name';
        $criteria->compare('t.deleted', 0);

        // A data provider is used here to allow faster search times. Results are iterated through using the data provider's pagination functionality and the CListView widget's pager.
        $patientData = new CActiveDataProvider('Patient', array(
            'criteria' => $criteria,
            'totalItemCount' => count($ids),
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));

        // Get the list of parameter types for display on-screen.
        $paramList = $this->module->getParamList();

        $this->render('index', array(
            'paramList' => $paramList,
            'params' => $parameters,
            'fixedParams' => $fixedParameters,
            'patients' => $patientData,
        ));
    }

    /**
     * Add a parameter to the case search. This is executed through an AJAX request.
     */
    public function actionAddParameter()
    {
        $id = $_GET['id'];
        $param = $_GET['param'];
        $parameter = new $param;
        $parameter->id = $id;

        $this->renderPartial('parameter_form', array(
            'model' => $parameter,
            'id' => $id,
        ));
    }

    /**
     * Clear the parameters and search results. This is executed through an AJAX request
     */
    public function actionClear()
    {
        unset($_SESSION['last_search']);
    }

    public function beforeAction($action)
    {
        $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OECaseSearch.assets'));
        Yii::app()->clientScript->registerCssFile($assetPath . '/css/module.css');

        return parent::beforeAction($action);
    }
}