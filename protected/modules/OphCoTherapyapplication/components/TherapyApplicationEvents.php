<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class TherapyApplicationEvents
{
    /**
     * @param $since
     * @return Event[]
     */
    protected static function getSentEvents($since)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.created_date >= :since');
        $criteria->addCondition('event.deleted = 0');
        $criteria->params = array(':since' => $since);
        return array_map(
            function ($e) {
                return $e->event;
            },
            OphCoTherapyapplication_Email::model()->unarchived()->with('event.episode')->findAll($criteria)
        );
    }

    /**
     * @param $status
     * @param $since
     * @return Event[]
     * @throws Exception
     */
    public static function getEventsByStatus($status, $since)
    {
        if ($status === OphCoTherapyapplication_Processor::STATUS_SENT) {
            return static::getSentEvents($since);
        }
        throw new Exception('Status ' . $status . ' not implemented yet.');
    }
}
