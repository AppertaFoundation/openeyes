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
        if ($data && !isset($data['driving_statuses'])) {
            $data['driving_statuses'] = null;
        }
        $element->attributes = $data;
    }
}
