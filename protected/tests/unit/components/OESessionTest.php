<?php

/**
 *
 * @group sample-data
 * @group session
 */
class OESessionTest extends OEDbTestCase
{
    use HasModelAssertions;
    use WithTransactions;

    protected ?OESession $session = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->session = $this->getSessionInstance();
    }

    /**
     * Although it may be better to have this throw an exception, the getter is a late addition,
     * and returning null is more akin to behaviour that would have been seen prior to its use
     *
     * @test
     */
    public function institution_returns_null_when_id_not_set_in_session()
    {
        $this->assertNull($this->session->getSelectedInstitution());
    }

    /** @test */
    public function institution_reflects_selected_id()
    {
        $institution = Institution::factory()->create();
        $this->session['selected_institution_id'] = $institution->id;

        $this->assertModelIs($institution, $this->session->getSelectedInstitution());
    }

    /** @test */
    public function exception_raised_for_invalid_institution_id()
    {
        $session = $this->getSessionInstance();
        $session['selected_institution_id'] = 'foobar';

        $this->expectException(Exception::class);
        $this->session->getSelectedInstitution();
    }

    /** @test */
    public function firm_returns_null_when_id_not_set_in_session()
    {
        $this->assertNull($this->session->getSelectedFirm());
    }

    /** @test */
    public function firm_reflects_selected_id()
    {
        $firm = Firm::factory()->create();
        $this->session['selected_firm_id'] = $firm->id;

        $this->assertModelIs($firm, $this->session->getSelectedFirm());
    }

    /** @test */
    public function exception_raised_for_invalid_firm_id()
    {
        $this->session['selected_firm_id'] = 'foobar';

        $this->expectException(Exception::class);
        $this->session->getSelectedFirm();
    }

    /** @test */
    public function site_returns_null_when_id_not_set_in_session()
    {
        $this->assertNull($this->session->getSelectedSite());
    }

    /** @test */
    public function site_reflects_selected_id()
    {
        $site = Site::factory()->create();
        $this->session['selected_site_id'] = $site->id;

        $this->assertModelIs($site, $this->session->getSelectedSite());
    }

    /** @test */
    public function exception_raised_for_invalid_site_id()
    {
        $this->session['selected_site_id'] = 'foobar';

        $this->expectException(Exception::class);
        $this->session->getSelectedSite();
    }

    private function getSessionInstance()
    {
        $_SESSION = [];
        $session = new OESession();
        $session->init();

        return $session;
    }
}
