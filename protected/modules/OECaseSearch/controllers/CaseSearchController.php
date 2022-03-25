<?php

/**
 * Class CaseSearchController
 *
 * @property null|Trial $trialContext
 */
class CaseSearchController extends BaseModuleController
{
    public $trialContext;
    protected $parameters = array();
    protected $parameterList = array();

    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + addParameter',
            'ajaxOnly + getSearchesByUser',
            'ajaxOnly + otherSearchUsers',
            'ajaxOnly + loadSearch',
            'ajaxOnly + deleteSearch',
            'ajaxOnly + clear',
            'ajaxOnly + searchCommonItems',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                    'addParameter',
                    'getSearchesByUser',
                    'loadSearch',
                    'saveSearch',
                    'deleteSearch',
                    'clear',
                    'getOptions',
                    'searchCommonItems',
                    'getDrilldownList',
                    'downloadCSV',
                    'renderPopups',
                    'lookedAtPopup',
                ),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Primary case search action.
     * @param $trial_id int The Trial that this case search is in context of
     * @throws Exception
     */
    public function actionIndex($trial_id = null)
    {
        $variables = array();
        $variable_data = array();
        $show_all_dates = false;
        $auditValues = array();

        $ids = array();
        $from = null;
        $to = null;
        $pagination = array(
            'pageSize' => 10,
        );

        if (isset($_SESSION['last_search'])) {
            $ids = $_SESSION['last_search'];
        }

        $this->trialContext = null;
        if ($trial_id !== null) {
            $this->trialContext = Trial::model()->findByPk($trial_id);
        }

        $criteria = new CDbCriteria();
        $valid = $this->populateParams(true);

        if ($valid && !empty($this->parameters)) {
            $this->actionClear();
            $ids = array_column(Yii::app()->searchProvider->search($this->parameters), 'id');

            foreach ($this->parameters as $param) {
                $auditValues[] = $param->getAuditData();
            }
            if (count($ids) === 0) {
                Audit::add('case-search', 'search-results', implode(' AND ', $auditValues) . '. Returned no results', null, array('module' => 'OECaseSearch'));
            } else {
                Audit::add('case-search', 'search-results', implode(' AND ', $auditValues) . '. Returned '. count($ids) . ' results', null, array('module' => 'OECaseSearch'));
            }

            // Only copy to the $_SESSION array if it isn't already there - Shallow copy is done at the start if it is already set.
            if (!isset($_SESSION['last_search']) || empty($_SESSION['last_search'])) {
                $_SESSION['last_search'] = $ids;
            }
            $_SESSION['last_search_params'] = $this->parameters;
            $pagination['currentPage'] = 0;
        }
        // If there are no IDs found, pass -1 as the value (as this will not match with anything).
        $criteria->compare('t.id', empty($ids) ? -1 : $ids);
        $criteria->with = 'contact';
        $criteria->compare('t.deleted', 0);

        // A data provider is used here to allow faster search times. Results are iterated through using the data provider's pagination functionality and the CListView widget's pager.
        $patientData = new CActiveDataProvider('Patient', array(
            'criteria' => $criteria,
            'totalItemCount' => count($ids),
            'pagination' => $pagination,
            'sort' => array(
                'attributes' => array(
                    'last_name' => array(
                        'asc' => 'last_name',
                        'desc' => 'last_name DESC',
                        'label' => 'Surname',
                    ),
                    'first_name' => array(
                        'asc' => 'first_name',
                        'desc' => 'first_name DESC',
                        'label' => 'First name',
                    ),
                    'age' => array(
                        'asc' => 'TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))',
                        'desc' => 'TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) DESC',
                        'label' => 'Age',
                    ),
                    'gender' => array(
                        'asc' => 'gender',
                        'desc' => 'gender DESC',
                        'label' => 'Gender',
                    ),
                    'hos_num'=> array(
                        'asc' => 'hos_num*1',
                        'desc' => 'hos_num*1 DESC',
                        'label' => \SettingMetadata::model()->getSetting('hos_num_label'),
                    ),
                    'nhs_num' => array(
                        'asc' => 'nhs_num',
                        'desc' => 'nhs_num DESC',
                        'label' => \SettingMetadata::model()->getSetting('nhs_num_label'),
                    ),
                ),
                'defaultOrder' => array(
                    'last_name' => CSort::SORT_ASC,
                ),
            ),
        ));

        $all_searches = SavedSearch::model()->findAll();

        if (!empty($ids)) {
            foreach (Yii::app()->params['CaseSearch']['variables']['OECaseSearch'] as $var) {
                $variables[] = $this->getVariableInstance($var, $ids);
            }
            if (!isset($_POST['show-all-dates']) || $_POST['show-all-dates'] !== '1') {
                if (isset($_POST['from_date']) && $_POST['from_date']) {
                    $from = new DateTime($_POST['from_date']);
                }
                if (isset($_POST['to_date']) && $_POST['to_date']) {
                    $to = new DateTime($_POST['to_date']);
                }
            } else {
                $from = null;
                $to = null;
                $show_all_dates = true;
            }
            $variable_data = Yii::app()->searchProvider->getVariableData($variables, $from, $to);
        }

        if (!array_key_exists('from_date', $_POST) && !array_key_exists('to_date', $_POST)) {
            // First entry to screen, so mark show_all_dates as true.
            $show_all_dates = true;
        }

        // Get the list of parameter types for display on-screen.
        $paramList = $this->module->getParamList();
        $variableList = $this->module->getVariableList();
        if (isset($_SESSION['last_search_params']) && !empty($_SESSION['last_search_params'])) {
            foreach ($_SESSION['last_search_params'] as $key => $last_search_param) {
                $last_search_param_name = get_class($last_search_param);
                if (!in_array($last_search_param_name, $this->parameterList, true)) {
                    unset($_SESSION['last_search_params'][$key]);
                }
            }
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('patient_drill_down_list', array(
                'patients' => $patientData,
                'display_class' => 'oe-search-results',
                'display' => true,
            ));
            Yii::app()->end();
        }

        $this->render('index', array(
            'paramList' => $paramList,
            'params' => (empty($this->parameters) && isset($_SESSION['last_search_params'])) ? $_SESSION['last_search_params'] : $this->parameters,
            'patients' => $patientData,
            'patientsID' => $ids,
            'variables' => $variables,
            'variableList' => $variableList,
            'variableData' => $variable_data,
            'saved_searches' => $all_searches,
            'search_label' => isset($_POST['search_name']) ? $_POST['search_name'] : '',
            'from_date' => $from ? $from->format('Y-m-d') : null,
            'to_date' => $to ? $to->format('Y-m-d') : null,
            'show_all_dates' => $show_all_dates,
        ));
    }

    /**
     * @param $var string|string[]
     * @param $ids int[]|null
     * @return CaseSearchVariable
     */
    protected function getVariableInstance($var, $ids = null)
    {
        $variable = null;

        if (is_array($var)) {
            $variable = new $var['class']($ids);
            foreach ($var as $k => $v) {
                if ($k !== 'class') {
                    $variable->$k = $v;
                }
            }
            return $variable;
        }

        return new $var($ids);
    }

    /**
     * Add a parameter to the case search. This is executed through an AJAX request.
     * @throws CException
     */
    public function actionAddParameter()
    {
        $param = $_GET['parameter'];

        /**
         * @var $parameter CaseSearchParameter
         */
        $parameter = new $param['type']();
        $parameter->id = $param['id'];
        $parameter->operation = $param['operation'];
        if (array_key_exists('value', $param)) {
            if (is_array($param['value'])) {
                foreach ($param['value'] as $value) {
                    $key = $value['field'];
                    $parameter->$key = $value['id'];
                }
            } else {
                $parameter->value = $param['value'];
            }
        }

        if ($parameter->validate()) {
            $this->renderPartial('parameter_form', array(
                'model' => $parameter,
                'id' => $parameter->id,
            ));
        } else {
            foreach ($parameter->getErrors() as $attr => $errors) {
                echo '<li>' . implode(', ', $errors) . '</li>';
            }
            http_response_code(400);
        }

        Yii::app()->end();
    }

    public function actionGetOptions()
    {
        /**
         * @var $parameter CaseSearchParameter
         */
        $type = Yii::app()->request->getQuery('type');
        $parameter = new $type();
        $this->renderJSON($parameter->getOptions());
    }

    /**
     * Load the selected search criteria.
     * @param $id
     * @throws CHttpException
     * @throws CException
     */
    public function actionLoadSearch($id)
    {
        $preview = isset($_GET['preview']) ? $_GET['preview'] : null;

        $search = SavedSearch::model()->findByPk($id);
        if (!$search) {
            throw new CHttpException(404, 'Saved search not found');
        }
        $this->actionClear();
        $params = unserialize($search->search_criteria, array('allowed_classes' => true));
        echo '<tbody>';

        foreach ($params as $param) {
            $class_name = $param['class_name'];
            /**
             * @var $instance CaseSearchParameter
             */
            $instance = new $class_name();
            $instance->loadSearch($param);
            if ($preview) {
                // Get the human-readable string representing the full parameter.
                $this->renderPartial(
                    'parameter_form',
                    array(
                        'model' => $instance,
                        'id' => $instance->id,
                        'readonly' => true,
                    )
                );
            } else {
                $this->renderPartial(
                    'parameter_form',
                    array(
                        'model' => $instance,
                        'id' => $instance->id,
                    )
                );
            }
        }
        echo '</tbody>';
    }

    /**
     * Save the search criteria.
     * @throws CHttpException
     * @throws Exception
     */
    public function actionSaveSearch()
    {
        $criteria_list = array();
        $search = new SavedSearch();
        $valid = $this->populateParams();

        if (!empty($this->parameters) && $valid) {
            foreach ($this->parameters as $parameter) {
                /**
                 * @var $mergedParam CaseSearchParameter
                 */
                $criteria_list[] = $parameter->saveSearch();
            }
            $search_criteria = serialize($criteria_list);
            $search->search_criteria = $search_criteria;
            $search->name = $_POST['search_name'];

            if (!$search->save()) {
                Yii::log(var_export($search->getErrors(), true));
                throw new CHttpException(500, 'Unable to save search');
            }
            Yii::app()->user->setFlash('success', 'Search saved successfully.');
        }
        $this->redirect('/OECaseSearch/caseSearch/index');
    }

    /**
     * Deletes the selected saved search
     * @param $id int ID of the saved search to delete.
     * @throws CHttpException
     */
    public function actionDeleteSearch($id)
    {
        $search = SavedSearch::model()->findByPk($id);

        if (!$search) {
            throw new CHttpException(404, 'Unable to delete saved search - Saved search not found.');
        }
        if (!$search->delete()) {
            throw new CHttpException(500, 'Unable to delete saved search - Unknown error occurred.');
        }
    }

    /**
     * Populate the list of search parameters.
     * @param bool $populate_param_cache True if the controller's parameter cache should be populated as well; otherwise false.
     * @return bool
     */
    protected function populateParams($populate_param_cache = false)
    {
        $valid = true;
        if ($populate_param_cache) {
            // Ensure that the parameter list is empty before appending to it.
            $this->parameterList = array();
        }
        foreach ($this->module->getConfigParam('parameters') as $group) {
            foreach ($group as $parameter) {
                $paramName = $parameter . 'Parameter';
                if ($populate_param_cache) {
                    $this->parameterList[] = $paramName;
                }

                if (isset($_POST[$paramName])) {
                    foreach ($_POST[$paramName] as $id => $param) {
                        /**
                         * @var $newParam CaseSearchParameter
                         */
                        $newParam = new $paramName();
                        $newParam->attributes = $_POST[$paramName][$id];
                        if (!$newParam->validate()) {
                            $valid = false;
                        }
                        $this->parameters[$id] = $newParam;
                    }
                }
            }
        }
        return $valid;
    }

    public function actionSearchCommonItems()
    {
        $term = Yii::app()->request->getQuery('term');
        $type = Yii::app()->request->getQuery('type');

        /**
         * @var $type CaseSearchParameter
         */
        $values = $type::getCommonItemsForTerm($term);

        $this->renderJSON($values);
    }

    /**
     * Get the drilldown list for the selected datapoint.
     * @param $patient_ids string List of patient IDs as a string (List is a string due to it being a parameter of a HTTP request).
     * @throws CException
     */
    public function actionGetDrilldownList($patient_ids)
    {
        $pagination = array(
            'pageSize' => 10,
        );
        $criteria = new CDbCriteria();
        $criteria->compare('t.id', explode(',', $patient_ids));
        $criteria->with = 'contact';
        $criteria->compare('t.deleted', 0);
        $patients = new CActiveDataProvider('Patient', array(
            'criteria' => $criteria,
            'totalItemCount' => count(explode(',', $patient_ids)),
            'pagination' => $pagination,
            'sort' => array(
                'attributes' => array(
                    'last_name' => array(
                        'asc' => 'last_name',
                        'desc' => 'last_name DESC',
                        'label' => 'Surname',
                    ),
                    'first_name' => array(
                        'asc' => 'first_name',
                        'desc' => 'first_name DESC',
                        'label' => 'First name',
                    ),
                    'age' => array(
                        'asc' => 'TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE()))',
                        'desc' => 'TIMESTAMPDIFF(YEAR, dob, IFNULL(date_of_death, CURDATE())) DESC',
                        'label' => 'Age',
                    ),
                    'gender' => array(
                        'asc' => 'gender',
                        'desc' => 'gender DESC',
                        'label' => 'Gender',
                    ),
                    'hos_num' => array(
                        'asc' => 'hos_num',
                        'desc' => 'hos_num DESC',
                        'label' => \SettingMetadata::model()->getSetting('hos_num_label'),
                    ),
                    'nhs_num' => array(
                        'asc' => 'nhs_num',
                        'desc' => 'nhs_num DESC',
                        'label' => \SettingMetadata::model()->getSetting('nhs_num_label'),
                    ),
                ),
                'defaultOrder' => array(
                    'last_name' => CSort::SORT_ASC,
                ),
            ),
        ));

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('patient_drill_down_list', array(
                'patients' => $patients,
                'display_class' => 'oe-search-drill-down-list',
                'display' => true,
            ));
            Yii::app()->end();
        }

        $this->renderPartial('patient_drill_down_list', array(
            'patients' => $patients,
            'display_class' => 'oe-search-drill-down-list',
            'display' => true,
        ));
    }

    /**
     * Clear the parameters and search results. This is executed through an AJAX request
     */
    public function actionClear()
    {
        unset($_SESSION['last_search'], $_SESSION['last_search_params']);
    }

    /**
     * @throws Exception
     */
    public function actionDownloadCSV()
    {
        $start_date = null;
        $end_date = null;
        $mode = Yii::app()->request->getQuery('mode');
        $var = $_POST['var'];

        $this->populateParams();
        $ids = array_column(Yii::app()->searchProvider->search($this->parameters), 'id');

        $variable = $this->getVariableInstance(Yii::app()->params['CaseSearch']['variables']['OECaseSearch'][$var], $ids);

        if (!isset($_POST['show-all-dates']) || $_POST['show-all-dates'] !== '1') {
            if (isset($_POST['from_date']) && $_POST['from_date']) {
                $start_date = new DateTime($_POST['from_date']);
            }
            if (isset($_POST['to_date']) && $_POST['to_date']) {
                $end_date = new DateTime($_POST['to_date']);
            }
        } else {
            $start_date = null;
            $end_date = null;
        }

        Yii::app()->searchProvider->getVariableData($variable, $start_date, $end_date, true, $mode);

        Yii::app()->end();
    }

    public function beforeAction($action)
    {
        $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OECaseSearch.assets'), true);
        Yii::app()->clientScript->registerCssFile($assetPath . '/css/module.css');

        // This is required when the search results return any records.
        Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js'), true);
        if (!Yii::app()->clientScript->isScriptFileRegistered('js/OpenEyes.UI.Dialog.js')) {
            Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.js');
        }

        if (!Yii::app()->clientScript->isScriptFileRegistered($assetPath . '/js/OpenEyes.UI.Dialog.LoadSavedSearch.js')) {
            Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.LoadSavedSearch.js', 'application.modules.OECaseSearch.assets', -10);
        }

        if (!Yii::app()->clientScript->isScriptFileRegistered($assetPath . '/js/OpenEyes.UI.Dialog.SaveSearch.js')) {
            Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.SaveSearch.js', 'application.modules.OECaseSearch.assets', -10);
        }

        return parent::beforeAction($action);
    }

    /**
     * @throws CException
     */
    public function actionRenderPopups()
    {
        if (isset($_POST["patientsID"])) {
            $patientsID = explode(",", $_POST["patientsID"]);
            $patients = Patient::model()->findAllByPk($patientsID);
            foreach ($patients as $patient) {
                $this->renderPartial('application.widgets.views.PatientIcons', array('data' => $patient, 'page' => 'caseSearch'));
            }
            Audit::add('case-search', 'rendered-summary-popups', 'Summary popups for patients ' . $_POST["patientsID"] . ' were rendered and may have been viewed', false);
        }
    }

    public function actionLookedAtPopup()
    {
        $patientID = $_GET["patientID"];
        $summaryId = $_GET["summaryId"];
        if (isset($patientID)) {
            $patient = Patient::model()->findByPk($patientID);
            if (!empty($patient)) {
                Audit::add('case-search', 'viewed-summary-popups', 'Summary popup '.  $summaryId . ' for patient ' . $patientID . ' has been viewed', false, array(
                    'module' => 'OECaseSearch', 'patient_id' => $patientID
                ) );
                return true;
            } else {
                throw new CHttpException(404, 'Unable to find patient');
            }
        }
        throw new CHttpException(404, 'Unable to find patient - no patientID');
    }
}
