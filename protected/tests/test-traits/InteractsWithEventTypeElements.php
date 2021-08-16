<?php


trait InteractsWithEventTypeElements
{
    protected function getEventTypeId()
    {
        /** TODO: abstract this to support multiple event types */
        return EventType::model()->find("class_name = :cls_name", [':cls_name' => 'OphCiExamination'])->getPrimaryKey();
    }

    protected function getPatientWithEpisodesAndWithOperationNoteEvent()
    {
        $patient = null;
        do {
            $patient = $this->getPatientWithEpisodes();
        } while (\Yii::app()->moduleAPI->get('OphTrOperationnote')->getLatestEvent($patient) === null);
        return $patient;
    }

    protected function getPatientWithEpisodes()
    {
        $sql = <<<EOSQL
select t.*, count(episode.id)
from patient t
left join episode on t.id = episode.patient_id
where (
    episode.deleted = 0
    and (episode.legacy is null or episode.legacy = 0)
    and (episode.change_tracker=0 or episode.change_tracker is null)
) group by t.id having count(episode.id) > 0 order by RAND() limit 1
EOSQL;

        return Patient::model()
            ->findBySql($sql);
    }

    protected function getEventToSaveWith(Patient $patient = null, $attrs = [])
    {
        if ($patient === null) {
            // Get a random patient with at least one episode
            $patient = $this->getPatientWithEpisodes();
        }

        $episode = $patient->episodes[0];

        $event = new Event();
        $event->setAttributes($attrs);
        $event->episode_id = $episode->getPrimaryKey();
        $event->event_type_id = $this->getEventTypeId();
        $event->institution_id = 1;
        $event->site_id = 1;

        $event->save();
        return $event;
    }

    /**
     * Helper function that will allow an element to be saved to an arbitrary event
     *
     * @param $element
     * @return mixed
     * @throws Exception
     */
    protected function saveElement($element)
    {
        if (!$element->event_id) {
            $event = $this->getEventToSaveWith();
            $element->event_id = $event->getPrimaryKey();
        }

        $element->save();

        return $element;
    }
}
