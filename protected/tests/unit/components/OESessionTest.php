<?php

use OEModule\OESysEvent\events\SessionSiteChangedSystemEvent;
use OEModule\OESysEvent\tests\test_traits\HasSysEventAssertions;

/**
 *
 * @group sample-data
 * @group session
 */
class OESessionTest extends OEDbTestCase
{
    use HasModelAssertions;
    use WithTransactions;
    use HasSysEventAssertions;

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

    /**
     * @test
     * @group sys-events
     */
    public function system_event_not_dispatched_when_initialising_site_id()
    {
        $site = Site::factory()->create();
        $this->fakeEvents();
        $this->session['selected_site_id'] = $site->id;

        $this->assertEventNotDispatched(SessionSiteChangedSystemEvent::class);
    }

    /**
     * @test
     * @group sys-events
     */
    public function system_event_dispatched_when_site_id_changed()
    {
        $sites = Site::factory()->count(2)->create();
        $this->fakeEvents();
        $this->session['selected_site_id'] = $sites[0]->id;
        $this->session['selected_site_id'] = $sites[1]->id;

        $this->assertEventDispatched(
            SessionSiteChangedSystemEvent::class,
            function (SessionSiteChangedSystemEvent $event) use ($sites) {
                return $event->old_site_id === (int) $sites[0]->id
                    && $event->new_site_id === (int) $sites[1]->id;
            }
        );
    }

    /**
     * @test
     * @group sys-events
     */
    public function system_event_not_dispatched_when_site_id_set_to_current_value()
    {
        $site = Site::factory()->create();
        $this->fakeEvents();
        $this->session['selected_site_id'] = $site->id;
        $this->session['selected_site_id'] = $site->id;

        $this->assertEventNotDispatched(SessionSiteChangedSystemEvent::class);
    }

    /**
     * @test
     * @group sys-events
     */
    public function can_handle_site_being_unset()
    {
        $site = Site::factory()->create();
        $this->fakeEvents();
        $this->session['selected_site_id'] = $site->id;
        $this->assertModelIs($site, $this->session->getSelectedSite());
        unset($this->session['selected_site_id']);
        $this->assertNull($this->session->getSelectedSite());

        // no need to dispatch event when being nulled - will be dispatched when set to new value
        $this->assertEventNotDispatched(SessionSiteChangedSystemEvent::class);
    }

    /** @test */
    public function storing_user_causes_the_user_id_to_be_stored_and_not_the_user_object()
    {
        $user = User::factory()->create();
        $this->session['user'] = $user;

        $this->assertArrayNotHasKey('user', $_SESSION);
        $this->assertEquals($user->id, $_SESSION['user_id']);
    }

    /** @test */
    public function user_is_retrieved_from_session_user_id()
    {
        $user = User::factory()->create();
        $this->session['user_id'] = $user->id;

        $this->assertModelIs($user, $this->session['user']);
    }

    /** @test */
    public function cannot_store_incorrect_model_class_for_user()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->session['user'] = UserAuthentication::factory()->create();
    }

    /** @test */
    public function storing_user_auth_causes_the_id_to_be_stored_and_not_the_object()
    {
        $user_auth = UserAuthentication::factory()->create();
        $this->session['user_auth'] = $user_auth;

        $this->assertArrayNotHasKey('user_auth', $_SESSION);
        $this->assertEquals($user_auth->id, $_SESSION['user_auth_id']);
    }

    /** @test */
    public function user_auth_is_retrieved_from_session_user_auth_id()
    {
        $user_auth = UserAuthentication::factory()->create();
        $this->session['user_auth_id'] = $user_auth->id;

        $this->assertModelIs($user_auth, $this->session['user_auth']);
    }

    /** @test */
    public function cannot_store_incorrect_model_class_for_user_auth()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->session['user_auth'] = User::factory()->create();
    }

    private function getSessionInstance()
    {
        $_SESSION = [];
        $session = new OESession();
        $session->autoStart = false;
        $session->init();

        return $session;
    }
}
