<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 25/03/2019
 * Time: 16:56
 */

/**
 * Saves the display_order.
 *
 * @throws CHttpException
 */

/**
 * Save ordering of the objects.
 */
class SaveDisplayOrderAction extends \CAction
{
    public $model;

    public function run() {
        $model = $this->model;
        $modelName = get_class($model);

        if (!$model->hasAttribute('display_order')) {
            throw new CHttpException(400, 'This object cannot be ordered');
        }

        if (Yii::app()->request->isPostRequest) {
            $post = Yii::app()->request->getPost($modelName);
            $page = Yii::app()->request->getPost('page');
            if (!array_key_exists('display_order', $post) || !is_array($post['display_order'])) {
                throw new CHttpException(400, 'No objects to order were provided');
            }

            foreach ($post['display_order'] as $displayOrder => $id) {
                $model = $model->findByPk($id);
                if (!$model) {
                    throw new CHttpException(400, 'Object to be ordered not found');
                }
                //Add one because display_order not zero indexed.
                //Times by page number to get correct order across pages.
                $model->display_order = ($displayOrder + 1) * $page;
                if (!$model->validate()) {
                    throw new CHttpException(400, 'Order was invalid');
                }
                if (!$model->save()) {
                    throw new CHttpException(500, 'Unable to save order');
                }
            }
        }
    }
}