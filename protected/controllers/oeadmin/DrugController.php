<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class DrugController.
 */
class DrugController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    public $group = 'Drugs';

    /**
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(Drug::model(), $this);
        $admin->setListFields(array(
            'name',
            'tags',
            'aliases',
            'active',
        ));
        $admin->getSearch()->addSearchItem('name, aliases');
        $admin->getSearch()->addSearchItem('tags.name');
        $admin->setModelDisplayName('Formulary Drugs');
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);

        $criteria = new CDbCriteria();

        foreach (array('name', 'name, aliases', 'active') as $field)
        {
            if(isset($_GET['search'][$field]) && $_GET['search'][$field] != '')
            {
                if(strpos($field, ', ') === false)
                {
                    // Single column fields
                    $criteria->compare('t.' . $field, $_GET['search'][$field], ($field != 'active'));
                }
                else
                {
                    // Combined fields
                    $crit2 = new CDbCriteria();

                    foreach (explode(', ', $field) as $column)
                    {
                        $crit2->compare('LOWER(t.'.$column.')', strtolower($_GET['search'][$field]), true, 'OR');
                    }

                    $criteria->mergeWith($crit2, 'AND');
                }
            }
        }

        if(isset($_GET['search']['tags.name']) && $_GET['search']['tags.name'] != '')
        {
            $command = Yii::app()->db->createCommand("SELECT drug_id FROM drug_tag WHERE tag_id IN (SELECT id FROM tag WHERE name LIKE CONCAT('%', :tagname ,'%'))");
            $matching_ids = $command->queryColumn(array(':tagname' => $_GET['search']['tags.name']));
            $criteria->addInCondition('t.id', $matching_ids, 'AND');
        }
        $criteria->with = array('tags');

        $admin->getSearch()->setCriteria($criteria);
        $admin->div_wrapper_class = 'cols-9';
        $admin->listModel();
    }

    /**
     * Edits or adds a Procedure.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        if ($id) {
            $drug = Drug::model()->findByPk($id);
        } else {
            $drug = new Drug();
        }

        $request = Yii::app()->getRequest();

        if ($request->getIsPostRequest()) {
            $drug->attributes = $_POST['Drug'];
            $drug['tags'] = $_POST['Drug']['tags'];
            $drug['allergies'] = $_POST['Drug']['allergies'];

            if (!$drug->save()) {
                throw new Exception('Unable to save drug: ' . print_r($drug->getErrors(), true));
            }

            Audit::add('admin-Drug', 'edit', $drug->id);
            $this->redirect('/oeadmin/drug/list');
        }

        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath);
        \Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath) . '/events_and_episodes.js');

        $this->render('/oeadmin/drug/edit', array(
            'model' => $drug,
        ));

    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(Drug::model(), $this);
        $admin->deleteModel();
    }

    public function actionTagsAutocomplete($term)
    {
        $tags = Tag::model()->findAllBySql("SELECT * FROM tag WHERE name LIKE CONCAT('%', :term, '%')", array(':term'=>$term));
        $tnames = array();
        foreach ($tags as $tag)
        {
            $tnames[] = $tag->name;
        }

        header('content-type: application/json');
        echo CJSON::encode($tnames);
    }
}
