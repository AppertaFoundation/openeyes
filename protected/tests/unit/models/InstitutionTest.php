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

    public function testGetCurrent_Success()
    {
        Yii::app()->params['institution_code'] = getenv('OE_INSTITUTION_CODE') ? getenv('OE_INSTITUTION_CODE') : 'NEW';
        $this->assertEquals($this->institutions('moorfields'), Institution::model()->getCurrent());
    }

    /**
     * @covers Institution
     */
    public function testGetCurrent_CodeNotSet()
    {
        $this->expectExceptionMessage("Institution code is not set");
        $this->expectException(Exception::class);
        unset(Yii::app()->params['institution_code']);
        Institution::model()->getCurrent();
    }

    /**
     * @covers Institution
     */
    public function testGetCurrent_NotFound()
    {
        $this->expectExceptionMessage("Institution with code 'bar' not found");
        $this->expectException(Exception::class);
        Yii::app()->params['institution_code'] = 'bar';
        Institution::model()->getCurrent();
    }
}
