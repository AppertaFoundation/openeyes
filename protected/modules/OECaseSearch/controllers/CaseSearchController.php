<?php

/**
 * Class CaseSearchController
 *
 * @property null|Trial $trialContext
 */
class CaseSearchController extends BaseModuleController
{
    public $trialContext;

    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + addParameter',
            'ajaxOnly + getSearchesByUser',
            'ajaxOnly + otherSearchUsers',
            'ajaxOnly + loadSearch',
            'ajaxOnly + clear',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'addParameter', 'otherSearchUsers', 'getSearchesByUser', 'loadSearch', 'saveSearch', 'clear'),
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
        $valid = true;
        $parameters = array();
        $auditValues = array();
        $fixedParameters = $this->module->getFixedParams();
        $parameterList = array();
        $ids = array();
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
        foreach ($this->module->getConfigParam('parameters') as $group) {
            foreach ($group as $parameter) {
                $paramName = $parameter . 'Parameter';
                $parameterList[] = $paramName;
                if (isset($_POST[$paramName])) {
                    foreach ($_POST[$paramName] as $id => $param) {
                        /**
                         * @var $newParam CaseSearchParameter
                         */
                        $newParam = new $paramName;
                        $newParam->attributes = $_POST[$paramName][$id];
                        $newParam->isFixed = false;
                        if (!$newParam->validate()) {
                            $valid = false;
                        }
                        $parameters[$id] = $newParam;
                    }
                }
            }
        }
        foreach ($fixedParameters as $parameter) {
            /**
             * @var $parameter CaseSearchParameter
             */
            if (isset($_POST[get_class($parameter)])) {
                foreach ($_POST[get_class($parameter)] as $id => $param) {
                    $parameter->attributes = $_POST[get_class($parameter)][$id];
                    $parameter->isFixed = true;
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
            $_SESSION['last_search_params'] = $parameters;
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

        $current_user_searches = SavedSearch::model()->findAllByAttributes(array('created_user_id' => Yii::app()->user->id));
        $query = Yii::app()->db->createCommand()
            ->select('cs.created_user_id id, CONCAT(u.first_name, " ", u.last_name) name')
            ->from('case_search_saved_search cs')
            ->join('user u', 'u.id = cs.created_user_id')
            ->where('cs.created_user_id != :user_id', array(':user_id' => Yii::app()->user->id));
        $query->distinct = true;
        $all_users = $query->queryAll();

        // Get the list of parameter types for display on-screen.
        $paramList = $this->module->getParamList();
        if (isset($_SESSION['last_search_params']) && !empty($_SESSION['last_search_params'])) {
            foreach ($_SESSION['last_search_params'] as $key => $last_search_param) {
                $last_search_param_name = get_class($last_search_param);
                if (!in_array($last_search_param_name, $parameterList, true)) {
                    unset($_SESSION['last_search_params'][$key]);
                }
            }
        }

        $this->render('index', array(
            'paramList' => $paramList,
            'params' => (empty($parameters) && isset($_SESSION['last_search_params']))?  $_SESSION['last_search_params']:$parameters,
            'fixedParams' => $fixedParameters,
            'patients' => $patientData,
            'saved_searches' => $current_user_searches,
            'user_list' => array_map(
                static function ($item) {
                    return array('id' => $item['id'], 'name' => $item['name']);
                },
                $all_users
            )
        ));
    }

    /**
     * Add a parameter to the case search. This is executed through an AJAX request.
     * @throws CException
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
     * @param $id
     * @throws CException
     */
    public function actionGetSearchesByUser($id)
    {
        $searches = Yii::app()->db->createCommand()
            ->select('id, name')
            ->from('case_search_saved_search')
            ->where('created_user_id = :user_id', array(':user_id' => $id))
            ->queryAll();
        echo json_encode($searches);
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
        $preview_list = array();

        $search = SavedSearch::model()->findByPk($id);
        if (!$search) {
            throw new CHttpException(404, 'Saved search not found');
        }
        $this->actionClear();
        $params = unserialize($search->search_criteria, array('allowed_classes' => true));
        if (!$preview) {
            echo '<tbody>';
        }

        foreach ($params as $param) {
            $class_name = $param['class_name'];
            /**
             * @var $instance CaseSearchParameter
             */
            $instance = new $class_name();
            $instance->loadSearch($param);
            if ($preview) {
                // Get the human-readable string representing the full parameter.
                if ($instance->getDisplayString()) {
                    $preview_list[] = $instance->getDisplayString();
                }
            } elseif (!$instance->isFixed) {
                $this->renderPartial(
                    'parameter_form',
                    array(
                        'model' => $instance,
                        'id' => $instance->id,
                    )
                );
            } else {
                $this->renderPartial(
                    'fixed_parameter_form',
                    array(
                        'model' => $instance,
                        'id' => $instance->id,
                    )
                );
            }
        }
        if ($preview) {
            echo json_encode($preview_list);
        } else {
            // Output the search label content.
            echo '<tr id="search-label-row"><td>' . $search->name . '</td></tr>';
            echo '</tbody>';
        }
    }

    /**
     * Save the search criteria.
     * @throws CHttpException
     * @throws Exception
     */
    public function actionSaveSearch()
    {
        $criteria_list = array();
        $parameters = array();
        $fixedParameters = $this->module->getFixedParams();
        $search = new SavedSearch();
        $valid = true;
        foreach ($this->module->getConfigParam('parameters') as $group) {
            foreach ($group as $parameter) {
                $paramName = $parameter . 'Parameter';
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
                        $parameters[$id] = $newParam;
                    }
                }
            }
        }
        foreach ($fixedParameters as $parameter) {
            /**
             * @var $parameter CaseSearchParameter
             */
            if (isset($_POST[get_class($parameter)])) {
                foreach ($_POST[get_class($parameter)] as $id => $param) {
                    $parameter->attributes = $_POST[get_class($parameter)][$id];
                    if (!$parameter->validate()) {
                        $valid = false;
                    }
                }
            }
        }
        if (!empty($parameters) && $valid) {
            $mergedParams = array_merge($parameters, $fixedParameters);
            foreach ($mergedParams as $mergedParam) {
                /**
                 * @var $mergedParam CaseSearchParameter
                 */
                $criteria_list[] = $mergedParam->saveSearch();
            }
            $search_criteria = serialize($criteria_list);
            $search->search_criteria = $search_criteria;
            $search->name = $_POST['search_name'];

            if (!$search->save()) {
                throw new CHttpException(500, 'Unable to save search');
            }
            Yii::app()->user->setFlash('success', 'Search saved successfully.');
        }
        $this->redirect('/OECaseSearch/caseSearch/index');
    }

    /**
     * Clear the parameters and search results. This is executed through an AJAX request
     */
    public function actionClear()
    {
        unset($_SESSION['last_search'], $_SESSION['last_search_params']);
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
        Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.js');
        Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.LoadSavedSearch.js', 'application.modules.OECaseSearch.assets', -10);

        return parent::beforeAction($action);
    }
}
