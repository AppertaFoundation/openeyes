<?php

class m191006_151659_add_new_patient_factor_questions extends OEMigration
{
    public function up()
    {
        $new_questions = array(
            array("name"=>"Does the patient live alone?",
                "code"=>"1v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "10",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Does someone support the patient with their care?",
                "code"=>"2v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "20",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Does the patient have difficulties with their physical mobility?",
                "code"=>"3v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "30",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Does the patient have difficulties with their hearing?",
                "code"=>"4v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "40",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Does the patient have a learning disability?",
                "code"=>"5v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "50",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Does the patient have a diagnosis of dementia?",
                "code"=>"6v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "60",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Is the patient employed?",
                "code"=>"7v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "70",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"Is the patient in full time education?",
                "code"=>"8v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "80",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
            array("name"=>"If the patient is a baby, child or young person, is the child/patient known to the specialist visual impairment education service?",
                "code"=>"9v1",
                "require_comments" => 0,
                "comments_label" => "",
                "display_order" => "90",
                "comments_only" => 0,
                "yes_no_only" => 0,
                "event_type_version" => 1
            ),
            array("name"=>"Record any further relevant information about medical conditions",
                "code"=>"10v1",
                "require_comments" => 1,
                "comments_label" => "",
                "display_order" => "100",
                "comments_only" => 1,
                "yes_no_only" => 0,
                "event_type_version" => 1
            ),
            array("name"=>"Record any further relevant information about emotional impact of sight loss",
                "code"=>"11v1",
                "require_comments" => 1,
                "comments_label" => "",
                "display_order" => "110",
                "comments_only" => 1,
                "yes_no_only" => 0,
                "event_type_version" => 1
            ),
            array("name"=>"Record any further relevant information about risk of falls",
                "code"=>"12v1",
                "require_comments" => 1,
                "comments_label" => "",
                "display_order" => "120",
                "comments_only" => 1,
                "yes_no_only" => 0,
                "event_type_version" => 1
            ),
            array("name"=>"Record any further relevant information about benefits of vision rehabilitation",
                "code"=>"13v1",
                "require_comments" => 1,
                "comments_label" => "",
                "display_order" => "130",
                "comments_only" => 1,
                "yes_no_only" => 0,
                "event_type_version" => 1
            ),
            array("name"=>"Record any other relevant information",
                "code"=>"14v1",
                "require_comments" => 1,
                "comments_label" => "",
                "display_order" => "140",
                "comments_only" => 1,
                "yes_no_only" => 0,
                "event_type_version" => 1
            ),
            array("name"=>"Does the patient require urgent support?",
                "code"=>"15v1",
                "require_comments" => 1,
                "comments_label" => "Please specify the reason why:",
                "display_order" => "150",
                "comments_only" => 0,
                "yes_no_only" => 1,
                "event_type_version" => 1
            ),
        );
        foreach ($new_questions as $question) {
            $this->insert('ophcocvi_clericinfo_patient_factor', $question);
        }
    }

    public function down()
    {
        $this->dbConnection->createCommand()->delete('ophcocvi_clericinfo_patient_factor', "code in ('1v1','2v1','3v1','4v1','5v1','6v1','7v1','8v1','9v1','10v1','11v1','12v1','13v1','14v1','15v1')");
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
