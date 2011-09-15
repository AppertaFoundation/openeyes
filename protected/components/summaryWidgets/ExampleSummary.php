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

	        $noEvents = Yii::app()->db->createCommand()
                        ->select('count(*) AS c')
                        ->from('event')
                        ->where('episode_id = :epid', array(':epid'=>$this->episode_id))
                        ->queryRow();

		$this->noEvents = $noEvents['c'];

		$this->render('ExampleSummary');
	}
}
