<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class IndexSearchTest
 *
 * @group sample-data
 */
class IndexSearchTest extends CTestCase
{

    private IndexSearch $search;
    private array $array = [
        'id' => '2',
        'parent' => null,
        'primary_term' => 'Examination History',
        'secondary_term_list' => 'Presenting Compliant, Follow Up Including History',
        'description' => null,
        'general_note' => null,
        'open_element_class_name' => 'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_History',
        'goto_id' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_History_description',
        'goto_tag' => null,
        'goto_text' => null,
        'img_url' => null,
        'goto_subcontainer_class' => null,
        'goto_doodle_class_name' => null,
        'goto_property' => null,
        'warning_note' => null,
    ];

    public function setUp(): void
    {
        $this->search = new IndexSearch();
    }

    /**
     * @test
     * @covers IndexSearch
     */
    public function getIndexSearchHTML()
    {
        $this->assertNotNull($this->search->processEventDefinition("Examination"));
    }

    /**
     * @test
     * @covers IndexSearch
     */
    public function testFormatHTML()
    {
        $expectedHTML =
        "\n<div>\n\t<span>Test</span>\n</div>";

        $unformattedHTML = "<div><span>Test</span></div>";
        $formattedHTML = $this->search->formatHTML($unformattedHTML);
        $this->assertEquals($expectedHTML, $formattedHTML );
    }

    /**
     * @covers IndexSearch
     */
    public function testGenerateIndexMainDiv()
    {

        $html_content_generated = $this->search->generateIndexMainDiv($this->array, 1);
        $html_content_expected = '<div  class="result_item" data-element-id="311" data-element-name="History" data-goto-id=\'OEModule_OphCiExamination_models_Element_OphCiExamination_History_description\' data-element-class-name=\'OEModule\OphCiExamination\models\Element_OphCiExamination_History\'><span data-alias="Examination History,Presenting Compliant, Follow Up Including History" class="lvl1">Examination History</span></div>';
        $this->assertEquals($html_content_expected, $html_content_generated);
    }

    /**
     * @covers IndexSearch
     */
    public function testGenerateAdditionalInfoDiv()
    {
        $html_content_generated = $this->search->generateAdditionalInfoDiv($this->array, 1);
        $html_content_expected = '<div class="index_row row"><div class="index_col_left_lvl1"><span class="alias">Presenting Compliant, Follow Up Including History</span></div><div class="index_col_right"></div></div>';
        $this->assertEquals($html_content_expected, $html_content_generated);
    }

    /**
     * @covers IndexSearch
     * @throws SystemException
     */
    public function testGetElementName()
    {
        $expected_element_name  = "Allergies";
        $actual_element_name = $this->search->getElementName("OEModule\OphCiExamination\models\Allergies");
        $this->assertEquals($expected_element_name, $actual_element_name);
    }

    /**
     * @covers IndexSearch
     */
    public function testGetMainDivDivData()
    {
        $actual_result = $this->search->getMainDivDivData($this->array);
        $expected_result = "data-element-id=\"311\" data-element-name=\"History\" data-goto-id='OEModule_OphCiExamination_models_Element_OphCiExamination_History_description' data-element-class-name='OEModule\OphCiExamination\models\Element_OphCiExamination_History'";
        $this->assertEquals($expected_result, $actual_result);
    }
}
