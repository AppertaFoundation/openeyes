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

namespace OEModule\OphCiExamination\tests\feature\admin;

use Institution;
use ModelCollection;
use OEDbTestCase;
use OEModule\OphCiExamination\models\OphCiExamination_Attribute;
use OEWebUser;
use UserIdentity;
use Yii;
use Symfony\Component\DomCrawler\Crawler;
use OE\factories\ModelFactory;
use User;

/**
 * @group sample-data
 * @group feature
 * @group examination
 * @group admin
 */
class ElementAttributeTest extends OEDbTestCase
{
    use \WithTransactions;
    use \MocksSession;
    use \MakesApplicationRequests;

    /** @test */
    public function attributes_displayed_in_the_correct_order()
    {
        list($user, $institution) = $this->createInstitutionForAdmin();

        $element_attributes = $this->createAttributesForInstitution($institution);

        $response = $this->actingAs($user, $institution)
            ->get('/oeadmin/ExaminationElementAttributes/list');

        $table_rows = $response->filter('table.standard tbody tr');

        $attribute_ids_in_order = [];
        foreach ($table_rows as $table_row) {
            $attribute_ids_in_order[] = $table_row->getAttribute('data-id');
        }

        usort($element_attributes, function ($a, $b) {
            return $a->display_order - $b->display_order;
        });
        $expected_ordered_attribute_ids = (new ModelCollection($element_attributes))->pluck('id');

        $this->assertEquals($expected_ordered_attribute_ids, $attribute_ids_in_order);
    }

    protected function createInstitutionForAdmin()
    {
        $user = User::model()->findByAttributes(['first_name' => 'admin']);

        $institution = ModelFactory::factoryFor(Institution::class)
            ->withUserAsMember($user)
            ->create();

        return [$user, $institution];
    }

    protected function createAttributesForInstitution($institution, $count = 5)
    {
        $display_orders = range(1, $count);
        // create out of display order
        shuffle($display_orders);

        $instances = [];
        foreach ($display_orders as $display_order) {
            $instances[] = ModelFactory::factoryFor(OphCiExamination_Attribute::class)->create([
                'institution_id' => $institution->id,
                'display_order' => $display_order
            ]);
        }

        return $instances;
    }
}
