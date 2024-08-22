<?php

class InstitutionTest extends ActiveRecordTestCase
{
    use HasModelAssertions;

    protected $fixtures = array(
        'import_sources' => 'ImportSource',
        'institutions' => 'Institution',
    );

    public function getModel()
    {
        return Institution::model();
    }

    public function tearDown(): void
    {
        // clear out session dependency
        Yii::app()->setComponent('session', null);
        $_SESSION = [];
    }

    /**
     * @throws Exception
     */
    public function testGetCurrent_Success()
    {
        Yii::app()->session['selected_institution_id'] = 1;
        $this->assertModelIs($this->institutions('moorfields'), $this->getModel()->getCurrent());
    }

    /**
     * @covers Institution
     */
    public function testGetCurrent_CodeNotSet()
    {
        $this->expectExceptionMessage("Institution is not set for application session");
        $this->expectException(RuntimeException::class);
        $this->getModel()->getCurrent();
    }

    /**
     * @covers Institution
     */
    public function testGetCurrent_NotFound()
    {
        $this->expectExceptionMessage("Institution with id '7' not found");
        $this->expectException(Exception::class);
        Yii::app()->session['selected_institution_id'] = 7;
        $this->getModel()->getCurrent();
    }

    /**
     * @covers Institution
     * @test
     */
    public function is_tenanted_is_correctly_defined()
    {
        $untenanted_institution = Institution::factory()->create();

        $this->assertFalse($untenanted_institution->isTenanted());

        $tenanted_institution = Institution::factory()->create();

        // An institution is tenanted when one or more institution authentications are associated with it
        InstitutionAuthentication::factory()->create(['institution_id' => $tenanted_institution]);

        $this->assertTrue($tenanted_institution->isTenanted());
    }
}
