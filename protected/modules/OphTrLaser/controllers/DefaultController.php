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
class DefaultController extends BaseEventTypeController
{
    // This map defines which elements can import eyedraw data from the most recent element type in the current episode
    public static $IMPORT_ELEMENTS = array(
        'Element_OphTrLaser_PosteriorPole' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_PosteriorPole',
        'Element_OphTrLaser_AnteriorSegment' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment',
    );

    /**
     * sets up some javascript variables for use in the editing views.
     */
    protected function _jsCreate()
    {
        $eventId = Yii::app()->getRequest()->getQuery('id', null);
        if ($eventId) {
            $eventObj = Element_OphTrLaser_Site::model()->find('event_id = '.$eventId);
            $lasers = OphTrLaser_Site_Laser::model()->activeOrPk($eventObj->laser_id)->findAll();
        } else {
            $lasers = OphTrLaser_Site_Laser::model()->active()->findAll();
        }

        $l_by_s = array();
        foreach ($lasers as $slaser) {
            $l_by_s[$slaser->site_id][] = array('id' => $slaser->id, 'name' => $slaser->name);
        }
        Yii::app()->getClientScript()->registerScript('OphTrLaserJS', 'var lasersBySite = '.CJavaScript::encode($l_by_s).';', CClientScript::POS_HEAD);
    }

    /**
     * Loads the split event type javascript libraries.
     *
     * @param CAction $action
     *
     * @return bool
     */
    protected function beforeAction($action)
    {
        Yii::app()->assetManager->registerScriptFile('js/spliteventtype.js', null, null, AssetManager::OUTPUT_SCREEN);

        return parent::beforeAction($action);
    }

    /**
     * need to ensure we load the required js.
     */
    public function initActionCreate()
    {
        parent::initActionCreate();
        $this->_jsCreate();
    }

    /**
     * need to ensure we load the required js.
     */
    public function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->_jsCreate();
    }

    /**
     * Override to support importing the eyedraw elements.
     *
     * @return BaseEventTypeElement[]
     */
    protected function getEventElements()
    {
        if (!$this->event || $this->event->isNewRecord) {
            $elements = $this->event_type->getDefaultElements();
            foreach ($elements as $el) {
                $this->importElementEyeDraw($el);
            }
        } else {
            $elements = $this->event->getElements();
        }

        return $elements;
    }

    /**
     * Look for and import eyedraw values from most recent related element if available.
     *
     * @param BaseEventTypeElement $element
     */
    protected function importElementEyeDraw($element)
    {
        $el_class = get_class($element);
        if (array_key_exists($el_class, self::$IMPORT_ELEMENTS)) {
            $import_model = self::$IMPORT_ELEMENTS[$el_class];
            $api = $this->getApp()->moduleAPI->get('OphCiExamination');
            if ($import = $api->getLatestElement($import_model, $this->patient)) {
                $element->left_eyedraw = $import->left_eyedraw;
                $element->right_eyedraw = $import->right_eyedraw;
                $element->eye_id = $import->eye_id;
            }
        }
    }

    /**
     * Override to call the eyedraw import for loaded elements.
     */
    protected function getElementForElementForm($element_type, $previous_id = 0, $additional)
    {
        $element = parent::getElementForElementForm($element_type, $previous_id, $additional);

        // do eyedraw import
        $this->importElementEyeDraw($element);

        return $element;
    }

    /**
     * Custom validation for Treatment child elements.
     *
     * @param array $data
     *
     * @return array $errors
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);

        foreach ($this->open_elements as $element) {
            if ($element->elementTypeName == 'Treatment') {
                break;
            }
        }

        foreach ($this->getChildElements($element->elementType) as $child) {
            if ($child->hasRight() && !$element->hasRight()) {
                $errors[$child->elementTypeName][] = "Can't have right side without procedures on right eye";
            }
            if ($child->hasLeft() && !$element->hasLeft()) {
                $errors[$child->elementTypeName][] = "Can't have left side without procedures on left eye";
            }
        }

        return $errors;
    }

    /**
     * Sets Laser Procedures.
     *
     * @param BaseEventTypeElement $element
     * @param array                $data
     * @param null                 $index
     */
    protected function setElementComplexAttributesFromData($element, $data, $index = null)
    {
        if (get_class($element) == 'Element_OphTrLaser_Treatment') {
            $right_procedures = array();
            if (isset($data['treatment_right_procedures'])) {
                foreach ($data['treatment_right_procedures'] as $proc_id) {
                    $right_procedures[] = Procedure::model()->findByPk($proc_id);
                }
            }
            $element->right_procedures = $right_procedures;

            $left_procedures = array();
            if (isset($data['treatment_left_procedures'])) {
                foreach ($data['treatment_left_procedures'] as $proc_id) {
                    $left_procedures[] = Procedure::model()->findByPk($proc_id);
                }
            }
            $element->left_procedures = $left_procedures;
        }
    }

    /**
     * Saves the Laser Procedures.
     *
     * @param array $data
     */
    protected function saveEventComplexAttributesFromData($data)
    {
        foreach ($this->open_elements as $el) {
            if (get_class($el) == 'Element_OphTrLaser_Treatment') {
                $rprocs = array();
                $lprocs = array();
                if ($el->hasRight() && isset($data['treatment_right_procedures'])) {
                    $rprocs = $data['treatment_right_procedures'];
                }
                $el->updateRightProcedures($rprocs);
                if ($el->hasLeft() && isset($data['treatment_left_procedures'])) {
                    $lprocs = $data['treatment_left_procedures'];
                }
                $el->updateLeftProcedures($lprocs);
            }
        }
    }

    public function checkPrintAccess()
    {
        return false;
    }
}
