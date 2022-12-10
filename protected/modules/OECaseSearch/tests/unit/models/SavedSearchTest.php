<?php

/**
 * Class SavedSearchTest
 * @covers SavedSearch
 * @method saved_searches($fixtureId)
 */
class SavedSearchTest extends ActiveRecordTestCase
{
    public $model;
    protected $fixtures = array(
        'saved_searches' => SavedSearch::class,
    );

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Yii::app()->getModule('OECaseSearch');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new SavedSearch();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function testModel(): void
    {
        self::assertInstanceOf('SavedSearch', SavedSearch::model());
    }

    public function testRelations(): void
    {
        self::assertCount(3, $this->model->relations());
        self::assertNotNull($this->model->created_user);
        self::assertNotNull($this->model->last_modified_user);
    }

    public function testAttributeLabels(): void
    {
        self::assertCount(8, $this->model->attributeLabels());
    }

    public function testFindAllByUser(): void
    {
        $expected = array($this->saved_searches('saved_search1'), $this->saved_searches('saved_search3'));
        $actual = SavedSearch::model()->findAllByUser(1);
        self::assertCount(2, $actual);
        self::assertEquals($expected, $actual);
    }

    public function testFindAllByInstitution(): void
    {
        $expected = array($this->saved_searches('saved_search1'), $this->saved_searches('saved_search2'));
        $actual = SavedSearch::model()->findAllByInstitution(1);
        self::assertCount(2, $actual);
        self::assertEquals($expected, $actual);
    }

    public function testFindAllByUserOrInstitution(): void
    {
        $expected = array(
            $this->saved_searches('saved_search1'),
            $this->saved_searches('saved_search2'),
            $this->saved_searches('saved_search3')
        );
        $actual = SavedSearch::model()->findAllByUserOrInstitution(1, 1);
        self::assertCount(3, $actual);
        self::assertEquals($expected, $actual);
    }
}
