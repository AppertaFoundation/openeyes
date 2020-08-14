<?php


class SavedSearchTest extends ActiveRecordTestCase
{
    public $model;
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp()
    {
        parent::setUp();
        $this->model = new SavedSearch();
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @covers SavedSearch
     */
    public function testModel()
    {
        $this->assertInstanceOf('SavedSearch', SavedSearch::model());
    }

    /**
     * @covers SavedSearch
     */
    public function testRelations()
    {
        $this->assertCount(2, $this->model->relations());
        $this->assertNotNull($this->model->created_user);
        $this->assertNotNull($this->model->last_modified_user);
    }

    /**
     * @covers SavedSearch
     */
    public function testAttributeLabels()
    {
        $this->assertCount(7, $this->model->attributeLabels());
    }
}
