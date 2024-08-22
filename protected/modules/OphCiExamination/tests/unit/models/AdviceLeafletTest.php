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

namespace OEModule\OphCiExamination\tests\unit\models;

use ModelTestCase;
use OEModule\OphCiExamination\models\AdviceLeaflet;

/**
 * Class AdviceLeafletTest
 *
 * @group sample-data
 * @group examination
 * @group advice-given
 */
class AdviceLeafletTest extends ModelTestCase
{
    use \WithTransactions;

    protected $element_cls = AdviceLeaflet::class;
    protected $existing_ids;

    public function setUp(): void
    {
        parent::setUp();

        // make sure and previous Advice Leaflets are set to be ignored before test
        $this->existing_ids = array_map(function ($existing) {
                return $existing->id;
        },
            AdviceLeaflet::model()->findAll()
        );
    }

    /** @test */
    public function active_scope_returns_only_active_results()
    {
        $expected_leaflets = AdviceLeaflet::factory()->active()->count(rand(2, 4))->create();
        $unexpected_leaflet = AdviceLeaflet::factory()->inactive()->create();

        $this->assertModelArraysMatch($expected_leaflets, AdviceLeaflet::model()->active()->findAll($this->getExcludeExistingCriteria()));
    }

    protected function getExcludeExistingCriteria()
    {
        $criteria = new \CdbCriteria();
        $criteria->addNotInCondition('t.id', $this->existing_ids);
        return $criteria;
    }
}
