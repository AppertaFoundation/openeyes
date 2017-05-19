<?php
/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */


namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\SocialHistory as SocialHistoryElement;


class SocialHistory extends \BaseEventElementWidget
{
    /**
     * @return SocialHistoryElement
     */
    protected function getNewElement()
    {
        return new SocialHistoryElement();
    }

    /**
     * @param SocialHistoryElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\SocialHistory')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }
        $element->attributes = $data;
        // note we ignore the has many relation for driving status, because the multiselect widget
        // takes care of it. But it does mean that the element is not entire reflective of the POSTed data
        // that is passed into this method.
    }

    /**
     * Determine the view file to use
     */
    protected function getView()
    {
        if ($this->view_file) {
            // manually overridden/set
            return $this->view_file;
        }
        switch ($this->mode) {
            case static::$EVENT_VIEW_MODE:
                return 'SocialHistory_event_view';
                break;
            case static::$EVENT_EDIT_MODE:
                return 'SocialHistory_event_edit';
                break;
            default:
                return 'SocialHistory_patient_mode';
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->getView(), array(
            'element' => $this->element,
            'form' => $this->form));
    }
}