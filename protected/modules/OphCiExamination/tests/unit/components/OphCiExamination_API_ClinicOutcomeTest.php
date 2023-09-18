<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\tests\unit\components;

use Event;
use Patient;
use OEDbTestCase;
use OEModule\OphCiExamination\components\OphCiExamination_API;
use OEModule\OphCiExamination\models\Element_OphCiExamination_ClinicOutcome;
use WithTransactions;

/**
 * @group sample-data
 */
class OphCiExamination_API_ClinicOutcomeTest extends OEDbTestCase
{
    use WithTransactions;

    private $api;
    private const TABLE_OPENING_TAGS = '<table><tbody>';
    private const ROW_OPENING_TAGS = '<tr><td>';
    private const ROW_PRE_STATUS_NAME_TAGS = '</td><td style="text-align:left"><b>';
    private const ROW_PRE_CONTENT_TAGS = '</b><span class="fade" style="font-size: 0.95em;"><em> ';
    private const ROW_CLOSING_TAGS = ' </em></span></td></tr>';
    private const TABLE_CLOSING_TAGS = '</tbody></table>';

    public function setApi($followup_element): void
    {
        $this->api = $this->getMockBuilder(OphCiExamination_API::class)
            ->onlyMethods(["getElementFromLatestVisibleEvent"])
            ->getMock();
        $this->api->expects($this->once())
            ->method('getElementFromLatestVisibleEvent')
            ->willReturn($followup_element);
    }

    /**
     * Function to set up a Follow up element.
     *
     * @param array $with_entry_types defines which entry types needs to be created
     * @return Element_OphCiExamination_ClinicOutcome
     */
    private function setUpFollowUpElement($with_entry_types = []): Element_OphCiExamination_ClinicOutcome
    {
        $clinic_outcome_factory = Element_OphCiExamination_ClinicOutcome::factory();
        foreach ($with_entry_types as $type) {
            $clinic_outcome_factory->$type();
        }

        $followup_element_element = $clinic_outcome_factory->create();
        $followup_element_element->refresh();
        $this->setApi($followup_element_element);

        return $followup_element_element;
    }

    private function formatBasicOutcomeFirstColumnContent($status_name, $is_first = true): string
    {
        return self::ROW_OPENING_TAGS . ($is_first ? "" : "AND") . self::ROW_PRE_STATUS_NAME_TAGS . $status_name . self::ROW_PRE_CONTENT_TAGS;
    }

    private function formatBasicOutcomeTableContent($content): string
    {
        return self::TABLE_OPENING_TAGS . $content . self::TABLE_CLOSING_TAGS;
    }

    private function getContentForFollowUpEntryType($entry, $is_first = true): string
    {
        return $this->formatBasicOutcomeFirstColumnContent($entry->status->name, $is_first)
            . ' - ' . $entry->followup_quantity . ' ' . $entry->periodLabel . ' for ' . $entry->subspecialtylabel . self::ROW_CLOSING_TAGS;
    }

    private function getContentForDischargeEntryType($entry, $is_first = true): string
    {
        return $this->formatBasicOutcomeFirstColumnContent($entry->status->name, $is_first)
            . ' - ' . $entry->dischargeStatusLabel . ' // ' . $entry->dischargeDestinationLabel . self::ROW_CLOSING_TAGS;
    }

    private function getContentForBasicEntry($entry, $is_first = true): string
    {
        return $this->formatBasicOutcomeFirstColumnContent($entry->status->name, $is_first) . self::ROW_CLOSING_TAGS;
    }

    /** @test */
    public function get_basic_outcome_details_for_followup_entry()
    {
        $followup_element = $this->setUpFollowUpElement(["withFollowUp"]);
        $expected_content = $this->formatBasicOutcomeTableContent($this->getContentForFollowUpEntryType($followup_element->entries[0]));
        $this->assertEquals($expected_content, $this->api->getBasicOutcomeDetails($followup_element->event->episode->patient));
    }

    /** @test */
    public function get_basic_outcome_details_for_discharge_entry()
    {
        $followup_element = $this->setUpFollowUpElement(["withDischarge"]);
        $expected_content = $this->formatBasicOutcomeTableContent($this->getContentForDischargeEntryType($followup_element->entries[0]));
        $this->assertEquals($expected_content, $this->api->getBasicOutcomeDetails($followup_element->event->episode->patient));
    }

    /** @test */
    public function get_basic_outcome_details_for_basic_entry()
    {
        $followup_element = $this->setUpFollowUpElement(["withEntry"]);
        $expected_content = $this->formatBasicOutcomeTableContent($this->getContentForBasicEntry($followup_element->entries[0]));
        $this->assertEquals($expected_content, $this->api->getBasicOutcomeDetails($followup_element->event->episode->patient));
    }

    /** @test */
    public function get_basic_outcome_details_for_multiple_entries()
    {
        $followup_element = $this->setUpFollowUpElement(["withFollowUp", "withDischarge", "withEntry"]);
        $expected_content = "";
        foreach ($followup_element->entries as $index => $entry) {
            $is_first = $index === 0;
            if ($entry->isFollowUp()) {
                $expected_content .= $this->getContentForFollowUpEntryType($entry, $is_first);
            } elseif ($entry->isDischarge()) {
                $expected_content .= $this->getContentForDischargeEntryType($entry, $is_first);
            } else {
                $expected_content .= $this->getContentForBasicEntry($entry, $is_first);
            }
        }
        $expected_content = $this->formatBasicOutcomeTableContent($expected_content);
        $this->assertEquals($expected_content, $this->api->getBasicOutcomeDetails($followup_element->event->episode->patient));
    }

    /** @test */
    public function get_basic_outcome_details_is_empty_when_no_followup_element()
    {
        $this->setApi(null);
        $expected_content = "";
        $this->assertEquals($expected_content, $this->api->getBasicOutcomeDetails(new Patient()));
    }
}
