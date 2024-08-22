<?php

/**
 * Class UserHotlistItemController
 */
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
                // Allow access by all authenticated users
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
     * @throws CHttpException Thrown if the model doesn't exist
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

    /**
     * @param string $date
     * @param int $is_open
     * @throws CException Thrown if the
     */
    public function actionRenderHotlistItems($is_open, $date = null)
    {
        $core_api = new CoreAPI();
        $institution_id = Institution::model()->getCurrent()->id;
        $site_id = Yii::app()->session['selected_site_id'];
        $display_primary_number_usage_code = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
        foreach (UserHotlistItem::model()->getHotlistItems($is_open, $date) as $hotlistItem) {
            echo $this->renderPartial('//base/_hotlist_item', [
                'hotlistItem' => $hotlistItem,
                'core_api' => $core_api,
                'institution_id' => $institution_id,
                'site_id' => $site_id,
                'display_primary_number_usage_code' => $display_primary_number_usage_code,
                ]);
        }
    }

    /**
     * Closes a hotlist item
     *
     * @param int $hotlist_item_id The ID of the hotlist item to closed
     * @throws Exception Thrown if an error occurs when saving the record
     * @throws CHttpException Thrown if the user doesn't have the privileges to access the ttem
     */
    public function actionCloseHotlistItem($hotlist_item_id)
    {
        $model = $this->loadModel($hotlist_item_id);

        if ($model->created_user_id !== Yii::app()->user->id) {
            throw new Exception('Access denied');
        }
        $model->is_open = 0;
        $model->save();
    }

    /**
     * Opens a hotlist item
     *
     * @param int $hotlist_item_id The ID of the hotlist item to open
     * @throws Exception Thrown if an error occurs when saving the record
     * @throws CHttpException Thrown if the user doesn't have the privileges to access the ttem
     */
    public function actionOpenHotlistItem($hotlist_item_id)
    {
        $model = $this->loadModel($hotlist_item_id);

        if ($model->created_user_id !== Yii::app()->user->id) {
            throw new CHttpException(403, 'Access denied');
        }

        if ($model->is_open) {
            return;
        }

        if ($model->wasUpdatedToday()) {
            $model->is_open = 1;
            if (!$model->save()) {
                throw new Exception('The hotlist item could not be saved ' . print_r($model->errors, true));
            }
        } else {
            $new_item = new UserHotlistItem();
            $new_item->patient_id = $model->patient_id;
            $new_item->user_comment = $model->user_comment;
            $new_item->is_open = 1;
            if (!$new_item->save()) {
                throw new Exception('New hotlist item could not be saved ' . print_r($new_item->errors, true));
            }
        }
    }

    /**
     * Updates the comment of a hotlist item to the given value
     *
     * @param int $hotlist_item_id The id of the hotlist item to update
     * @param string $comment The text to set the comment to
     * @throws Exception Thrown if an error occurs when saving the record
     * @throws CHttpException Thrown if the user doesn't have privileges to access the item
     */
    public function actionUpdateUserComment($hotlist_item_id, $comment)
    {
        $model = $this->loadModel($hotlist_item_id);

        if ($model->created_user_id !== Yii::app()->user->id) {
            throw new CHttpException(403, 'Access denied');
        }
        $model->user_comment = $comment;
        $model->save();
    }
}
