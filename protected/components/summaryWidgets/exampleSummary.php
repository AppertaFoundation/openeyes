<?php

class ExampleSummary extends CWidget {
	public $episode_id;
	public $noEvents;

    public function run()
    {
		if (!isset($this->episode_id)) {
			throw new CHttpException(403, 'No episode id provided.');
		}

		$episode = Episode::model()->findByPk($this->episode_id);

		if (!isset($episode)) {
			throw new CHttpException(403, 'There is no episode of that id.');
		}

		// @todo - change this to a count() query
		$events = Event::model()->findAll('episode_id = ?', array($this->episode_id));

		$this->noEvents = count($events);

        $this->render('exampleSummary');
    }
}
