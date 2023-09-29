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

namespace OEModule\Api\tests\unit\controllers;

/**
 * Class DocumentControllerTest
 *
 * @package OEModule\Api\tests\unit\controllers
 * @covers DocumentController
 * @group sample-data
 */
class DocumentControllerTest extends \OEModule\Api\tests\BaseApiTest
{
    protected $base_url_stub = 'v2/Document/';

    /** @test */
    public function create_document_with_valid_data()
    {
        $this->expected_response_code = 201;
        $this->post('create?' .
        'patient_identifier_type=LOCAL-1-0&' .
        'patient_id=0000001&' .
        'firm_id=1&' .
        'document_title=ExampleDocument.pdf&' .
        'document_subtype=General&' .
        'unique_ref=example_unique_ref&' .
        'comments=Example Comments', base64_encode('Document Data'));

        $element_id = \Yii::app()->db->createCommand('SELECT id FROM et_ophcodocument_document WHERE unique_ref = "example_unique_ref";')->queryScalar();
        $this->assertNotNull($element_id);
    }

    /** @test */
    public function create_document_with_missing_required_data()
    {
        $this->expected_response_code = 400;
        $this->post('create?' .
        'patient_identifier_type=LOCAL-1-0&' .
        // Missing patient_id
        'firm_id=1&' .
        'document_title=ExampleDocument.pdf&' .
        'document_subtype=General&' .
        'unique_ref=example_unique_ref&' .
        'comments=Example Comments', base64_encode('Document Data'));
    }

    /** @test */
    public function search_with_valid_unique_ref()
    {
        $this->expected_response_code = 200;
        $this->get('search?' .
        'unique_ref=example_unique_ref');
    }

    /** @test */
    public function search_with_valid_firm_name()
    {
        $this->expected_response_code = 200;
        $this->get('search?' .
        'firm_id=1&' .
        'document_title=ExampleDocument.pdf&' .
        'patient_identifier_type=LOCAL-1-0&' .
        'patient_id=0000001');
    }

    /** @test */
    public function search_with_missing_required_parameters()
    {
        $this->expected_response_code = 400;
        $this->get('search?' .
        // Missing firm
        // Missing document_title
        'patient_identifier_type=LOCAL-1-0&' .
        'patient_id=0000001');
    }

    /** @test */
    public function update_document_with_valid_data()
    {
        $element_id = \Yii::app()->db->createCommand('SELECT id FROM et_ophcodocument_document WHERE unique_ref = "example_unique_ref";')->queryScalar();

        $this->expected_response_code = 200;
        $this->patch('update?' .
        'element_id=' . $element_id . '&' .
        'document_title=Updated Document&' .
        'document_subtype=General&' .
        'comments=Updated Comments&' .
        'laterality=L&' .
        'unique_ref=updated_unique_ref', base64_encode('Updated Document Data'));
    }

    /** @test */
    public function update_document_with_missing_element_id()
    {
        $this->expected_response_code = 400;
        $this->patch('update?' .
        // Missing element_id
        'document_title=Updated Document&' .
        'document_subtype=General&' .
        'comments=Updated Comments&' .
        'laterality=L&' .
        'unique_ref=updated_unique_ref', base64_encode('Updated Document Data'));
    }

    /** @test */
    public function delete_document_with_valid_data()
    {
        $element_id = \Yii::app()->db->createCommand('SELECT id FROM et_ophcodocument_document WHERE unique_ref = "updated_unique_ref";')->queryScalar();

        $this->expected_response_code = 202;
        $this->delete('delete?' .
        'element_id=' . $element_id);
    }

    /** @test */
    public function delete_document_with_invalid_element_id()
    {
        $this->expected_response_code = 404;
        $this->delete('delete?' .
        'element_id=999');
    }
}
