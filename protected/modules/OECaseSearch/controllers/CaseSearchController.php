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
                ),
                'users' => array('@'),
            ),
        );
    }

    /**
     * Primary case search action.
     * @param $trial_id integer The Trial that this case search is in context of
     * @throws Exception
     */
    public function actionIndex($trial_id = null)
    {
        $variables = array();
        $variable_data = array();
        $ids = array();
        $from = null;
        $to = null;
        $searchProvider = $this->module->getSearchProvider('mysql');
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
            /**
             * @var $searchProvider SearchProvider
             */
            $ids = $this->runSearch();

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
                ),
                'defaultOrder' => array(
                    'last_name' => CSort::SORT_ASC,
                ),
            ),
        ));

        $all_searches = SavedSearch::model()->findAll();

        if (array_key_exists('variable_list', $_POST) && !empty($ids)) {
            $variable_names = explode(',', $_POST['variable_list']);
            if ($variable_names[0] != '') {
                foreach ($variable_names as $variable_name) {
                    $class_name = Yii::app()->params['CaseSearch']['variables']['OECaseSearch'][$variable_name];
                    $variable = new $class_name($ids);
                    $variables[] = $variable;
                }
                if (!isset($_POST['show-all-dates']) || $_POST['show-all-dates'] !== '1') {
                    if ($_POST['from_date']) {
                        $from = new DateTime($_POST['from_date']);
                    }
                    if ($_POST['to_date']) {
                        $to = new DateTime($_POST['to_date']);
                    }
                } else {
                    $from = null;
                    $to = null;
                }
                $variable_data = $searchProvider->getVariableData($variables, $from, $to);
            }
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
            'variables' => $variables,
            'variableList' => $variableList,
            'variableData' => $variable_data,
            'saved_searches' => $all_searches,
            'search_label' => isset($_POST['search_name']) ? $_POST['search_name'] : '',
            'from_date' => $from ? $from->format('Y-m-d') : null,
            'to_date' => $to ? $to->format('Y-m-d') : null,
            'show_all_dates' => isset($_POST['show-all-dates']) ? $_POST['show-all-dates'] === '1' : false,
        ));
    }

    /**
     * Add a parameter to the case search. This is executed through an AJAX request.
     * @throws CException
     */
    public function actionAddParameter()
    {
        $param = $_GET['parameter'];

        $parameter = new $param['type'];
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

        $this->renderPartial('parameter_form', array(
            'model' => $parameter,
            'id' => $parameter->id,
        ));
        Yii::app()->end();
    }

    public function actionGetOptions()
    {
        /**
         * @var $parameter CaseSearchParameter
         */
        $type = Yii::app()->request->getQuery('type');
        $parameter = new $type;
        echo json_encode($parameter->getOptions());
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

        if ($preview) {
            foreach (explode(',', $search->variables) as $var) {
                $class_name = Yii::app()->params['CaseSearch']['variables']['OECaseSearch'][$var];
                $variable = new $class_name(null);
                echo '<tr class="search-var">
                <td>' . $variable->label . '</td>
                </tr>';
            }
        } else {
            foreach (explode(',', $search->variables) as $var) {
                $class_name = Yii::app()->params['CaseSearch']['variables']['OECaseSearch'][$var];
                /**
                 * @var $variable CaseSearchVariable
                 */
                $variable = new $class_name(null);
                echo '<tr class="search-var" data-id="' . $variable->field_name . '">
                <td>' . $variable->label . '</td>
                <td><i class="oe-i remove-circle small"></i></td>
                </tr>';
            }
            echo '<tr id="var-list"><td>' . $search->variables . '</td></tr>';
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
            $search->variables = $_POST['variable_list'];

            if (!$search->save()) {
                Yii::log(var_Export($search->getErrors(), true));
                throw new CHttpException(500, 'Unable to save search');
            }
            Yii::app()->user->setFlash('success', 'Search saved successfully.');
        }
        $this->redirect('/OECaseSearch/caseSearch/index');
    }

    /**
     * @param $id
     * @throws CHttpException
     * @throws CDbException
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
                        $newParam = new $paramName;
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

    protected function runSearch()
    {
        $searchProvider = $this->module->getSearchProvider('mysql');
        $auditValues = array();
        /**
         * @var $searchProvider SearchProvider
         */
        $results = $searchProvider->search($this->parameters);

        if (count($results) === 0) {
            /**
             * @var $param CaseSearchParameter
             */
            foreach ($this->parameters as $param) {
                $auditValues[] = $param->getAuditData();
            }
            Audit::add('case-search', 'search-results', implode(' AND ', $auditValues) . '. No results', null, array('module' => 'OECaseSearch'));
        }
        // deconstruct the results list into a single array of primary keys.
        return array_column($results, 'id');
    }

    public function actionSearchCommonItems()
    {
        $term = Yii::app()->request->getQuery('term');
        $type = Yii::app()->request->getQuery('type');

        /**
         * @var $type CaseSearchParameter
         */
        $values = $type::getCommonItemsForTerm($term);

        echo json_encode($values);
    }

    /**
     * @param $patient_ids
     * @throws CException
     */
    public function actionGetDrilldownList($patient_ids) {
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
     * @throws CHttpException
     * @throws Exception
     */
    public function actionDownloadCSV()
    {
        /**
         * @var $searchProvider SearchProvider
         */
        $searchProvider = $this->module->getSearchProvider('mysql');
        $start_date = null;
        $end_date = null;
        $mode = Yii::app()->request->getQuery('mode');
        $var = $_POST['var'];

        $this->populateParams();
        $ids = $this->runSearch();

        $class_name = Yii::app()->params['CaseSearch']['variables']['OECaseSearch'][$var];
        $variable = new $class_name($ids);
        if (!isset($_POST['show-all-dates']) || $_POST['show-all-dates'] !== '1') {
            if ($_POST['from_date']) {
                $start_date = new DateTime($_POST['from_date']);
            }
            if ($_POST['to_date']) {
                $end_date = new DateTime($_POST['to_date']);
            }
        } else {
            $start_date = null;
            $end_date = null;
        }

        $searchProvider->getVariableData($variable, $start_date, $end_date, true, $mode);

        Yii::app()->end();
    }

    public function beforeAction($action)
    {
        $assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.OECaseSearch.assets'), true);
        Yii::app()->clientScript->registerCssFile($assetPath . '/css/module.css');

        // Loading the following files from the package before calling each action as they are required by the zii AutoCompleteSearch widget (used for diagnosis)
        // and they must be loaded before the widget loads any jquery files.
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerCoreScript('jquery.ui');
        Yii::app()->clientScript->registerCoreScript('cookie');

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
}
