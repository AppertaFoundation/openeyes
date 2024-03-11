<?php
/**
* (C) OpenEyes Foundation, 2024
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (C) 2024, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/

/**
 * @test
 * @group sample-data
 */
class PatientSearchTest extends OEDbTestCase
{
    use MocksSession;

    /** @test */
    public function sort_option_defaults_to_first_option_for_invalid_string()
    {
        $this->mockCurrentContext();
        $_POST['sort_by'] = 'foobar';

        $patient_search = new PatientSearch();
        $criteria = $patient_search->prepareSearch('foo');

        $this->assertEquals(Patient::SEARCH_SORT_BY_OPTIONS[0], $criteria['sortBy']);
    }

    /** @test */
    public function sort_option_looks_up_from_options_list_with_valid_index()
    {
        $this->mockCurrentContext();
        $valid_index = array_rand(Patient::SEARCH_SORT_BY_OPTIONS);
        $_POST['sort_by'] = $valid_index;

        $patient_search = new PatientSearch();
        $criteria = $patient_search->prepareSearch('foo');

        $this->assertEquals(Patient::SEARCH_SORT_BY_OPTIONS[$valid_index], $criteria['sortBy']);
    }

    /** @test */
    public function sort_option_defaults_to_first_options_when_index_out_of_range()
    {
        $this->mockCurrentContext();
        $invalid_index = count(Patient::SEARCH_SORT_BY_OPTIONS);
        $_POST['sort_by'] = $invalid_index;

        $patient_search = new PatientSearch();
        $criteria = $patient_search->prepareSearch('foo');

        $this->assertEquals(Patient::SEARCH_SORT_BY_OPTIONS[0], $criteria['sortBy']);
    }
}
