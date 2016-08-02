<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
namespace OEModule\OphCoCvi\controllers;
use Yii;
use Audit;
use CDbCriteria;
use OEModule\OphCoCvi\models;
class AdminController extends \ModuleAdminController
{
	public $defaultAction = "clinicalDisorderSection";

        // -- Clinical section for disorder actions --
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
					),array(
						'field' => 'comments_label',
						'type' => 'text'
					)
				)
			)
		);
	}
	// -- Clinical disorder actions --
        public function actionClinicalDisorders()
	{
		$this->genericAdmin(
			'Clinical Disorders',
			'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorders',
			array(
				'extra_fields' => array(
					array(
						'field' => 'code',
						'type' => 'text'
					),array(
						'field' => 'section_id',
						'type' => 'lookup',
						'model' => 'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section',
					)
				)
			)
		);
	}
        // -- Patient factor actions --
        
        public function actionPatientFactor()
	{
		$this->genericAdmin(
			'Patient Factor',
			'OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_PatientFactor',
			array(
				'extra_fields' => array(
                                        array(
						'field' => 'code',
						'type' => 'text'
					),
					array(
						'field' => 'require_comments',
						'type' => 'boolean',
					),array(
						'field' => 'comments_label',
						'type' => 'text'
					)
				)
			)
		);
	}
        // -- Employeement status actions --
        public function actionEmployeementStatus()
	{
		$this->genericAdmin(
			'Clinical Disorders',
			'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_EmploymentStatus',
			array(
				'extra_fields' => array(
					array(
						'field' => 'child_default',
						'type' => 'boolean'
					),array(
						'field' => 'social_history_occupation_id',
						'type' => 'lookup',
						'model' => 'SocialHistoryOccupation',
					)
				)
			)
		);
	}
        
        // -- Contact urgency for Generic type lookup --
        public function actionContactUrgency()
	{
		$this->genericAdmin('Contact Urgency', 
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
        
        // -- Field of vision for Generic type lookup --
        public function actionFieldOfVision()
	{
		$this->genericAdmin('Field of Vision',
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
        
        // -- Low vision status for Generic type lookup --
        public function actionLowVisionStatus()
	{
		$this->genericAdmin('Low Vision Status',
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
        
        // -- Preferred info format --
        public function actionPreferredInfoFormat()
	{
		$this->genericAdmin('Preferred Info Format',
                        'OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt',
                        array(
				'extra_fields' => array(
                                        array(
						'field' => 'require_email',
						'type' => 'boolean'
					)
				)
                        )
                                    
                );
        }
        
}
