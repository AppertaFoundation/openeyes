<?php
class ContactFormTest extends CDbTestCase
{
	public $model;

	public function setUp()
	{
		parent::setUp();
		$this->model = new ContactForm;
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'verifyCode'=>'Verification Code',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}
}
