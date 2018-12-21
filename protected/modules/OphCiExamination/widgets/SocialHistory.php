<?php
/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */


namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\SocialHistory as SocialHistoryElement;


class SocialHistory extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

    /**
     * @param $mode
     * @return bool
     * @inheritdoc
     */
    protected function validateMode($mode)
    {
        return $mode === static::$EPISODE_SUMMARY_MODE || parent::validateMode($mode);
    }

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
        if (!is_a($element, 'OEModule\OphCiExamination\models\SocialHistory')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }
        $element->attributes = $data;
        // note we ignore the has many relation for driving status, because the multiselect widget
        // takes care of it. But it does mean that the element is not entire reflective of the POSTed data
        // that is passed into this method.
    }
}