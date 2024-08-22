<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m191006_151659_add_new_patient_factor_questions extends OEMigration
{
    private $new_questions = array(
        array("name" => "Does the patient live alone?",
            "code" => "1v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "10",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Does someone support the patient with their care?",
            "code" => "2v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "20",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Does the patient have difficulties with their physical mobility?",
            "code" => "3v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "30",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Does the patient have difficulties with their hearing?",
            "code" => "4v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "40",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Does the patient have a learning disability?",
            "code" => "5v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "50",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Does the patient have a diagnosis of dementia?",
            "code" => "6v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "60",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Is the patient employed?",
            "code" => "7v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "70",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "Is the patient in full time education?",
            "code" => "8v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "80",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
        array("name" => "If the patient is a baby, child or young person, is the child/patient known to the specialist visual impairment education service?",
            "code" => "9v1",
            "require_comments" => 0,
            "comments_label" => "",
            "display_order" => "90",
            "comments_only" => 0,
            "yes_no_only" => 0,
            "event_type_version" => 1
        ),
        array("name" => "Record any further relevant information about medical conditions",
            "code" => "10v1",
            "require_comments" => 1,
            "comments_label" => "",
            "display_order" => "100",
            "comments_only" => 1,
            "yes_no_only" => 0,
            "event_type_version" => 1
        ),
        array("name" => "Record any further relevant information about emotional impact of sight loss",
            "code" => "11v1",
            "require_comments" => 1,
            "comments_label" => "",
            "display_order" => "110",
            "comments_only" => 1,
            "yes_no_only" => 0,
            "event_type_version" => 1
        ),
        array("name" => "Record any further relevant information about risk of falls",
            "code" => "12v1",
            "require_comments" => 1,
            "comments_label" => "",
            "display_order" => "120",
            "comments_only" => 1,
            "yes_no_only" => 0,
            "event_type_version" => 1
        ),
        array("name" => "Record any further relevant information about benefits of vision rehabilitation",
            "code" => "13v1",
            "require_comments" => 1,
            "comments_label" => "",
            "display_order" => "130",
            "comments_only" => 1,
            "yes_no_only" => 0,
            "event_type_version" => 1
        ),
        array("name" => "Record any other relevant information",
            "code" => "14v1",
            "require_comments" => 1,
            "comments_label" => "",
            "display_order" => "140",
            "comments_only" => 1,
            "yes_no_only" => 0,
            "event_type_version" => 1
        ),
        array("name" => "Does the patient require urgent support?",
            "code" => "15v1",
            "require_comments" => 1,
            "comments_label" => "Please specify the reason why:",
            "display_order" => "150",
            "comments_only" => 0,
            "yes_no_only" => 1,
            "event_type_version" => 1
        ),
    );

    public function up()
    {
        foreach ($this->new_questions as $question) {
            $this->insert('ophcocvi_clericinfo_patient_factor', $question);
        }
    }

    public function down()
    {
        $codes_array = array_column($this->new_questions, 'code');
        $code_string = implode($codes_array);
        $this->delete('ophcocvi_clericinfo_patient_factor', "code IN (${$code_string})");
    }
}
