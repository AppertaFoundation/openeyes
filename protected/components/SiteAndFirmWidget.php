<?php

/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class SiteAndFirmWidget extends CWidget
{
    public $title;
    public $subspecialty;
    public $support_services;
    public $patient;
    public $returnUrl;

    /*
     * 'popup' or 'static'
     */
    public $mode = 'popup';

    public function init()
    {
        $this->title = $this->title ? : 'Please confirm Site and ' . Firm::contextLabel();
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

                if (!\Yii::app()->request->isAjaxRequest) {
                    // Redirect browser to clear POST
                    $this->controller->redirect($this->returnUrl);
                } else {
                    echo 1;
                }

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
                if (
                    $preferred_firm->active && $preferred_firm->runtime_selectable &&
                    (count($user_firm_ids) === 0 || in_array($preferred_firm->id, $user_firm_ids)) &&
                    (!$this->subspecialty || ($preferred_firm->serviceSubspecialtyAssignment && $this->subspecialty->id == $preferred_firm->serviceSubspecialtyAssignment->subspecialty_id))
                ) {
                    if ($preferred_firm->serviceSubspecialtyAssignment) {
                        $firms['Recent'][$preferred_firm->id] = "$preferred_firm->name ({$preferred_firm->serviceSubspecialtyAssignment->subspecialty->name})";
                    } else {
                        $firms['Recent'][$preferred_firm->id] = "$preferred_firm->name";
                    }
                }
            }
        }

        foreach ($this->controller->firms as $firm_id => $firm_label) {
            if ((count($user_firm_ids) === 0 || in_array($firm_id,
                        $user_firm_ids)) && !isset($firms['Recent'][$firm_id])
            ) {
                $firm = Firm::model()->findByPk($firm_id);
                if ($firm instanceof Firm && $firm->active && $firm->runtime_selectable &&
                    (!$this->subspecialty || ($firm->serviceSubspecialtyAssignment && $firm->serviceSubspecialtyAssignment->subspecialty_id == $this->subspecialty->id))
                ) {
                    if ($preferred_firms) {
                        $firms['Other'][$firm_id] = $firm_label;
                    } else {
                        $firms[$firm_id] = $firm_label;
                    }
                }
            }
        }

        $this->render('SiteAndFirmWidget', array(
            'model' => $model,
            'firms' => $firms,
            'sites' => CHtml::listData($sites, 'id', 'short_name'),
            'mode' => $this->mode
        ));
    }
}
