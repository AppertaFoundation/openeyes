<?php

/**
 * Created by PhpStorm.
 * User: veta
 * Date: 06/05/15
 * Time: 11:30.
 */
class LensTypeAdminController extends BaseAdminController
{
    /**
     * @var int
     */
    public $itemsPerPage = 100;

    public $group = 'Biometry';

    /**
     * Lists lens types.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $criteria = new CDbCriteria();
        $search = \Yii::app()->request->getPost('search', ['query' => '', 'active' => '']);

        if (Yii::app()->request->isPostRequest) {
            if ($search['query']) {
                $criteria->addCondition('name = :query', 'OR');
                $criteria->addCondition('id = :query', 'OR');
                $criteria->addCondition('display_name = :query', 'OR');
                $criteria->addCondition('description = :query', 'OR');
                $criteria->addCondition('acon = :query', 'OR');
                $criteria->params[':query'] = $search['query'];
            }

            if ($search['active'] == 1) {
                $criteria->addCondition('t.active = 1');
            } elseif ($search['active'] != '') {
                $criteria->addCondition('t.active != 1');
            }
        }

        $lensType_lens = OphInBiometry_LensType_Lens::model();

        $this->render('/admin/index', array(
            'pagination' => $this->initPagination($lensType_lens, $criteria),
            'lensType_lens' => $lensType_lens->findAll($criteria),
            'search' => $search,
        ));
    }

    /**
     * Edits or adds a lens type.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $errors = [];
        $lensType_object = OphInBiometry_LensType_Lens::model()->findByPk($id);

        if (!$lensType_object) {
            $lensType_object = new OphInBiometry_LensType_Lens();
            if ($id) {
                $lensType_object->id = $id;
            }
        }

        if (Yii::app()->request->isPostRequest) {
            $user_data = \Yii::app()->request->getPost('OphInBiometry_LensType_Lens');
            $lensType_object->attributes = $user_data;

            if (!$lensType_object->save()) {
                $errors = $lensType_object->getErrors();
            } else {
                $this->redirect('/OphInBiometry/lensTypeAdmin/list');
            }
        }

        $this->render('/admin/edit', array(
            'lensType_lens' => $lensType_object,
            'errors' => $errors
        ));
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        if (Yii::app()->request->isPostRequest) {
            $data = \Yii::app()->request->getPost('select', []);
            if ($data) {
                $criteria = new CDbCriteria();
                $criteria->addInCondition('id', $data);

                OphInBiometry_LensType_Lens::model()->deleteAll($criteria);
            }
        }
        echo 1;
    }
}
