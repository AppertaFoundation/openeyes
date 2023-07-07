<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\controllers;

use Exception;
use Yii;
use CDbCriteria;
use OEModule\OphCoCvi\models;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section;
use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder;
use OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PatientFactor;
use CHtml;
use ModelSearch;
use Audit;

class AdminController extends \ModuleAdminController
{
    public $defaultAction = 'clinicalDisorderSection';
    public $displayOrder = 0;
    public $group = "CVI";

    /**
     * Admin for the disorder choices presented in the clinical element.
     */
    public function actionClinicalDisorders()
    {
        Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder', 'list');
        $search = \Yii::app()->request->getParam('search', [
            'patient_type' => null,
            'version' => null
        ]);

        $criteria = new \CDbCriteria();
        if (isset($search['patient_type'])) {
            $criteria->addCondition('patient_type =:patient_type');
            $criteria->params[':patient_type'] = $search['patient_type'];
        }
        $disorders = OphCoCvi_ClinicalInfo_Disorder::model()->findAll($criteria);

        $this->render('/default/clinical_disorders', array(
            'search' => $search,
            'patient_types' => [
                OphCoCvi_ClinicalInfo_Disorder::PATIENT_TYPE_ADULT => 'Diagnosis for patients 18 years of age or over',
                OphCoCvi_ClinicalInfo_Disorder::PATIENT_TYPE_CHILD => 'Diagnosis for patients under the age of 18',
            ],
            'disorders' => $disorders,
        ));
    }

    public function actionAddClinicalDisorder($event_type_version = null, $patient_type = null)
    {
        $disorder = new OphCoCvi_ClinicalInfo_Disorder();

        if (!empty($_POST)) {
            $disorder->attributes = $_POST['OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder'];
            if ($event_type_version) {
                $disorder->event_type_version = $event_type_version;
            } else {
                $maxVersion = OphCoCvi_ClinicalInfo_Disorder::model()->findBySql('SELECT event_type_version FROM ophcocvi_clinicinfo_disorder_section ORDER BY event_type_version DESC LIMIT 1');
                $disorder->event_type_version = $maxVersion->event_type_version;
            }
            if ($patient_type != '') {
                $disorder->patient_type = $patient_type;
            }
            $maxDisplayOrder = OphCoCvi_ClinicalInfo_Disorder::model()->findBySql('SELECT display_order FROM ophcocvi_clinicinfo_disorder_section ORDER BY display_order DESC LIMIT 1');
            $disorder->display_order = $maxDisplayOrder->display_order+1;

            if (!$disorder->validate()) {
                $errors = $disorder->getErrors();
            } else {
                if (!$disorder->save()) {
                    throw new Exception('Unable to save Clinical Disorder: ' . print_r($disorder->getErrors(), true));
                }
                Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder', 'add', $disorder->id);

                if (!is_null($event_type_version) && $patient_type != '') {
                    $this->redirect('/OphCoCvi/admin/clinicalDisorders/' . ceil($disorder->id / $this->items_per_page). '?search[event_type_version]='.$event_type_version.'&search[patient_type]='.$patient_type);
                } else {
                    $this->redirect('/OphCoCvi/admin/clinicalDisorders/' . ceil($disorder->id / $this->items_per_page));
                }
            }
        }

        $this->render('/default/edit_clinical_disorder', array(
            'disorder' => $disorder,
            'errors' => @$errors,
        ));
    }

    public function actionEditClinicalDisorder($id)
    {

        if (!$disorder = OphCoCvi_ClinicalInfo_Disorder::model()->findByPk($id)) {
            throw new Exception("Section not found: $id");
        }

        if (!empty($_POST)) {
            $disorder->attributes = $_POST['OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder'];
            if (!$disorder->validate()) {
                $errors = $disorder->getErrors();
            } else {
                if (!$disorder->save()) {
                    throw new Exception('Unable to save section: ' . print_r($disorder->getErrors(), true));
                }

                Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder', 'edit', $disorder->id);
                $this->redirect('/OphCoCvi/admin/clinicalDisorders/' . ceil($disorder->id / $this->items_per_page) . '?search[patient_type]='.$disorder->patient_type);
            }
        } else {
            Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder', 'view', $id);
        }

        $this->render('/default/edit_clinical_disorder', array(
            'disorder' => $disorder,
            'errors' => @$errors,
        ));
    }

    /**
     * Lists all disorders for a given search term.
     */
    public function actionCilinicalDisorderAutocomplete($term)
    {
        $search = "%{$term}%";
        $where = '(term like :search or id like :search)';
        $where .= ' and active = 1';
        $disorders = \Yii::app()->db->createCommand()
            ->select('id, term AS value, term AS label')
            ->from('disorder')
            ->where($where, array(
                ':search' => $search,
            ))
            ->order('term')
            ->queryAll();

        $this->renderJSON($disorders);
    }

    /**
     * Admin for the sections that the disorders are separated into on the clinical info element.
     *
     * @throws \Exception
     */
    public function actionClinicalDisorderSection()
    {
        $search = \Yii::app()->request->getParam('search', [
            'patient_type' => null,
            'version' => null
        ]);

        Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder_Section', 'list');
        $criteria = new \CDbCriteria();

        if (isset($search['version'])) {
            $criteria->addCondition('event_type_version = :version');
            $criteria->params[':version'] = $search['version'];
        }

        if (isset($search['patient_type'])) {
            $criteria->addCondition('patient_type =:patient_type');
            $criteria->params[':patient_type'] = $search['patient_type'];
        }

        $disorder_sections = OphCoCvi_ClinicalInfo_Disorder_Section::model()->findAll($criteria);

        $this->render('/default/clinical_disorder_section', array(
            'search' => $search,
            'patient_types' => [
                OphCoCvi_ClinicalInfo_Disorder::PATIENT_TYPE_ADULT => 'Diagnosis for patients 18 years of age or over',
                OphCoCvi_ClinicalInfo_Disorder::PATIENT_TYPE_CHILD => 'Diagnosis for patients under the age of 18',
            ],
            'disorder_sections' => $disorder_sections,
        ));
    }

    public function actionAddClinicalDisorderSection($patient_type = null)
    {
        $section = new OphCoCvi_ClinicalInfo_Disorder_Section();

        if (!empty($_POST)) {
            $section->attributes = $_POST['OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder_Section'];
            if ($patient_type) {
                $section->patient_type = $patient_type;
            }
            $maxDisplayOrder = OphCoCvi_ClinicalInfo_Disorder_Section::model()->find(
                array(
                    "condition" => 'display_order = (SELECT MAX(display_order) FROM ophcocvi_clinicinfo_disorder_section)',
                ));
            $section->display_order = $maxDisplayOrder->display_order+1;

            if (!$section->validate()) {
                $errors = $section->getErrors();
            } else {
                if (!$section->save()) {
                    throw new Exception('Unable to save Clinical Disorder: ' . print_r($section->getErrors(), true));
                }
                Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder_Section', 'add', $section->id);

                if ($patient_type) {
                    $this->redirect('/OphCoCvi/admin/clinicalDisorderSection/' . ceil($section->id / $this->items_per_page). '?&search[patient_type]='.$patient_type);
                } else {
                    $this->redirect('/OphCoCvi/admin/clinicalDisorderSection/' . ceil($section->id / $this->items_per_page));
                }
            }
        }

        $this->render('/default/edit_clinical_disorder_section', array(
            'section' => $section,
            'errors' => @$errors,
        ));
    }

    public function actionEditClinicalDisorderSection($id)
    {

        if (!$section = OphCoCvi_ClinicalInfo_Disorder_Section::model()->findByPk($id)) {
            throw new Exception("Section not found: $id");
        }

        if (!empty($_POST)) {
            $section->attributes = $_POST['OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder_Section'];

            if (!$section->validate()) {
                $errors = $section->getErrors();
            } else {
                if (!$section->save()) {
                    throw new Exception('Unable to save section: ' . print_r($section->getErrors(), true));
                }

                Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder_Section', 'edit', $section->id);
                $this->redirect('/OphCoCvi/admin/clinicalDisorderSection/' . ceil($section->id / $this->items_per_page) . '?search[patient_type]='.$section->patient_type);
            }
        } else {
            Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder_Section', 'view', $id);
        }

        $this->render('/default/edit_clinical_disorder_section', array(
            'section' => $section,
            'errors' => @$errors,
        ));
    }


    public function actionDeleteClinicalDisorderSection()
    {
        $result = 1;

        if (!empty($_POST['sections'])) {
            foreach (OphCoCvi_ClinicalInfo_Disorder_Section::model()->findAllByPk($_POST['sections']) as $section) {
                try {
                    $section_id = $section->id;
                    if (!$section->delete()) {
                        $result = 0;
                    } else {
                        Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder_Section', 'delete', $section_id);
                    }
                } catch (Exception $e) {
                    $result = 0;
                }
            }
        }

        echo $result;
    }

    public function actionDeleteClinicalDisorders()
    {
        $result = 1;

        if (!empty($_POST['disorders'])) {
            foreach (OphCoCvi_ClinicalInfo_Disorder::model()->findAllByPk($_POST['disorders']) as $disorder) {
                try {
                    $disorder_id = $disorder->id;
                    if (!$disorder->delete()) {
                        $result = 0;
                    } else {
                        Audit::add('admin-OphCoCvi_ClinicalInfo_Disorder', 'delete', $disorder_id);
                    }
                } catch (Exception $e) {
                    $result = 0;
                }
            }
        }

        echo $result;
    }

    /**
     * To create the row with the search from model
     * @param $key
     */
    public function actionNewClinicalDisorderRow($key)
    {
        $this->genericAdmin(
            'Clinical Disorders',
            'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
            array(
                'new_row_url' => Yii::app()->createUrl('/OphCoCvi/admin/newClinicalDisorderRow'),
                'extra_fields' => array(
                    array(
                        'field' => 'code',
                        'type' => 'text'
                    ), array(
                        'field' => 'section_id',
                        'type' => 'lookup',
                        'model' => 'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section',
                    ),
                    array(
                        'field' => 'disorder_id',
                        'relation' => 'disorder',
                        'type' => 'search_lookup',
                        'model' => '\Disorder',
                    )
                )
            ),
            $key
        );
    }

    /**
     * Admin for the patient factor questions on the clinical info element.
     *
     * @throws \Exception
     */
    public function actionPatientFactor()
    {
        $this->genericAdmin(
            'Patient Factor',
            'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PatientFactor',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'code',
                        'type' => 'text'
                    ),
                    array(
                        'field' => 'require_comments',
                        'type' => 'boolean',
                    ),
                    array(
                        'field' => 'comments_label',
                        'type' => 'text'
                    ),
                    array(
                        'field' => 'comments_only',
                        'type' => 'boolean',
                    ),
                    array(
                        'field' => 'yes_no_only',
                        'type' => 'boolean',
                    )
                )
            )
        );
    }

    /**
     * Admin for the employment status lookup on the clerical info element.
     *
     * @throws \Exception
     */
    public function actionEmploymentStatus()
    {
        $this->genericAdmin(
            'Employment Status',
            'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_EmploymentStatus',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'child_default',
                        'type' => 'boolean'
                    ), array(
                        'field' => 'social_history_occupation_id',
                        'type' => 'lookup',
                        'model' => 'OEModule\OphCiExamination\models\SocialHistoryOccupation',
                    )
                )
            )
        );
    }

    /**
     * Admin for contact urgency options on clerical info.
     *
     * @throws \Exception
     */
    public function actionContactUrgency()
    {
        $this->genericAdmin(
            'Contact Urgency',
            'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_ContactUrgency',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'code',
                        'type' => 'text'
                    )
                )
            )
        );
    }

    /**
     * Admin for field of vision options on clinical info element.
     *
     * @throws \Exception
     */
    public function actionFieldOfVision()
    {
        $this->genericAdmin(
            'Field of Vision',
            'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_FieldOfVision',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'code',
                        'type' => 'text'
                    )
                )
            )
        );
    }

    /**
     * Admin for low vision status lookup on clinical info element.
     *
     * @throws \Exception
     */
    public function actionLowVisionStatus()
    {
        $this->genericAdmin(
            'Low Vision Status',
            'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_LowVisionStatus',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'code',
                        'type' => 'text'
                    )
                )
            )
        );
    }

    /**
     * Admin for information format options in clerical info element.
     *
     * @throws \Exception
     */
    public function actionPreferredInfoFormat()
    {
        $this->genericAdmin(
            'Preferred Info Format',
            'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'code',
                        'type' => 'text'
                    ),
                    array(
                        'field' => 'require_email',
                        'type' => 'boolean'
                    )
                )
            )
        );
    }

    public function actionFirmAutoComplete($term, $subspecialty_id = null)
    {
        $res = array();
        if (\Yii::app()->request->isAjaxRequest && !empty($term)) {
            $command = Yii::app()->db->createCommand()
                ->select('f.id, f.name, s.name AS subspecialty')
                ->from('firm f')
                ->join('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
                ->join('subspecialty s', 'ssa.subspecialty_id = s.id')
                ->where('f.active = 1 AND LOWER(f.name) LIKE "%' . strtolower($term) . '%"');

            if ($subspecialty_id) {
                $command->andWhere('s.id = :id', array(':id' => $subspecialty_id));
            }

            $firms = $command->order('f.name, s.name')->queryAll();

            $data = array();
            foreach ($firms as $firm) {
                $display = $firm['name'];
                if ($firm['subspecialty']) {
                    $display .= ' (' . $firm['subspecialty'] . ')';
                }
                $data[$firm['id']] = $display;
            }
            natcasesort($data);

            foreach ($data as $key => $firm) {
                $res[] = array(
                    'id' => $key,
                    'label' => $firm,
                    'value' => $firm,
                    'username' => $firm,
                );
            }
        }
        $this->renderJSON($res);
    }
}
