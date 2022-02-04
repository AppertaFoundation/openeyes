<?php

/**
 * Class ResultTypeController.
 *
 * Controller to administer result types
 */
class ResultTypeController extends BaseAdminController
{
    public $group = 'Lab Results';

    /**
     * Lists Result Types.
     *
     * @throws Exception
     */

    public function actionList()
    {
        $asset_manager = Yii::app()->getAssetManager();
        $asset_manager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $asset_manager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('/admin/list_OphInLabResults_Type', array(
            'model_list' => OphInLabResults_Type::model()->findAll(),
            'title' => 'Manage Result Types',
            'model_class' => 'OphInLabResults_Type',
        ));
    }

    public function actionEdit()
    {
        $savedModelWithErrors = $this->save('edit');
        $this->render('/admin/edit', array(
            'model' => $savedModelWithErrors['model'],
            'title' => 'Edit Results Type',
            'errors' => $savedModelWithErrors['errors'] ?? null,
            'cancel_uri' => '/OphInLabResults/oeadmin/resultType/list',
        ));
    }

    public function actionAdd()
    {
        $savedModelWithErrors = $this->save('add');
        $this->render('/admin/edit', array(
            'model' => $savedModelWithErrors['model'],
            'title' => 'Add Results Type',
            'errors' => $savedModelWithErrors['errors'] ?? null,
            'cancel_uri' => '/OphInLabResults/oeadmin/resultType/list',
        ));
    }

    /**
     * @throws Exception
     */
    public function actionAddMapping()
    {
        $transaction = Yii::app()->db->beginTransaction();
        $result = [];
        $result['status'] = 1;
        $result['errors'] = [];
        $typeIds = Yii::app()->request->getPost('resultTypes', []);
        $institution_id = Institution::model()->getCurrent()->id;

        $result_types = OphInLabResults_Type::model()->findAllByPk($typeIds);

        foreach ($result_types as $type) {
            try {
                if (!$type->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                    $result['errors'][] = $type->getErrors();
                }
            } catch (Exception $e) {
                $result['status'] = 0;
                $result['errors'][] = $e->getMessage();
            }
        }

        if (!empty($result['errors'])) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }

        $this->renderJSON($result);
    }

    public function actionDeleteMapping()
    {
        $transaction = Yii::app()->db->beginTransaction();
        $result = [];
        $result['status'] = 1;
        $result['errors'] = [];
        $typeIds = Yii::app()->request->getPost('resultTypes', []);
        $types = OphInLabResults_Type::model()->findAllByPk($typeIds);
        $institution_id = Institution::model()->getCurrent()->id;
        foreach ($types as $type) {
            try {
                if (!$type->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                    $result['errors'][] = $type->getErrors();
                }
            } catch (Exception $e) {
                $result['status'] = 0;
                $result['errors'][] = $e->getMessage();
            }
        }

        if (!empty($result['errors'])) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }

        $this->renderJSON($result);
    }

    function save($mode)
    {
        $errors = [];
        $request = Yii::app()->getRequest();
        if ($mode === 'add') {
            $model = new OphInLabResults_Type();
            $elementType = ElementType::model()->find('class_name = "Element_OphInLabResults_Entry"');
        } else {
            $model = OphInLabResults_Type::model()->findByPk((int)$request->getParam('id'));
        }

        if ($request->getPost('OphInLabResults_Type')) {
            $transaction = Yii::app()->db->beginTransaction();
            $model->attributes = $request->getPost('OphInLabResults_Type');
            if (isset($elementType)) {
                $model->result_element_id = $elementType->id;
            }
            if ($model->fieldType->name !== "Numeric Field") {
                $model->min_range = null;
                $model->max_range = null;
                $model->normal_min = null;
                $model->normal_max = null;
                $model->custom_warning_message = null;
            }
            if (!$model->save()) {
                $errors = $model->getErrors();
            }

            if ($model->fieldType->name === "Drop-down Field") {
                if (isset($_POST['type_options']['options_id'])) {
                    $optionsId = $_POST['type_options']['options_id'];
                    $values = $_POST['type_options']['value'];
                } else {
                    $optionsId = [];
                }

                if ($mode === 'edit') {
                    $resultOptions = $model->resultOptions;
                }

                foreach ($optionsId as $key => $optionId) {
                    $foundExistingOption = false;

                    if ($mode === 'edit') {
                        foreach ($resultOptions as $resultOption) {
                            if ($resultOption->id === $optionId) {
                                $resultOption->value = $values[$key];
                                if (!$resultOption->save()) {
                                    $errors = array_merge($resultOption->getErrors(), $errors);
                                }
                                $foundExistingOption = true;
                                break;
                            }
                        }
                    }

                    if (!$foundExistingOption) {
                        $resultOption = new OphInLabResults_Type_Options();
                        $resultOption->type = $model->id;
                        $resultOption->value = $values[$key];
                        if (!$resultOption->save()) {
                            $errors = array_merge($resultOption->getErrors(), $errors);
                        }
                    }
                }

                if ($mode === 'edit') {
                    $resultOptions = array_filter($resultOptions, function ($resultOption) use ($optionsId) {
                        return !in_array($resultOption->id, $optionsId, true);
                    });

                    foreach ($resultOptions as $resultOption) {
                        $resultOption->delete();
                    }
                }
            }

            if (empty($errors)) {
                $transaction->commit();
                Yii::app()->user->setFlash('success', 'Lab Results Type saved');
                $this->redirect(array('List'));
            } else {
                $transaction->rollback();
            }
        }

        return ['model' => $model, 'errors' => $errors];
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $transaction = Yii::app()->db->beginTransaction();
        $result = [];
        $result['status'] = 1;
        $result['errors'] = [];
        $typeIds = Yii::app()->request->getPost('resultTypes', []);

        $resultTypes = OphInLabResults_Type::model()->findAllByPk($typeIds);

        foreach ($resultTypes as $type) {
            try {
                if (!$type->deleteMappings(ReferenceData::LEVEL_INSTITUTION)) {
                    $result['errors'][] = $type->getErrors();

                    // Clear the errors here as we don't want duplication when we try to delete the base model itself.
                    $type->clearErrors();
                }

                foreach ($type->institutionAssignments as $assignment) {
                    if (!$assignment->delete()) {
                        $result['errors'][] = $assignment->getErrors();
                    }
                }
                if (!$type->delete()) {
                    $result['status'] = 0;
                    $result['errors'][] = $type->getErrors();
                }
            } catch (Exception $e) {
                $result['status'] = 0;
                $result['errors'][] = $e->getMessage();
            }
        }

        if (!empty($result['errors'])) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }

        $this->renderJSON($result);
    }

    public function actions()
    {
        return [
          'sortTypes' => [
            'class' => 'SaveDisplayOrderAction',
            'model' => OphInLabResults_Type::model(),
            'modelName' => 'OphInLabResults_Type',
          ],
        ];
    }
}
