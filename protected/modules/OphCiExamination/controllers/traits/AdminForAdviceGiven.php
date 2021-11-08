<?php

namespace OEModule\OphCiExamination\controllers\traits;

use OEModule\OphCiExamination\models\AdviceLeaflet;
use OEModule\OphCiExamination\models\AdviceLeafletCategory;
use OEModule\OphCiExamination\models\AdviceLeafletCategoryAssignment;
use OEModule\OphCiExamination\models\AdviceLeafletCategorySubspecialty;

trait AdminForAdviceGiven
{
    public function actionAdviceLeaflets()
    {
        $this->genericAdmin('Leaflets', AdviceLeaflet::class, [
            'extra_fields' => array(
                array(
                    'field' => 'institution_id',
                    'type' => 'institution',
                    'model' => AdviceLeaflet::model(),
                    'current_institution_only' => true
                ),
            ),
        ], null, false);
    }

    public function actionAdviceLeafletCategories()
    {
        // Needs a more complex admin screen because of the ability to assign more than 1 leaflet to a category.
        $this->render('list_AdviceLeafletCategory', [
            'title' => 'Advice Leaflet Categories',
            'model_list' => AdviceLeafletCategory::model()->findAll('active = 1 AND institution_id = :id', [':id' => \Yii::app()->session['selected_institution_id']])
        ]);
    }

    public function actionAddAdviceLeafletCategory()
    {
        $model = new AdviceLeafletCategory();
        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];
            $model->institution_id = \Yii::app()->session['selected_institution_id'];

            if ($model->save()) {
                $model->refresh();
                foreach ($_POST['leaflets'] as $i => $leaflet_id) {
                    $assignment = new AdviceLeafletCategoryAssignment();
                    $assignment->category_id = $model->id;
                    $assignment->leaflet_id = $leaflet_id;
                    $assignment->display_order = $i + 1;
                    $assignment->save();
                }
                \Yii::app()->user->setFlash('success', 'Category created');

                $this->redirect(array('adviceLeafletCategories'));
            }
        }
        $this->render('create', [
            'model' => $model,
            'title' => 'Add Advice Leaflet Category',
            'cancel_uri' => '/OphCiExamination/admin/adviceLeafletCategories',
        ]);
    }

    public function actionGetLeafletCategories()
    {
        $subspecialty_id = $_GET['subspecialty_id'];
        $criteria = new \CDbCriteria();
        $criteria->with = array('category');
        $criteria->together = true;

        $criteria->addCondition('subspecialty_id = :query AND category.institution_id = :institution_id');
        $criteria->params[':query'] = $subspecialty_id;
        $criteria->params[':institution_id'] = \Yii::app()->session['selected_institution_id'];
        $criteria->order = 't.display_order';

        $leaflets = AdviceLeafletCategorySubspecialty::model()->findAll($criteria);

        $html_tbody = '';

        foreach ($leaflets as $leaflet) {
            $delete_button = '<input id="' . $leaflet->id . '-subspecialty-button" 
                        type="button" value="DELETE" onclick="deleteLeaflet(this);" />';
            // Display the delete button if context has been selected or if context has not been selected and the user is an installation admin.
            // Otherwise, hide it so subspecialty-level mappings cannot be deleted by institution admins.
            $model_name = \CHtml::modelName($leaflet);
            $html_tbody .=
                "<tr>
                <td class=\"reorder\">
                    <span>↑↓</span>
                    <input type=\"hidden\" class='js-category-display-order' name=\"{$model_name}[display_order][]\" value=\"{$leaflet->id}\"/>
                    <input type=\"hidden\" class='js-category-id' name=\"{$model_name}[id][]\" value=\"{$leaflet->id}\"/>
                </td>
                <td>{$leaflet->category->name}</td>
                <td>$delete_button</td>
            </tr>";
        }

        echo $html_tbody;
    }

    /**
     * @throws \CHttpException
     */
    public function actionSetCategoryOrder()
    {
        $id = $_POST['id'];
        $display_order = $_POST['display_order'];

        $model = AdviceLeafletCategorySubspecialty::model()->findByPk($id);

        if ($model) {
            $model->display_order = $display_order;
            if (!$model->save()) {
                throw new \CHttpException(500, 'Unable to save category-subspecialty mapping.');
            }
        } else {
            throw new \CHttpException(404, 'Subspecialty mapping not found for selected leaflet category.');
        }
        echo '1';
    }

    public function actionEditAdviceLeafletCategory($id)
    {
        $model = AdviceLeafletCategory::model()->findByPk($id);

        if ($model && isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                AdviceLeafletCategoryAssignment::model()->deleteAllByAttributes(['category_id' => $model->id]);
                foreach ($_POST['leaflets'] as $i => $leaflet_id) {
                    $assignment = new AdviceLeafletCategoryAssignment();
                    $assignment->category_id = $model->id;
                    $assignment->leaflet_id = $leaflet_id;
                    $assignment->display_order = $i + 1;
                    $assignment->save();
                }
                \Yii::app()->user->setFlash('success', 'Category updated');

                $this->redirect(array('adviceLeafletCategories'));
            }
        }
        $this->render('update', [
            'model' => $model,
            'title' => 'Edit Advice Leaflet Category',
            'cancel_uri' => '/OphCiExamination/admin/adviceLeafletCategories',
        ]);
    }

    public function actionAdviceLeafletSubspecialties()
    {
        $this->render('list_AdviceLeafletCategorySubspecialty');
    }

    public function actionAddAdviceLeafletSubspecialty()
    {
        $category_id = @$_POST['category_id'];
        $subspecialty_id = @$_POST['subspecialty_id'];
        $display_order = \Yii::app()->db->createCommand()
            ->select('MAX(display_order)')
            ->from('ophciexamination_advice_leaflet_category_subspecialty')
            ->where('subspecialty_id = :id')
            ->bindValues([':id' => $subspecialty_id])
            ->queryScalar();
        $new_leaflet = new AdviceLeafletCategorySubspecialty();
        $new_leaflet->subspecialty_id = $subspecialty_id;
        $new_leaflet->category_id = $category_id;
        $new_leaflet->display_order = $display_order ? $display_order + 1 : 1;

        if (!$new_leaflet->save()) {
            echo 'error';
        }
    }

    public function actionDeleteAdviceLeafletSubspecialties()
    {
        $id = @$_POST['id'];

        if (!AdviceLeafletCategorySubspecialty::model()->deleteByPk($id)) {
            echo 'error';
        }
    }

    /**
     * Return a list with all leaflets.
     */
    public function actionSearchAdviceLeafletCategories()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            $criteria = new \CDbCriteria();
            $criteria->addCondition('active = 1 AND institution_id = :institution_id');
            $params = [':institution_id' => \Yii::app()->session['selected_institution_id']];
            if (isset($_GET['term'])) {
                $term = $_GET['term'];
                $criteria->addCondition(array('LOWER(name) LIKE :term'), 'OR');
                $params[':term'] = '%' . strtolower(strtr($term, array('%' => '\%'))) . '%';
            }
            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;
            $results = AdviceLeafletCategory::model()->findAll($criteria);

            $return = array();
            foreach ($results as $resultRow) {
                $return[] = array(
                    'label' => $resultRow->name,
                    'value' => $resultRow->name,
                    'id' => $resultRow->id,
                );
            }
            $this->renderJSON($return);
        }
    }
}
