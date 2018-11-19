<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class DefaultController extends \ModuleAdminController
{
    public $group = 'CVI';

    public $defaultAction = 'clinicalDisorderSection';

    public $layout = 'application.views.layouts.admin';

    /**
     * Admin for the sections that the disorders are separated into on the clinical info element.
     *
     * @throws \Exception
     */
    public function actionClinicalDisorderSection()
    {
        $this->genericAdmin(
            'Clinical Disorder Section',
            'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section',
            array(
                'extra_fields' => array(
                    array(
                        'field' => 'comments_allowed',
                        'type' => 'boolean',
                    ), array(
                        'field' => 'comments_label',
                        'type' => 'text'
                    )
                ),
                'div_wrapper_class' => 'cols-7',
            )
        );
    }

    /**
     * Admin for the disorder choices presented in the clinical element.
     */
    public function actionClinicalDisorders()
    {


        $this->genericAdmin(
            'Clinical Disorders',
            'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder',
            array(
                'new_row_url' => Yii::app()->createUrl('/OphCoCvi/admin/default/newClinicalDisorderRow'),
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
                ),
                'div_wrapper_class' => 'cols-9',
            )
        );
    }

    /**
     * To create the row with the search from model
     * @param $key
     */
    public function actionNewClinicalDisorderRow($key)
    {
        Yii::app()->clientScript->reset();

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
                    ), array(
                        'field' => 'comments_label',
                        'type' => 'text'
                    )
                ),
                'div_wrapper_class' => 'cols-9',
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
                ),
                'div_wrapper_class' => 'cols-5',
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
                ),
                'div_wrapper_class' => 'cols-5',
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
                ),
                'div_wrapper_class' => 'cols-5',
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
                ),
                'div_wrapper_class' => 'cols-5',
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
                ),
                'div_wrapper_class' => 'cols-5',
            )
        );
    }

}
