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
use MakesElementFormRequests;
use WithTransactions;

use ElementType;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Fundus;

/**
 * @group sample-data
 * @group examination
 */
class Element_OphCiExamination_FundusTest extends ModelTestCase
{
    use MakesElementFormRequests;
    use WithTransactions;

    protected $element_cls = Element_OphCiExamination_Fundus::class;

    /** @test */
    public function optic_disc_element_marked_dirty_on_creation()
    {
        $this->mockCurrentContext();

        $response = $this->getElementForm(Element_OphCiExamination_Fundus::model()->elementtype);

        $is_dirty = $this->extractElementDirtyValue(Element_OphCiExamination_Fundus::model(), $response);

        $this->assertEquals($is_dirty, '1');
    }
}
