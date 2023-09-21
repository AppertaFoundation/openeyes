<?php

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

use ModelTestCase;
use WithTransactions;
use WithFaker;
use MocksSession;
use Institution;
use Site;
use ContactLabel;

/**
 *
 * @group sample-data
 */
class ContactLabelTest extends ModelTestCase
{
    use WithTransactions;
    use WithFaker;
    use MocksSession;

    protected $element_cls = ContactLabel::class;

    /**
     * @test
     * @covers ContactLabel
     */
    public function search_with_valid_terms_returns_expected_results()
    {
        $valid_name = $this->faker->word();
        $invalid_name = $this->faker->word() . (string)microtime();

        $expected_results = [
            ContactLabel::factory()->create(['name' => $valid_name])
        ];

        $search_terms = [
            ['name' => $invalid_name],
            ['name' => $valid_name]
        ];

        $data = [];

        foreach ($search_terms as $search_term) {
            $contactlabel = new ContactLabel();
            $contactlabel->setAttributes($search_term);
            $results = $contactlabel->search();
            $data = array_merge($data, $results->getData());
        }

        $this->assertModelArraysMatch($expected_results, $data);
    }

    /**
     * @covers ContactLabel
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'name' => 'Name',
            'letter_template_only' => 'Letter Template Only',
            'is_private' => 'Is Private',
            'max_number_per_patient' => 'Max Number Per Patient'
        );
        $this->assertEquals($expected, $this->getModel()->attributeLabels());
    }

    /**
     * @test
     * @covers ContactLabel
     */
    public function staff_type()
    {
        $name = $this->faker->word();

        $institution = Institution::factory()->create(['name' => $name, 'short_name' => $name]);
        $site = Site::factory()->create(['institution_id' => $institution]);

        $this->mockCurrentContext(null, $site, $institution);

        $result = ContactLabel::model()->staffType();
        $expected = $name . ' staff';

        $this->assertEquals($expected, $result);
    }
}
