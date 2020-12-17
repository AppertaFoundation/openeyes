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
class AdminController extends ModuleAdminController
{
    public $group = 'Operation note';

    /**
     * Renders list of post op drugs.
     *
     * @throws Exception
     */
    public function actionViewPostOpDrugs()
    {
        $this->group = "Drugs";
        Audit::add(
            'admin',
            'list',
            null,
            null,
            array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationnote_PostopDrug')
        );

        $this->render('postopdrugs');
    }

    /**
     * Rendors incision length list.
     */
    public function actionViewIncisionLengthDefaults()
    {
        $this->group = 'Operation note';
        $this->render('incisionlengthdefaults');
    }

    /**
     * Renders the add form and accepts posts to update or add incision lengths.
     *
     * @param null $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionIncisionLengthDefaultAddForm($id = null)
    {
        $default = new OphTrOperationnote_CataractIncisionLengthDefault();
        if ($id !== null) {
            $default = OphTrOperationnote_CataractIncisionLengthDefault::model()->findByPk((int) $id);
        }
        $errors = array();

        if (!empty($_POST)) {
            $default->attributes = $_POST['OphTrOperationnote_CataractIncisionLengthDefault'];

            if (!$default->validate()) {
                $errors = $default->getErrors();
            } else {
                if (!$default->save()) {
                    throw new CHttpException(400, 'Unable to save drug: '.print_r($default->getErrors(), true));
                } else {
                    Audit::add('admin-OphTrOperationnote_IncisionLengthDefaults', 'add', $default->id);
                    $this->redirect('/OphTrOperationnote/admin/viewIncisionLengthDefaults');
                }
            }
        }

        $this->render('/admin/incisionlengthdefaultaddform', array(
            'default' => $default,
            'errors' => $errors,
        ));
    }

    /**
     * Allows a user to delete an incision length.
     *
     * @throws Exception
     */
    public function actionDeleteIncisionLengthDefaults()
    {
        $result = 1;
        if (is_array($_POST['incisionLengths'])) {
            foreach (OphTrOperationnote_CataractIncisionLengthDefault::model()->findAllByPk($_POST['incisionLengths']) as $incisionLength) {
                if (!$incisionLength->delete()) {
                    $result = 0;
                } else {
                    Audit::add(
                        'admin',
                        'delete',
                        $incisionLength->id,
                        null,
                        array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationnote_IncisionLengthDefault')
                    );
                }
            }
        }
        echo $result;
    }

    /**
     * Add a Post Op drug.
     *
     * @throws Exception
     */
    public function actionAddPostOpDrug()
    {
        $drug = new OphTrOperationnote_PostopDrug();

        if (!empty($_POST)) {
            $drug->attributes = $_POST['OphTrOperationnote_PostopDrug'];

            if (!$drug->validate()) {
                $errors = $drug->getErrors();
            } else {
                if (!$drug->save()) {
                    throw new Exception('Unable to save drug: '.print_r($drug->getErrors(), true));
                }
                Audit::add('admin-OphTrOperationnote_PostopDrug', 'add', $drug->id);
                $this->redirect('/OphTrOperationnote/admin/viewPostOpDrugs');
            }
        }

        $this->render('/admin/addpostopdrug', array(
            'drug' => $drug,
            'errors' => @$errors,
        ));
    }

    /**
     * Edit existing post op drug.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionEditPostOpDrug($id)
    {
        if (!$drug = OphTrOperationnote_PostopDrug::model()->findByPk($id)) {
            throw new Exception("Drug not found: $id");
        }

        if (!empty($_POST)) {
            $drug->attributes = $_POST['OphTrOperationnote_PostopDrug'];

            if (!$drug->validate()) {
                $errors = $drug->getErrors();
            } else {
                if (!$drug->save()) {
                    throw new Exception('Unable to save drug: '.print_r($drug->getErrors(), true));
                }

                Audit::add('admin-OphTrOperationnote_PostopDrug', 'edit', $id);

                $this->redirect('/OphTrOperationnote/admin/viewPostOpDrugs');
            }
        } else {
            Audit::add('admin-OphTrOperationnote_PostopDrug', 'view', $id);
        }

        $this->render('/admin/editpostopdrug', array(
            'drug' => $drug,
            'errors' => @$errors,
        ));
    }

    /**
     * Delete a post op drug.
     *
     * @throws Exception
     */
    public function actionDeletePostOpDrugs()
    {
        $result = 1;
        foreach (OphTrOperationnote_PostopDrug::model()->findAllByPk(@$_POST['drugs']) as $drug) {
            $drug->active = 0;
            if (!$drug->save()) {
                $result = 0;
            } else {
                Audit::add(
                    'admin',
                    'delete',
                    $drug->id,
                    null,
                    array('module' => 'OphTrOperationnote', 'model' => 'OphTrOperationnote_PostopDrug')
                );
            }
        }
        echo $result;
    }

    /**
     * Reorder post op drugs.
     *
     * @throws Exception
     */
    public function actionSortPostOpDrugs()
    {
        if (!empty($_POST['order'])) {
            foreach ($_POST['order'] as $i => $id) {
                if ($drug = OphTrOperationnote_PostopDrug::model()->findByPk($id)) {
                    $drug->display_order = $i + 1;
                    if (!$drug->save()) {
                        throw new Exception('Unable to save drug: '.print_r($drug->getErrors(), true));
                    }
                }
            }
        }
    }

    /**
     * Renders a form and accepts post for updating, editing and adding Post Op Instructions.
     *
     * @throws Exception
     */
    public function actionPostOpInstructions()
    {
        $this->group = 'Operation note';
        if (Yii::app()->request->isAjaxRequest) {
            if ( isset($_POST['action']) && $_POST['action'] == 'save') {
                if ( isset($_POST['id']) && $_POST['id'] != null ) {
                    $instruction = OphTrOperationnote_PostopInstruction::model()->findByPk($_POST['id']);
                } else {
                    $instruction = new OphTrOperationnote_PostopInstruction;
                }
                $instruction->content = $_POST['content'];
                $instruction->site_id = $_POST['site_id'];
                $instruction->subspecialty_id = $_POST['subspecialty_id'];

                $this->renderJSON(array(
                    'success' => (int)$instruction->save()
                ));
            } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
                $instruction = OphTrOperationnote_PostopInstruction::model()->findByPk($_POST['id']);

                $this->renderJSON(array(
                   'success' => (int)$instruction->delete()
                ));
            }
            Yii::app()->end();
        }

        $this->render('/admin/postOpInstructions/list', array(
            'instructions' => OphTrOperationnote_PostopInstruction::model()->findAll()
        ));
    }

    public function actionPostOpDrugMappings()
    {
        $this->genericAdmin('Per Op Drug Mappings', 'OphTrOperationnote_PostopSiteSubspecialtyDrug', array(
            'extra_fields' => array(
                array(
                    'field' => 'site_id',
                    'type' => 'lookup',
                    'model' => 'Site',
                ),
                array(
                    'field' => 'subspecialty_id',
                    'type' => 'lookup',
                    'model' => 'Subspecialty',
                ),
                array(
                    'field' => 'drug_id',
                    'type' => 'lookup',
                    'model' => 'OphTrOperationnote_PostopDrug',
                ),
            ),
        ));
    }
}
