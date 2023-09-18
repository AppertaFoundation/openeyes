<?php

use OE\factories\models\EventFactory;
use services\Date;

/**
 * @group sample-data
 */
class ConsentSignDisplayTest extends OEDbTestCase
{
    use \FakesSettingMetadata;
    use \MakesApplicationRequests;

    private User $user;
    private Institution $institution;

    /** @test
     * test view and print that is showing fine when setting is off
     */
    public function consent_require_pin_for_consent_disabled_shows_correct_user_and_time_view_mode()
    {
        [$user, $institution, $event, $formatted_date, $formatted_time] = $this->consentRequirePinForConsentDisabledSetup();

        $view_mode_response = $this->actingAs($user, $institution)
            ->get('/OphTrConsent/default/view?id=' . $event->id)
            ->assertSuccessful()
            ->crawl();

        $first_sign_row = $view_mode_response->filter('[data-test="sign-row"]')->first();

        $this->assertEquals('Mr Admin AdminSystem administrator',
            $first_sign_row->filter('[data-test="signatory-name"]')->text());

        $this->assertEquals("Signed at $formatted_time", $first_sign_row->filter('[data-test="signed-at"]')->text());

        $this->assertEquals("$formatted_date", $first_sign_row->filter('[data-test="signature-date"]')->text());
    }

    /** @test
     * test view and print that is showing fine when setting is off
     */
    public function consent_require_pin_for_consent_disabled_shows_correct_user_and_time_print_mode()
    {
        [$user, $institution, $event, $formatted_date, $formatted_time] = $this->consentRequirePinForConsentDisabledSetup();

        $print_mode_response = $this->actingAs($user, $institution)
            ->get('/OphTrConsent/default/print?id=' . $event->id)
            ->assertSuccessful()
            ->crawl();

        $this->assertStringContainsString("$formatted_date, $formatted_time",
            $print_mode_response->filter('[data-test="signed-date-time"]')->text());

        $this->assertStringContainsString("Mr Admin AdminSystem administrator",
            $print_mode_response->filter('[data-test="signatory-name"]')->text());
    }

    protected function consentRequirePinForConsentDisabledSetup()
    {
        $this->fakeSettingMetadata('require_pin_for_consent', 'no');

        [$user, $institution] = $this->createUserWithInstitution();

        [$date, $event] = $this->createConsentEventWithLastModifiedDateFifteenMinutesSubtracted();

        [$formatted_date, $formatted_time] = $this->getConsentPinFormattedDateAndTime($date);

        return [$user, $institution, $event, $formatted_date, $formatted_time];
    }


    protected function getConsentPinFormattedDateAndTime($date): array
    {
        $formatted_time = $date->format('h:i');
        $formatted_date = $date->format(Helper::NHS_DATE_FORMAT);

        return [$formatted_date, $formatted_time];
    }

    /**
     * We need 15 minutes subtracted to test that it's not showing the current date , but the event's last modified date
     * @return array
     * @throws Exception
     */
    protected function createConsentEventWithLastModifiedDateFifteenMinutesSubtracted(): array
    {
        $event_elements = [
            Element_OphTrConsent_Type::class,
            Element_OphTrConsent_Esign::class];

        //subtract 15 minutes from current date
        $date = new Date(date('Y-m-d H:i:s', (time() - 60 * 15)));

        $event = EventFactory::forModule('OphTrConsent')
            ->forLastModifiedDate($date)
            ->withElements($event_elements)
            ->create();

        return [$date, $event];
    }

    protected function createUserWithInstitution(): array
    {
        $user = \User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = \Institution::factory()
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }
}
