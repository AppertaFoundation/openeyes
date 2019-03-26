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
class AdminController extends \ModuleAdminController
{
    public $group = 'Correspondence';

    public $defaultAction = 'letterMacros';

    public function actions() {
        return [
            'sort' => [
                'class' => 'SaveDisplayOrderAction',
                'model' => LetterMacro::model(),
                ],
        ];
    }

    public function actionLetterMacros()
    {
        $macros = $this->getMacros();

        Audit::add('admin', 'list', null, null, array('module' => 'OphCoCorrespondence', 'model' => 'LetterMacro'));

        $unique_names = CHtml::listData($macros, 'name', 'name');
        asort($unique_names);


        $assetManager = Yii::app()->getAssetManager();
        $assetManager->registerScriptFile('/js/oeadmin/OpenEyes.admin.js');
        $assetManager->registerScriptFile('/js/oeadmin/list.js');

        $this->render('letter_macros', array(
            'macros' => $macros,
            'unique_names' => $unique_names,
            'episode_statuses' => $this->getUniqueEpisodeStatuses($macros),
        ));
    }


    public function actionLetterSettings()
    {
        $this->render('/admin/letter_settings',array(
            'settings' => OphCoCorrespondenceLetterSettings::model()->findAll(),
        ));
    }

    public function actionEditSetting()
    {
        if (!$metadata = OphCoCorrespondenceLetterSettings::model()->find('`key`=?', array(@$_GET['key']))) {
            $this->redirect(array('/OphCoCorrespondence/admin/letterSettings/settings'));
        }

        $errors = array();

        if (Yii::app()->request->isPostRequest) {
            foreach (OphCoCorrespondenceLetterSettings::model()->findAll() as $metadata) {
                if (@$_POST['hidden_' . $metadata->key] || @$_POST[$metadata->key]) {
                    if (!$setting = $metadata->getSetting($metadata->key, null, true)) {
                        $setting = new OphCoCorrespondenceLetterSettingValue();
                        $setting->key = $metadata->key;
                    }
                    $setting->value = @$_POST[$metadata->key];
                    if (!$setting->save()) {
                        $errors = $setting->errors;
                    } else {
                        $this->redirect(array('/OphCoCorrespondence/admin/letterSettings/settings'));
                    }
                }
            }
        }

        $this->render(
            '/admin/edit_setting',
            [
                'metadata' => $metadata,
                'errors' => $errors,
                'cancel_uri' => '/OphCoCorrespondence/admin/letterSettings/settings',
            ]
        );
    }


    public function getUniqueEpisodeStatuses($macros)
    {
        $statuses = array();

        foreach ($macros as $macro) {
            if ($macro->episode_status_id && !isset($statuses[$macro->episode_status_id])) {
                $statuses[$macro->episode_status_id] = $macro->episode_status->name;
            }
        }

        ksort($statuses);

        return $statuses;
    }

    public function actionFilterMacros()
    {
        $this->renderPartial('_macros', array('macros' => $this->getMacros()));
    }

    public function actionFilterMacroNames()
    {
        $macros = $this->getMacros(false);

        $unique_names = CHtml::listData($macros, 'name', 'name');
        asort($unique_names);

        $this->renderPartial('_macro_names', array('names' => $unique_names));
    }

    public function actionFilterEpisodeStatuses()
    {
        $this->renderPartial('_episode_statuses', array('statuses' => $this->getUniqueEpisodeStatuses($this->getMacros(false))));
    }

    public function getMacros($filter_name_and_episode_status = true)
    {
        $criteria = new CDbCriteria();

        if (@$_GET['type'] == 'site') {
            $criteria->addCondition('site_id is not null');
        }
        if (@$_GET['type'] == 'subspecialty') {
            $criteria->addCondition('subspecialty_id is not null');
        }
        if (@$_GET['type'] == 'firm') {
            $criteria->addCondition('firm_id is not null');
        }

        if (@$_GET['site_id']) {
            $criteria->addCondition('site_id = :site_id');
            $criteria->params[':site_id'] = $_GET['site_id'];
        }

        if (@$_GET['subspecialty_id']) {
            $criteria->addCondition('subspecialty_id = :subspecialty_id');
            $criteria->params[':subspecialty_id'] = $_GET['subspecialty_id'];
        }

        if (@$_GET['firm_id']) {
            $criteria->addCondition('firm_id = :firm_id');
            $criteria->params[':firm_id'] = $_GET['firm_id'];
        }

        if ($filter_name_and_episode_status) {
            if (@$_GET['name']) {
                $criteria->addCondition('name = :name');
                $criteria->params[':name'] = $_GET['name'];
            }

            if (@$_GET['episode_status_id']) {
                $criteria->addCondition('episode_status_id = :esi');
                $criteria->params[':esi'] = $_GET['episode_status_id'];
            }
        }

        $criteria->order = 'display_order asc, site_id asc, subspecialty_id asc, firm_id asc, name asc';

        return LetterMacro::model()->findAll($criteria);
    }

    public function actionAddMacro()
    {
        $macro = new LetterMacro();

        $errors = array();

        if (!empty($_POST)) {
            $macro->attributes = $_POST['LetterMacro'];

            if (!$macro->validate()) {
                $errors = $macro->errors;
            } else {
                if (!$macro->save()) {
                    throw new Exception('Unable to save macro: '.print_r($macro->errors, true));
                }

                Audit::add('admin', 'create', $macro->id, null, array('module' => 'OphCoCorrespondence', 'model' => 'LetterMacro'));

                $this->redirect('/OphCoCorrespondence/admin/letterMacros');
            }
        } else {
            Audit::add('admin', 'view', $macro->id, null, array('module' => 'OphCoCorrespondence', 'model' => 'LetterMacro'));
        }

        $init_method = new OphcorrespondenceInitMethod();

        $this->render('_macro', array(
            'macro' => $macro,
            'init_method' => $init_method,
            'associated_content' => array(),
            'errors' => $errors,
        ));
    }

    public function actionEditMacro($id)
    {
        if (!$macro = LetterMacro::model()->findByPk($id)) {
            throw new Exception("LetterMacro not found: $id");
        }

        $init_method = new OphcorrespondenceInitMethod();

        $criteria = new \CDbCriteria();
        $criteria->addCondition('macro_id = '.$id);
        $criteria->order = 'display_order asc';
        $associated_content_saved = MacroInitAssociatedContent::model()->findAll($criteria);

        $errors = array();

        if (!empty($_POST)) {

            $macro->attributes = $_POST['LetterMacro'];

            if (!$macro->validate()) {
                $errors = $macro->errors;
            } else {
                if (!$macro->save()) {
                    throw new Exception('Unable to save macro: '.print_r($macro->errors, true));
                }

                Audit::add('admin', 'update', $macro->id, null, array('module' => 'OphCoCorrespondence', 'model' => 'LetterMacro'));

                $this->redirect('/OphCoCorrespondence/admin/letterMacros');
            }
        } else {
            Audit::add('admin', 'view', $macro->id, null, array('module' => 'OphCoCorrespondence', 'model' => 'LetterMacro'));
        }

        $this->render('_macro', array(
            'macro' => $macro,
            'init_method' => $init_method,
            'associated_content' => $associated_content_saved,
            'errors' => $errors,
        ));
    }

    public function actionDeleteLetterMacros()
    {
        if(!isset($_POST['id'])) {
            return null;
        }
        //Make all the macro ids null that is equal to the macro id
        // that is being deleted in the document instance data table
        DocumentInstanceData::model()->updateAll(['macro_id' => null], 'macro_id IN (' . implode($_POST['id']) . ')');

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', @$_POST['id']);
        if (LetterMacro::model()->deleteAll($criteria)) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function actionAddSiteSecretary($id = null)
    {
        $firmId = $id;
        $siteSecretaries = array();
        $errors = array();
        if ($firmId === null && isset(Yii::app()->session['selected_firm_id'])) {
            $firmId = Yii::app()->session['selected_firm_id'];
        }
        $errorList = array();
        if (Yii::app()->request->isPostRequest) {
            foreach ($_POST['FirmSiteSecretary'] as $i => $siteSecretaryPost) {
                if (empty($siteSecretaryPost['site_id']) && empty($siteSecretaryPost['direct_line']) &&  empty($siteSecretaryPost['fax'])) {
                    //The entire row is empty, ignore it
                    $errorList[] = array('You must supply at least a Site and Direct Line');
                    continue;
                }

                //Are we updating an existing object
                if ($siteSecretaryPost['id'] !== '') {
                    $siteSecretary = FirmSiteSecretary::model()->findByPk($siteSecretaryPost['id']);
                } else {
                    $siteSecretary = new FirmSiteSecretary();
                }
                //Set to have posted attributes
                $siteSecretary->attributes = $siteSecretaryPost;

                if (!$siteSecretary->firm_id) {
                    $siteSecretary->firm_id = (int) $firmId;
                }
                if (!$siteSecretary->validate()) {
                    $errorList[] = $siteSecretary->getErrors();
                } else {
                    if (!$siteSecretary->save()) {
                        throw new CHttpException(500, 'Unable to save Site Secretary: '.$siteSecretary->site->name);
                    }
                }
                //Add to array so updated version can be rendered
                $siteSecretaries[] = $siteSecretary;
            }
        } else {
            //Find all of the contacts for the current firm
            $siteSecretary = new FirmSiteSecretary();
            $siteSecretaries = $siteSecretary->findSiteSecretaryForFirm($firmId);
        }
        //Add a blank one to the end of the form for adding
        $siteSecretaries[] = new FirmSiteSecretary();
        if (count($errorList)) {
            $errors = call_user_func_array('array_merge', $errorList);
        }

        $outputArray = array(
            'siteSecretaries' => $siteSecretaries,
            'errors' => $errors,
            'success' => (count($errors) === 0),
        );

        if (Yii::app()->request->isAjaxRequest) {
            if (!$outputArray['success']) {
                $outputArray['errors'] = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($outputArray['errors'])), false);
            }
            $this->renderJSON($outputArray);
        } else {
            $this->render('/admin/secretary/edit', $outputArray);
        }
    }

    /**
     * Deletes a site secretary.
     *
     * @throws CHttpException
     */
    public function actionDeleteSiteSecretary()
    {
        if (Yii::app()->request->isPostRequest) {
            if (!isset($_POST['id'])) {
                throw new CHttpException(400, 'Unable to delete Site Secretary: no ID provided');
            }
            $siteSecretary = FirmSiteSecretary::model()->findByPk($_POST['id']);
            if (!$siteSecretary) {
                throw new CHttpException(404, 'Unable to delete Site Secretary: Can not find Site Secretary');
            }
            $firmId = $siteSecretary->firm_id;
            $siteSecretary->delete();
            $this->redirect('/OphCoCorrespondence/admin/addSiteSecretary/'.$firmId);
        }
        throw new CHttpException(400, 'Invalid method for delete');
    }

    /*
     * Get init method's data by id
     */
    public function actionGetInitMethodDataById()
    {

        if (Yii::app()->request->isAjaxRequest ) {
            if (!isset($_POST['id'])) {
                throw new CHttpException(400, 'No ID provided');
            }
            if (!$method = OphcorrespondenceInitMethod::model()->findByPk($_POST['id'])) {
                throw new Exception("Method not found: ".$_POST['id']);
            }

            $result = array(
                'success'       => 1,
                'description'   => $method->description,
                'short_code'    => $method->short_code
            );

            $this->renderJSON($result);
        }
        throw new CHttpException(400, 'Invalid method');
    }
}
