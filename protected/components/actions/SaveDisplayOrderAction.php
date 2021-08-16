<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2019
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Saves the display_order.
 *
 * @throws CHttpException
 */
class SaveDisplayOrderAction extends \CAction
{
    public $model;
    public $modelName;

    public function run() {
        $model = $this->model;
        $modelName = is_null($this->modelName) ? get_class($model) : $this->modelName;

        if (!$model->hasAttribute('display_order')) {
            throw new CHttpException(400, 'This object cannot be ordered');
        }

        if (Yii::app()->request->isPostRequest) {
            $post = Yii::app()->request->getPost($modelName);
            $page = Yii::app()->request->getPost('page', 1);
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
                if (!$model->update(['display_order'])) {
                    throw new Exception('Unable to save order: ' . print_r($model->getErrors(), true));
                }
            }
        }
    }
}