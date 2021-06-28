<?php

class DefaultController extends BaseEventTypeController
{

    var $box_id;
    var $letter;
    var $number;
    
    protected static $action_types = array(
        'addTransaction' => self::ACTION_TYPE_FORM,
        'GetNewStorageFields' => self::ACTION_TYPE_FORM,
        'getAvailableLetterNumberToBox' => self::ACTION_TYPE_FORM,
        'saveNewStorage' => self::ACTION_TYPE_FORM,
        'refreshStorageSelect' => self::ACTION_TYPE_FORM,
        'updateDnaTests' => self::ACTION_TYPE_FORM,
        'print' => self::ACTION_TYPE_PRINT
    );

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('Create', 'Update', 'View', 'Print', 'Delete', 'AddTransaction','GetNewStorageFields','getAvailableLetterNumberToBox','saveNewStorage', 'refreshStorageSelect', 'updateDnaTests'),
                'roles' => array('OprnEditDNAExtraction'),
            ),
            array('allow',
                'actions' => array('View', 'Print'),
                'roles' => array('OprnViewDNAExtraction'),
            ),
        );
    }

    public function volumeRemaining($event_id)
    {
        $api = Yii::app()->moduleAPI->get('OphInDnaextraction');

        return $api->volumeRemaining($event_id);
    }

    public function initActionView()
    {
        parent::initActionView();

        $this->jsVars['dnaExtractionPrintUrl'] = Yii::app()->createUrl('OphInDnaextraction/Default/print/'.$this->event->id);
    }

    public function actionPrint($id)
    {
        parent::actionPrint($id);
    }

    public function actionAddTransaction()
    {
        if (!isset($_GET['i'])) {
            throw new Exception('Row number not set');
        }

        $is_remove_allowed = Yii::app()->request->getQuery('is_remove_allowed');

        $transaction = new OphInDnaextraction_DnaTests_Transaction();
        $transaction->setDefaultOptions();
        $this->renderPartial('_dna_test', array(
            'i' => $_GET['i'],
            'transaction' => $transaction,
            'disabled' => false,
            'is_remove_allowed' => $is_remove_allowed === 'false' ? false : true,
        ));
    }

    /*
     * Validate element related models
     */

    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);

        if ( isset($data['OphInDnaextraction_DnaTests_Transaction']) ) {
            foreach ($data['OphInDnaextraction_DnaTests_Transaction'] as $transaction_data) {
                $transaction = $this->getTransactionModel($transaction_data, $data['Element_OphInDnaextraction_DnaTests']['id']);

                if (!$transaction->validate()) {
                    foreach ($transaction->getErrors() as $error_msgs) {
                        foreach ($error_msgs as $error) {
                            $errors['Tests']['volume'] = $error;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    protected function saveComplexAttributes_Element_OphInDnaextraction_DnaTests($element, $data, $index, $handle_errors = false)
    {
        $item_ids = array();

        if ( isset($data['OphInDnaextraction_DnaTests_Transaction']) ) {
            foreach ($data['OphInDnaextraction_DnaTests_Transaction'] as $transaction_data) {
                $transaction = $this->getTransactionModel($transaction_data, $element->id);

                if (!$transaction->save()) {
                    if (!$handle_errors) {
                        //throw new Exception('Unable to save transaction: '.print_r($transaction->getErrors(), true));
                    } else {
                        $errors = $transaction->getErrors();
                        //  throw new Exception('Unable to save transaction: '.$errors[''][0]);
                    }
                }

                $item_ids[] = $transaction->id;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('element_id = :element_id');
        $criteria->addNotInCondition('id', $item_ids);
        $criteria->params[':element_id'] = $element->id;

        foreach (OphInDnaextraction_DnaTests_Transaction::model()->findAll($criteria) as $transaction) {
            if (!$transaction->delete()) {
                if (!$handle_errors) {
                    throw new Exception('Unable to delete transaction: '.print_r($transaction->getErrors(), true));
                } else {
                    $errors = $transaction->getErrors();
                    throw new Exception('Unable to save transaction: '.$errors[''][0]);
                }
            }
        }
    }

    /**
     * OphInDnaextraction_DnaTests_Transaction factory
     *
     * @param $transaction_data
     * @param $element_id
     * @return OphInDnaextraction_DnaTests_Transaction
     */
    public function getTransactionModel($transaction_data, $element_id)
    {
        $transaction = null;
        if ( isset($transaction_data['id']) && $transaction_data['id']) {
            $transaction = OphInDnaextraction_DnaTests_Transaction::model()->findByPk($transaction_data['id']);
        }

        if (!$transaction) {
            $transaction = new OphInDnaextraction_DnaTests_Transaction();
        }

        $date = new DateTime($transaction_data['date']);

        $transaction->element_id = $element_id;
        $transaction->date = $date->format('Y-m-d');
        $transaction->study_id = $transaction_data['study_id'];
        $transaction->volume = $transaction_data['volume'];
        $transaction->comments = $transaction_data['comments'];

        return $transaction;
    }

    public function isRequiredInUI(BaseEventTypeElement $element)
    {
        return true;
    }
    
    public function actionGetNewStorageFields()
    {
        $element = new Element_OphInDnaextraction_DnaExtraction();
        $this->renderPartial('newStoragePopup', array('element'=> $element), false, true);
    }
    
    public function actionGetAvailableLetterNumberToBox()
    {
        $result = array();
        if ((int)$_POST['box_id'] > '0') {
            $storage = new OphInDnaextraction_DnaExtraction_Storage();
            $boxModel = new OphInDnaextraction_DnaExtraction_Box();
            $boxRanges = $boxModel->boxMaxValues($_POST['box_id']);

            $letterArray = $storage->generateLetterArrays($_POST['box_id'], $boxRanges['maxletter'], $boxRanges['maxnumber']);
            $usedBoxRows = $storage->getAllLetterNumberToBox($_POST['box_id']);


            $arrayDiff = array_filter($letterArray, function ($element) use ($usedBoxRows) {
                return !in_array($element, $usedBoxRows);
            });

            foreach ($arrayDiff as $key => $val) {
                if ($val['letter'] == "0") {
                    $result['letter'] = "You have not specified a maximum letter value.";
                    $result['number'] = "You have not specified a maximum number value.";
                } else {
                    $result['letter'] = $val['letter'];
                    $result['number'] = $val['number'];
                }

                break;
            }
        }

        $this->renderJSON($result);
    }

    public function actionSaveNewStorage()
    {
        $storage = new OphInDnaextraction_DnaExtraction_Storage();


        $storage->box_id = Yii::app()->request->getPost('dnaextraction_box_id');
        $storage->letter = Yii::app()->request->getPost('dnaextraction_letter');
        $storage->number = Yii::app()->request->getPost('dnaextraction_number');
        $storage->letter = $storage->letter ? strtoupper($storage->letter) : $storage->letter;
        $storage->display_order = $storage->calculateDefaultDisplayOrder();

        if ( $storage->save() ) {
            $selectedID = $storage->getPrimaryKey();
            $result = array('s' => 1 , 'selected' => $selectedID);
        } else {
            $errors = '';
            foreach ( $storage->getErrors() as $attribute => $error ) {
                $errors .= $errors == '' ? '' : "<br>";
                $errors .= "$attribute: " . ( implode($error) );
            }
            $result = array(
                's' => 0,
                'msg' => $errors
            );
        }

        $this->renderJSON($result);
    }
    
    public function actionRefreshStorageSelect()
    {
        $element = new Element_OphInDnaextraction_DnaExtraction();
        $this->renderPartial('_boxSelectRefresh', array('element'=> $element), false, true);
    }

    public function actionView($id)
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphInDnaextraction.assets'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dna_tests_view.js');

        parent::actionView($id);
    }

    public function actionUpdate($id)
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphInDnaextraction.assets'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dna_tests_update.js');

        parent::actionUpdate($id);
    }

    public function actionCreate()
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.OphInDnaextraction.assets'), true);
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dna_tests_update.js');

        parent::actionCreate();
    }

    /**
     * @param $id
     *
     * Ajax action to separately save DNA Tests
     */

    public function actionUpdateDnaTests($id)
    {

        $element_array = Yii::app()->request->getPost('OphInDnaextraction_DnaTests_Transaction');
        $element_array = array_shift($element_array);

        $transaction = new OphInDnaextraction_DnaTests_Transaction();

        $transaction->setAttributes($element_array);

        if ($transaction->save()) {
            $this->renderJSON(['success'=>true]);
        } else {
            $error_message = '';
            foreach ($transaction->getErrors() as $attr => $error) {
                $error_message .= "$attr: " . ( implode(',', $error) ) . PHP_EOL;
            }

            $this->renderJSON(['success'=>false, 'message' => $error_message]);
        }

        Yii::app()->end();
    }
}
