<?php

class InstitutionTest extends ActiveRecordTestCase
{
    protected $fixtures = array(
        'import_sources' => 'ImportSource',
        'institutions' => 'Institution',
    );

    public function getModel()
    {
        return Institution::model();
    }

    public static function setUpBeforeClass()
    {
        Yii::app()->session['selected_institution_id'] = 1;
    }

    public static function tearDownAfterClass()
    {
        unset(Yii::app()->session['selected_institution_id']);
    }

    /**
     * @throws Exception
     */
    public function testGetCurrent_Success()
    {
        $this->assertEquals($this->institutions('moorfields'), Institution::model()->getCurrent());
    }

    /**
     * @covers Institution
     */
    public function testGetCurrent_CodeNotSet()
    {
        $this->expectExceptionMessage("Institution id is not set");
        $this->expectException(Exception::class);
        unset(Yii::app()->session['selected_institution_id']);
        Institution::model()->getCurrent();
    }

    /**
     * @covers Institution
     */
    public function testGetCurrent_NotFound()
    {
        $this->expectExceptionMessage("Institution with id '7' not found");
        $this->expectException(Exception::class);
        Yii::app()->session['selected_institution_id'] = 7;
        Institution::model()->getCurrent();
    }
}
