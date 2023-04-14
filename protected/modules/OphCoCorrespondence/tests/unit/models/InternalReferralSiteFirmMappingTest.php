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
    use MocksSession;
    use WithTransactions;
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
            $firm = Firm::factory()->withNewSubspecialty()->create();
            $mapping[] = InternalReferralSiteFirmMapping::factory()->withSite($site)->withFirm($firm)->create();
        }

        // create 1 mapping with a different site and firm
        $different_firm = Firm::factory()->withNewSubspecialty()->create();
        $mapping[] = InternalReferralSiteFirmMapping::factory()->withSite($different_site)->withFirm($different_firm)->create();


        $actual = InternalReferralSiteFirmMapping::findInternalReferralFirms($site->id);

        $this->assertEquals(count($actual), $site_mapping_count);
    }

    /** @test */
    public function find_internal_referral_firms_with_site_and_subspecialty_test()
    {
        $this->fakeSettingMetadata('filter_service_firms_internal_referral', 'off');
        $sites = [];
        $site_count = 5;
        $created_mapping_count = [];
        // create 5 sites and initialize the counter
        for ($i = 0; $i < $site_count; $i++) {
            $site = Site::factory()->create();
            $sites[] = $site;
            $created_mapping_count[$site->id] = [];
        }

        $firms = [];
        $ss_count = 5;
        // create 5 firms and initialize the counter with 0
        for ($i = 0; $i < $ss_count; $i++) {
            $firm = Firm::factory()->canOwnEpisode()->withNewSubspecialty()->create();
            $firms[] = $firm;
            foreach ($created_mapping_count as $key => $mapping) {
                $created_mapping_count[$key][$firm->subspecialty_id] = 0;
            }
        }

        // create 10 mappings with random site and firm
        $total_mapping_count = 10;
        for ($i = 0; $i < $total_mapping_count; $i++) {
            $rand_site_idx = array_rand($sites);
            $rand_firm_idx = array_rand($firms);
            $selected_site = $sites[$rand_site_idx];
            $selected_firm = $firms[$rand_firm_idx];
            InternalReferralSiteFirmMapping::factory()->withSite($selected_site)->withFirm($selected_firm)->create();
            $created_mapping_count[$selected_site->id][$selected_firm->subspecialty_id] += 1;
        }

        $rand_site_idx = array_rand($sites);
        $rand_firm_idx = array_rand($firms);
        $checking_site = $sites[$rand_site_idx];
        $checking_firm = $firms[$rand_firm_idx];

        $actual = InternalReferralSiteFirmMapping::findInternalReferralFirms($checking_site->id, $checking_firm->subspecialty_id);

        $this->assertEquals(count($actual), $created_mapping_count[$checking_site->id][$checking_firm->subspecialty_id]);
    }
}
