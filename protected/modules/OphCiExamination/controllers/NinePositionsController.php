<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers;

use OEModule\OphCiExamination\models\NinePositions;
use OEModule\OphCiExamination\widgets\NinePositions as NinePositionsWidget;
use Patient;

class NinePositionsController extends \BaseController
{
    public function accessRules()
    {
        // Allow logged in users - the main authorisation check happens later in verifyActionAccess
        return [
            ['allow', 'users' => ['@']]
        ];
    }

    public function actionReadingForm($patient_id, $index)
    {
        $patient = Patient::model()->findByPk($patient_id);
        if (!$patient) {
            throw new \CHttpException(404, 'Unknown Patient');
        }

        $widget = $this->createWidget(
            NinePositionsWidget::class,
            array(
                'patient' => $patient,
                'mode' => NinePositionsWidget::$READING_FORM_MODE,
                'reading_index' => $index,
                'form' => $form = $this->getApp()->getWidgetFactory()->createWidget($this, 'BaseEventTypeCActiveForm', array(
                    'id' => 'clinical-create',
                    'enableAjaxValidation' => false,
                    'htmlOptions' => array('class' => 'sliding'),
                ))
            )
        );

        // reset assets for render
        $this->getApp()->assetManager->reset();

        $this->renderPartial('//elements/widget_element', ['widget' => $widget], false, true);
    }
}
