<?php

use PDepend\Util\Log;

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class ContactTest extends ModelTestCase
{
    use WithTransactions;
    use WithFaker;

    protected $element_cls = Contact::class;

    /**
     * @test
     * @covers Contact
     */
    public function attribute_labels()
    {
        $expected = [
            'id' => 'ID',
            'nick_name' => 'Nickname',
            'primary_phone' => 'Phone number',
            'mobile_phone' => 'Mobile number',
            'title' => 'Title',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'qualifications' => 'Qualifications',
            'contact_label_id' => 'Label',
            'email' => 'Email'
        ];

        $this->assertEquals($expected, Contact::model()->attributeLabels(), 'Attribute labels should match.');
    }

    /**
     * @test
     * @covers Contact
     */
    public function getFullNametestG()
    {
        $title = $this->faker->word();
        $first_name = $this->faker->word();
        $last_name = $this->faker->word(2);

        $contact = Contact::factory()->make([
            'title' => $title,
            'first_name' => $first_name,
            'last_name' => $last_name
        ]);

        $expected = trim(implode(' ', [$title, $first_name, $last_name]));
        $result = $contact->getFullName();

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers Contact
     */
    public function getReversedFullName_for_title_first_name_and_last_name()
    {
        $title = $this->faker->word();
        $first_name = $this->faker->word();
        $last_name = $this->faker->word(2);

        $contact = Contact::factory()->make([
            'title' => $title,
            'first_name' => $first_name,
            'last_name' => $last_name
        ]);

        $expected = trim(implode(' ', [$title, $last_name, $first_name]));
        $result = $contact->getReversedFullName();

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers Contact
     */
    public function getSalutationName_for_title_and_last_name()
    {
        $title = $this->faker->word();
        $last_name = $this->faker->word(2);

        $contact = Contact::factory()->make([
            'title' => $title,
            'last_name' => $last_name
        ]);

        $expected = $title . ' ' . $last_name;
        $result = $contact->GetSalutationName();

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers Contact
     */
    public function contactLine_with_location()
    {
        $title = $this->faker->word();
        $first_name = $this->faker->word();
        $last_name = $this->faker->word(2);

        $label = ContactLabel::factory()->create();

        $contact = Contact::factory()->make([
            'title' => $title,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $location = ContactLocation::factory()->forContact($contact)->create();

        $expectedwithlocation = $contact->getFullName() . ' (' . $label->name . ', ' . $location . ')';

        $resultwithlocation = $contact->ContactLine($location);
        $this->assertEquals($expectedwithlocation, $resultwithlocation);
    }

    /**
     * @test
     * @covers Contact
     */
    public function contactLine_without_location()
    {
        $title = $this->faker->word();
        $first_name = $this->faker->word();
        $last_name = $this->faker->word(2);

        $label = ContactLabel::factory()->create();

        $contact = Contact::factory()->make([
            'title' => $title,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $expectedwithoutlocation = $contact->getFullName() . ' (' . $label->name . ')';
        $resultwithoutlocation = $contact->ContactLine();
        $this->assertEquals($expectedwithoutlocation, $resultwithoutlocation);
    }

    /**
     * @test
     * @covers Contact
     */
    public function findByLabel_no_partial_matchs_without_percent_symbol()
    {
        $title = $this->faker->word();
        $first_name = $this->faker->word();
        $last_name_partial = $this->faker->word();
        $last_name_rest = $this->faker->word();
        $last_name = $last_name_partial . ' ' . $last_name_rest;

        $label = ContactLabel::factory()->create();

        $contact = Contact::factory()->make([
            'title' => $title,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $res = Contact::model()->findByLabel($last_name_partial, $label->name);

        $this->assertEquals([], $res, 'No partial match without % appended to search term');
    }

    /**
     * @test
     * @covers Contact
     */
    public function findByLabel_wildcard_matches_without_location()
    {
        $last_name_partial = $this->faker->word();
        $last_name_rest = $this->faker->word();
        $last_name = $last_name_partial . ' ' . $last_name_rest;

        $label = ContactLabel::factory()->create();

        $contact1 = Contact::factory()->create([
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $contact2 = Contact::factory()->create([
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $term = strtolower($last_name_partial);
        $res = Contact::model()->findByLabel($term . '%', $label->name);

        $expected = [
            ['line' => $contact1->ContactLine(), 'contact_id' => $contact1->id],
            ['line' => $contact2->ContactLine(), 'contact_id' => $contact2->id],
        ];

        usort($res, static function ($lhs, $rhs) {
            return $lhs['contact_id'] <=> $rhs['contact_id'];
        });
        usort($expected, static function ($lhs, $rhs) {
            return $lhs['contact_id'] <=> $rhs['contact_id'];
        });

        $this->assertEquals($expected, $res, 'Should match the contact with wildcard appended to substring of last name');
    }

    /**
     * @test
     * @covers Contact
     */
    public function findByLabel_wildcard_matches_with_location()
    {
        $last_name_partial = $this->faker->word();
        $last_name_rest = $this->faker->word();
        $last_name = $last_name_partial . ' ' . $last_name_rest;

        $label = ContactLabel::factory()->create();

        $contact = Contact::factory()->create([
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $site_name = $this->faker->word();
        $site = Site::factory()->create(['name' => $site_name, 'contact_id' => $contact]);

        $location = ContactLocation::factory()->forContact($contact)->forSite($site)->create();
        $address = $contact->address ?? Address::factory()->create(['contact_id' => $contact]);

        $res = Contact::model()->findByLabel($last_name_partial . '%', $label->name);
        $expected = [['line' => $contact->ContactLine($site_name . ', ' . $address->address1 . ', ' . $address->city), 'contact_location_id' => $location->id]];

        $this->assertEquals($expected, $res, 'Should match the first contact with wildcard appended to term');
    }

    /**
     * @test
     * @covers Contact
     */
    public function findByLabel_wildcard_matches_person()
    {
        $last_name_partial = $this->faker->word();
        $last_name_rest = $this->faker->word();
        $last_name = $last_name_partial . ' ' . $last_name_rest;

        $label = ContactLabel::factory()->create();

        $contact1 = Contact::factory()->create([
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        $contact2 = Contact::factory()->create([
            'last_name' => $last_name,
            'contact_label_id' => $label
        ]);

        // note checking restricted to only Person as the search term matches a non-Person contact as well
        Person::factory()->create(['contact_id' => $contact2]);

        $term = strtolower($last_name_partial);
        $expected = [['line' => $contact2->ContactLine(), 'contact_id' => $contact2->id]];
        $res = Contact::model()->findByLabel($term . '%', $label->name, false, 'person');

        $this->assertEquals($expected, $res);
    }

    /**
     * @test
     * @covers Contact
     */
    public function searching_with_valid_terms_returns_expected_results()
    {
        $nick_name = $this->faker->slug(2);
        $email = $this->faker->email();

        $search_terms = [
            ['nick_name' => $nick_name],
            ['email' => 'foo@' . (string)microtime() . '.bar.invalid'],
            ['email' => $email]
        ];

        $expected_results = [
            Contact::factory()->create(['nick_name' => $nick_name]),
            Contact::factory()->create(['email' => $email])
        ];

        $data = [];

        foreach ($search_terms as $search_term) {
            $contact = new Contact();
            $contact->setAttributes($search_term);
            $results = $contact->search();
            $data = array_merge($data, $results->getData());
        }

        $this->assertModelArraysMatch($expected_results, $data);
    }

    /**
     * @test
     * @covers Contact
     */
    public function contact_types_match_models()
    {
        $contacts = array_combine(
            ['Gp', 'Patient', 'Person', 'User'],
            Contact::factory()->count(4)->create()
        );

        Gp::factory()->create(['contact_id' => $contacts['Gp']]);
        Patient::factory()->create(['contact_id' => $contacts['Patient']]);
        Person::factory()->create(['contact_id' => $contacts['Person']]);
        User::factory()->create(['contact_id' => $contacts['User']]);

        foreach ($contacts as $expected => $contact) {
            $result = $contact->GetType();

            $this->assertEquals($expected, $result);
        }
    }
}
