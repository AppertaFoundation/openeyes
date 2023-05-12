<?php

/**
 * @group sample-data
 * @group address
 * @group correspondence
 */
class AddressWithSampleDataTest extends OEDbTestCase
{
    use FakesSettingMetadata;
    use WithTransactions;

    /** @test */
    public function default_letter_array_behaviour_separates_by_new_line()
    {
        $this->fakeSettingMetadata('correspondence_address_force_city_state_postcode_on_same_line', 'off');

        $address = Address::factory()
            ->full()
            ->create([
                'address1' => "foo\nbar",
                'address2' => 'baz'
            ]);

        $this->assertEquals(
            ['foo', 'bar', 'baz', $address->city, $address->county, $address->postcode, $address->country->name],
            $address->getLetterArray()
        );
    }

    /** @test */
    public function letter_array_behaviour_respects_city_state_postcode_setting_joining_with_spaces()
    {
        $this->fakeSettingMetadata('correspondence_address_force_city_state_postcode_on_same_line', 'on');

        $address = Address::factory()
            ->full()
            ->create([
            'address1' => "foo\nbar",
            'address2' => 'baz'
            ]);

        $this->assertEquals(
            ['foo', 'bar', 'baz', implode(" ", [$address->city, $address->county, $address->postcode]), $address->country->name],
            $address->getLetterArray()
        );
    }
}
