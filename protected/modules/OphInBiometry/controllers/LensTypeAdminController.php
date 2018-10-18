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
            // get data from POST
            $user_data = \Yii::app()->request->getPost('OphInBiometry_LensType_Lens');

            $lensType_object->name = $user_data['name'];
            $lensType_object->display_name = $user_data['display_name'];
            $lensType_object->description = $user_data['description'];
            $lensType_object->comments = $user_data['comments'];
            $lensType_object->acon = $user_data['acon'];
            $lensType_object->active = $user_data['active'];

            // try saving the data
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
            $ids = Yii::app()->request->getPost('select');

            foreach ($ids as $id) {
                if (!OphInBiometry_LensType_Lens::model()->deleteByPk($id)) {
                    echo "error";
                }
            }
        }
        echo 1;
    }
}
