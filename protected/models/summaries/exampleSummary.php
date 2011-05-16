<?php

class exampleSummary extends baseSummary
{
	public $noEvents;

	public function summarise()
	{
		// Here we generate some a sample summary of
		// number of events for this summary
		$events = Event::Model()->findAll('episode_id = ?', array($this->episode->id));

		$this->noEvents = count($events);
	}
}