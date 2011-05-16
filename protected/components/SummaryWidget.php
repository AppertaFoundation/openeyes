<?php

class SummaryWidget extends CWidget {
	public $episode_id;
	public $summary;

    public function run()
    {
		if (!isset($this->episode_id)) {
			throw new Exception('No episode id provided.');
		}

		if (!isset($this->summary)) {
			throw new Exception('No summary name provided.');
		}

		$episode = Episode::Model()->findByPk($this->episode_id);

		if (!isset($episode)) {
			throw new Exception('There is no episode of that id.');
		}

		$summary = Summary::Model()->find('name = ?', array($this->summary));

		if (!isset($summary)) {
			throw new Exception('There is no summary of that name.');
		}

		// @todo - check to see if this summary is linked to this
		//	episode's specialty?

		$summaryWidget = new $summary->name;
		$summaryWidget->episode = $episode;
		$summaryWidget->generate();

        $this->render($summary->name, array(
				'summary' => $summaryWidget
			)
        );
    }
}
