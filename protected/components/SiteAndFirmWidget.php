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

class SiteAndFirmWidget extends CWidget {

	public $title = 'Please confirm Site and Firm';
	
	public $returnUrl;

	public function init() {
		if(!$this->returnUrl) {
			$this->returnUrl = Yii::app()->request->url;
		}
	}

	public function run() {
		$model = new SiteAndFirmForm();
		if(isset($_POST['SiteAndFirmForm'])) {
			$model->attributes = $_POST['SiteAndFirmForm'];
			if($model->validate()) {
				$user = User::model()->findByPk(Yii::app()->user->id);
				$user->last_firm_id = $model->firm_id;
				$user->last_site_id = $model->site_id;
				if(!$user->save(false)) {
					throw new CException('Error saving user');
				}
				$user->audit('user', 'change-firm', $user->last_firm_id);
				
				Yii::app()->session['selected_site_id'] = $model->site_id;
				$this->controller->selectedSiteId = $model->site_id;
				Yii::app()->session['selected_firm_id'] = $model->firm_id;
				$this->controller->selectedFirmId = $model->firm_id;
				Yii::app()->session['confirm_site_and_firm'] = false;
				
				// TODO: Reset theatre and waiting list search options via event
				/*
					$so = Yii::app()->session['theatre_searchoptions'];
					if (isset($so['firm-id'])) unset($so['firm-id']);
					if (isset($so['specialty-id'])) unset($so['specialty-id']);
					if (isset($so['site-id'])) unset($so['site-id']);
					if (isset($so['date-filter'])) unset($so['date-filter']);
					if (isset($so['date-start'])) unset($so['date-start']);
					if (isset($so['date-end'])) unset($so['date-end']);
					Yii::app()->session['theatre_searchoptions'] = $so;
					Yii::app()->session['waitinglist_searchoptions'] = null;
				*/

				// Redirect browser to clear POST
				$this->controller->redirect($this->returnUrl);
				Yii::app()->end();
			}
		} else {
			$model->firm_id = Yii::app()->session['selected_firm_id'];
			$model->site_id = Yii::app()->session['selected_site_id'];
		}

		$sites = Site::model()->findAll(array(
				'condition' => 'institution.code = :institution_code',
				'join' => 'JOIN institution ON institution.id = t.institution_id',
				'order' => 'short_name',
				'params' => array(':institution_code' => 'RP6'),
		));

		$this->render('SiteAndFirmWidget', array(
				'model' => $model,
				'firms' => $this->controller->firms,
				'sites' => CHtml::listData($sites, 'id', 'short_name'),
		));
	}

}