<?php
use OE\factories\ModelFactory;
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
 * @group sample-data
 * @group common-lists
 */
class CommonPreviousSystemicOperationTest extends \ModelTestCase
{
    use \InteractsWithCommonPreviousSystemicOperation;
    use \HasDatabaseAssertions;
    use \WithTransactions;
    use \MocksSession;

    protected $element_cls = \CommonPreviousSystemicOperation::class;

    /** @test */
    public function can_delete_an_instance_mapped_to_institution()
    {
        $institution = ModelFactory::factoryFor(\Institution::class)->create();
        $instance = $this->generateCommonPreviousSystemicOperationForInstitution($institution);

        $pk = $instance->id;
        $this->assertTrue($instance->delete());
        $this->assertDatabaseDoesntHave(CommonPreviousSystemicOperation::model()->tableName(), [
            'id' => $pk
        ]);
    }
}
