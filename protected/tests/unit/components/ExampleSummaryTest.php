<?php
class ExampleSummaryTest extends CDbTestCase
{
	protected $widget;

	public $fixtures = array(
		'episodes' => 'Episode',
		'events' => 'Event',
	);

	protected function setUp()
	{
		$this->widget = new exampleSummary('exampleSummary');
		parent::setUp();
	}

	public function testRun_NoEpisodeId_ThrowsException()
	{
		$this->setExpectedException('CHttpException', 'No episode id provided.');
		$this->widget->run();
	}

	public function testRun_InvalidEpisodeId_ThrowsException()
	{
		$this->widget->episode_id = 99999999;

		$this->setExpectedException('CHttpException', 'There is no episode of that id.');
		$this->widget->run();
	}

	public function testRun_RendersSummaryView()
	{
		$episode = $this->episodes('episode1');

		$mockWidget = $this->getMock('ExampleSummary', array('render'),
			array('ExampleSummary'));

		$mockWidget->episode_id = $episode->id;

		$mockWidget->expects($this->any())
			->method('render')
			->with('ExampleSummary');

		$mockWidget->run();
	}
}
