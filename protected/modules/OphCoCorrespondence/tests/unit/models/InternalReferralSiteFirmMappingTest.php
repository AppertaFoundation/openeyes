<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


/**
 * @covers InternalReferralSiteFirmMapping
 * @group sample-data
 */
class InternalReferralSiteFirmMappingTest extends \OEDbTestCase
{
    use WithFaker;
    use MocksSession;
    use WithTransactions;
    use \HasModelAssertions;
    use \FakesSettingMetadata;

    /** @test */
    public function find_internal_referral_firms_with_site_param_only_test()
    {
        $this->fakeSettingMetadata('filter_service_firms_internal_referral', 'off');

        $site = Site::factory()->create();

        $different_site = Site::factory()->create();

        $mapping = [];
        $site_mapping_count = 5;
        // create 5 mappings with the same site but different firm
        for ($i = 0; $i < $site_mapping_count; $i++) {
            $firm = Firm::factory()->withSubspecialty()->create();
            $mapping[] = InternalReferralSiteFirmMapping::factory()->withSite($site)->withFirm($firm)->create();
        }

        // create 1 mapping with a different site and firm
        $different_firm = Firm::factory()->withSubspecialty()->create();
        $mapping[] = InternalReferralSiteFirmMapping::factory()->withSite($different_site)->withFirm($different_firm)->create();


        $actual = InternalReferralSiteFirmMapping::findInternalReferralFirms($site->id);

        $this->assertEquals($site_mapping_count, count($actual));
    }

    /** @test */
    public function find_internal_referral_firms_with_site_and_subspecialty_test()
    {
        $this->fakeSettingMetadata('filter_service_firms_internal_referral', 'off');
        $sites = [];
        $site_count = 5;
        $created_mappings = [];
        $subspecialty_firms = [];

        // create 5 sites and initialize the counter
        for ($i = 0; $i < $site_count; $i++) {
            $site = Site::factory()->create();
            $sites[] = $site;
        }

        $firms = [];
        $ss_count = 5;

        // create 5 firms and initialize the counter with 0
        for ($i = 0; $i < $ss_count; $i++) {
            $firm = Firm::factory()->canOwnEpisode()->withSubspecialty()->create();
            $firms[] = $firm;
        }

        // create 10 mappings with random site and firm

        for ($i = 0; $i < 5; $i++) {
            // This loop ensures there are no duplicate mappings whilst still keeping the selection randomised.
            $num_mappings = rand(1, 5);
            for ($j = 0; $j < $num_mappings; $j++) {
                $selected_site = $sites[$i];
                $selected_firm = $firms[$j];
                $created_mappings[] = InternalReferralSiteFirmMapping::factory()->withSite($selected_site)->withFirm($selected_firm)->create();
                $subspecialty_firms[$selected_firm->getSubspecialty()->id][] = $selected_firm;
            }
        }

        $test_mapping = $this->faker->randomElement($created_mappings);
        $checking_site = $test_mapping->site;
        $checking_firm = $test_mapping->firm;

        $test_firms = $subspecialty_firms[$checking_firm->getSubspecialty()->id];

        $firm_list = [];

        foreach ($test_firms as $firm) {
            $firm_list[$firm->id] = $firm->name . ($firm->getSubspecialty() ? ' (' . $firm->getSubspecialty()->name . ')' : '');
        }

        natcasesort($firm_list);

        $actual = InternalReferralSiteFirmMapping::findInternalReferralFirms($checking_site->id, $checking_firm->getSubspecialty()->id);

        $this->assertEquals($firm_list, $actual);
    }
}
