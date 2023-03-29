<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @group sample-data
 * @group correspondence
 * @group letter
 */
class ElementLetterWithSampleDataTest extends \OEDbTestCase
{
    use \FakesSettingMetadata;
    use \HasModelAssertions;

    /** @test */
    public function internal_referral_firm_validator(): void
    {
        $instance = new ElementLetter();

        $letter_type = LetterType::factory()->useExisting([
            'name' => LetterType::NAME_FOR_INTERNAL_REFERRAL,
            'is_active' => true
        ])->create();

        $instance->letter_type_id = $letter_type->id; //set type to internal referral
        $instance->draft = '0'; //drafts are not validated

        //test default case, firm can be null
        $this->fakeSettingMetadata('correspondence_make_context_mandatory_for_internal_referrals', 'Off');

        $this->assertEquals($letter_type->id, $instance->letter_type_id);

        $this->assertAttributeValid($instance, 'to_firm_id');

        //set system settings to on
        $this->fakeSettingMetadata('correspondence_make_context_mandatory_for_internal_referrals', 'On');

        //set to_firm_id to the first available firm
        $instance->to_firm_id = Firm::model()->find()->id;

        $this->assertAttributeValid($instance, 'to_firm_id');
    }
}