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

class SiteAndFirmWidget extends CWidget
{
	public $title = 'Please confirm Site and Firm';
	public $subspecialty;
	public $support_services;
	public $patient;
	public $returnUrl;

	public function init()
	{
		if (!$this->returnUrl) {
			$this->returnUrl = Yii::app()->request->url;
		}
	}

	public function run()
	{
		$model = new SiteAndFirmForm();
		$user = User::model()->findByPk(Yii::app()->user->id);
		if (isset($_POST['SiteAndFirmForm'])) {
			$model->attributes = $_POST['SiteAndFirmForm'];
			if ($model->validate()) {
				$user->changeFirm($model->firm_id);
				$user->last_site_id = $model->site_id;
				if (!$user->save(false)) {
					throw new CException('Error saving user');
				}
				$user->audit('user', 'change-firm', $user->last_firm_id);

				Yii::app()->session['selected_site_id'] = $model->site_id;
				$this->controller->selectedSiteId = $model->site_id;
				Yii::app()->session['selected_firm_id'] = $model->firm_id;
				$this->controller->selectedFirmId = $model->firm_id;
				Yii::app()->session['confirm_site_and_firm'] = false;

				Yii::app()->event->dispatch('firm_changed', array('firm_id' => $model->firm_id));

				$firm = Firm::model()->findByPk($model->firm_id);
				$subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

				if ($this->patient && $episode = $this->patient->getOpenEpisodeOfSubspecialty($subspecialty_id)) {
					Yii::app()->session['episode_hide_status'] = array(
						$episode->id => 1,
					);
				}

				// Redirect browser to clear POST
				$this->controller->redirect($this->returnUrl);
				Yii::app()->end();
			}
		} else {
			$model->firm_id = Yii::app()->session['selected_firm_id'];
			$model->site_id = Yii::app()->session['selected_site_id'];
		}

		if (!$sites = $user->siteSelections) {
			$sites = Institution::model()->getCurrent()->sites;
		}

		$user_firm_ids = array();
		if (Yii::app()->params['profile_user_can_edit']) {
			// firm selections only apply when the user is able to change them.
			// if firms should be restricted then this should be done through UserFirmRights
			foreach ($user->firmSelections as $firm) {
				$user_firm_ids[] = $firm->id;
			}
		}

		$firms = array();
		if ($preferred_firms = $user->preferred_firms) {
			foreach ($preferred_firms as $preferred_firm) {
				if (empty($user_firm_ids) || in_array($preferred_firm->id,$user_firm_ids)) {
					if (!$this->subspecialty || ($preferred_firm->serviceSubspecialtyAssignment && $this->subspecialty->id == $preferred_firm->serviceSubspecialtyAssignment->subspecialty_id)) {
						if ($preferred_firm->serviceSubspecialtyAssignment) {
							$firms['Recent'][$preferred_firm->id] = "$preferred_firm->name ({$preferred_firm->serviceSubspecialtyAssignment->subspecialty->name})";
						} else {
							$firms['Recent'][$preferred_firm->id] = "$preferred_firm->name";
						}
					}
				}
			}
		}

		foreach ($this->controller->firms as $firm_id => $firm_label) {
			if (empty($user_firm_ids) || in_array($firm_id,$user_firm_ids)) {
				if (!isset($firms['Recent'][$firm_id])) {
					$firm = Firm::model()->findByPk($firm_id);
					if (!$this->subspecialty || ($firm->serviceSubspecialtyAssignment && $firm->serviceSubspecialtyAssignment->subspecialty_id == $this->subspecialty->id)) {
						if ($preferred_firms) {
							$firms['Other'][$firm_id] = $firm_label;
						} else {
							$firms[$firm_id] = $firm_label;
						}
					}
				}
			}
		}

		$this->render('SiteAndFirmWidget', array(
				'model' => $model,
				'firms' => $firms,
				'sites' => CHtml::listData($sites, 'id', 'short_name'),
		));
	}
}
