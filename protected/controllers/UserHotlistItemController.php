<?php

class UserHotlistItemController extends BaseController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'users' => array('@'),
            ),
        );
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return UserHotlistItem the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        /* @var UserHotlistItem $model */
        $model = UserHotlistItem::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    public function actionRenderHotlistItems($date = null, $is_open = 0)
    {
        foreach (UserHotlistItem::model()->getHotlistItems($is_open, $date) as $hotlistItem) {
            echo $this->renderPartial('//base/_hotlist_item', array('hotlistItem' => $hotlistItem));
        }
    }

    public function actionCloseHotlistItem($hotlist_item_id)
    {
        $model = $this->loadModel($hotlist_item_id);

        if ($model->created_user_id !== Yii::app()->user->id) {
            throw new Exception('Access denied');
        }
        $model->is_open = 0;
        $model->save();
    }

    public function actionOpenHotlistItem($hotlist_item_id)
    {
        $model = $this->loadModel($hotlist_item_id);

        if ($model->created_user_id !== Yii::app()->user->id) {
            throw new Exception('Access denied');
        }
        $model->is_open = 1;
        $model->save();
    }

    public function actionupdateUserComment($hotlist_item_id, $comment) {
        $model = $this->loadModel($hotlist_item_id);

        if ($model->created_user_id !== Yii::app()->user->id) {
            throw new Exception('Access denied');
        }
        $model->user_comment = $comment;
        $model->save();
    }
}
