<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric\controllers;

use OEModule\OphGeneric\components\EventManager;
use OEModule\OphGeneric\models\Assessment;
use OEModule\OphGeneric\models\AssessmentEntry;
use OEModule\OphGeneric\models\Comments;
use SettingMetadata;

class DefaultController extends \BaseEventTypeController
{
    protected $show_element_sidebar = false;
    protected EventManager $event_manager;

    public function getPageTitle()
    {
        $action_type = ucfirst($this->getAction()->getId());

        $short_title = ((string)SettingMetadata::model()->getSetting('use_short_page_titles') === "on");

        $title = (in_array($action_type, ['Update', 'Create']) && $short_title) ? 'Edit' : $action_type;
        $title .= ' ' . $this->getTitle();

        if ($short_title) {
            return $title;
        }

        if ($this->patient) {
            $title .= ' - ' . $this->patient->last_name . ', ' . $this->patient->first_name;
        }

        return $title . ' - OE';
    }


    public function getTitle()
    {
        return $this->event_manager->getDisplayName() ?? parent::getTitle();
    }

    public function saveEvent($data)
    {
        $creating_event = $this->event->isNewRecord;

        if (!parent::saveEvent($data)) {
            return false;
        }

        if (!$creating_event) {
            return true;
        }

        return $this->event_manager->syncEventSubtypeFor($this->event);
    }

    /**
     * @return \ElementType[]
     */
    protected function getAllElementTypes()
    {
        return $this->event_manager->getElementTypes();
    }

    protected function getElementsForElementType(\ElementType $element_type, $data)
    {
        // if we can edit the element behave as normal
        if ($this->event_manager->elementTypeIsEditable($element_type)) {
            return parent::getElementsForElementType($element_type, $data);
        }

        // otherwise return the saved element state
        return [$this->event->getElementByClass($element_type->class_name)];
    }

    /**
     * Uses Event Manager to return correct elements
     *
     * @return \BaseEventTypeElement[]
     */
    protected function getEventElements()
    {
        return $this->event_manager->getElements();
    }

    public function getOptionalElements()
    {
        return [];
    }


    /**
     * Custom validation for elements.
     *
     * @param array $data
     * @return array|mixed
     * @throws \Exception
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);

        if (isset($data['OEModule_OphGeneric_models_Assessment'])) {
            $errors = $this->setAndValidateAssessmentFromData($data, $errors);
        }

        return $errors;
    }

    /**
     * Custom validation for OCT element
     *
     * @param $data
     * @param $errors
     * @return mixed
     */
    protected function setAndValidateAssessmentFromData($data, $errors)
    {
        $et_name = Assessment::model()->getElementTypeName();
        $assessment_element = $this->getOpenElementByClassName('OEModule_OphGeneric_models_Assessment');
        $entries = $data['OEModule_OphGeneric_models_Assessment']['entries'];

        //TODO: make validation error link to Assessment work:
        // currently, "addError()" is not called so function "scrolToElement()" is not called

        foreach ($entries as $assessment_id => $assessment_entry_sides) {
            foreach ($assessment_entry_sides as $eye_side => $assessment_entry_data) {
                $assessment_entry = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
                $assessment_entry->attributes = $assessment_entry_data;

                if (!$assessment_entry->validate()) {
                    $assessmentErrors = $assessment_entry->getErrors();
                    foreach ($assessmentErrors as $assessmentErrorsAttributeName => $assessmentErrorsMessages) {
                        foreach ($assessmentErrorsMessages as $assessmentErrorMessage) {
                            $assessment_element->setFrontEndError('OEModule_OphGeneric_models_Assessment_entries_' . $assessment_id . '_' . $eye_side . '_' . $assessmentErrorsAttributeName);
                            $errors[$et_name][] = $assessmentErrorMessage;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    protected function initAction($action)
    {
        parent::initAction($action);

        $this->ensureEventIsInitialised();

        $this->initialiseEventManager();
    }

    protected function ensureEventIsInitialised()
    {
        // some base actions do no initialise the event until later in the request cycle
        // to ensure we can create our event manager, we do this a bit earlier
        if ($this->event || !\Yii::app()->getRequest()->getParam('id')) {
            return;
        }

        $this->initWithEventId(\Yii::app()->getRequest()->getParam('id'));
    }

    protected function initialiseEventManager()
    {
        if (!$this->event) {
            // lack of event implies no event state to manage
            return;
        }

        if ($this->event->isNewRecord && !\Yii::app()->getRequest()->getParam('event_subtype')) {
            throw new \CHttpException(404, 'Subtype parameter must be provided for new event');
        }

        $this->event_manager = $this->event->isNewRecord
            ? EventManager::forEventSubtypePk(\Yii::app()->getRequest()->getParam('event_subtype'))
            : EventManager::forEvent($this->event);

        $this->initFromEventManager();
    }

    protected function initFromEventManager()
    {
        if (!$this->event_manager) {
            throw new \RuntimeException('cannot init without event manager');
        }

        // the print view derives title differently, so we use the event manager to set the appropriate property
        if ($this->isPrintAction($this->action->id)) {
            $this->attachment_print_title = $this->event_manager->getDisplayName();
        }
    }

    protected function saveComplexAttributes_Assessment($element, $data, $index)
    {
        $entries = $data['OEModule_OphGeneric_models_Assessment']['entries'];

        foreach ($entries as $assessment_id => $assessment_entry_sides) {
            foreach ($assessment_entry_sides as $eye_side => $assessment_entry_data) {
                $assessment_entry = AssessmentEntry::model()->findByPk($assessment_entry_data['entry_id']);
                $assessment_entry->attributes = $assessment_entry_data;
                $assessment_entry->save(false);
            }
        }
    }
}
