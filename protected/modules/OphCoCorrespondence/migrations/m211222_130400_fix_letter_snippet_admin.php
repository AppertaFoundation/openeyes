<?php

class m211222_130400_fix_letter_snippet_admin extends OEMigration
{
    public function safeUp()
    {

        $snippet_data_array = $this->dbConnection->createCommand("SELECT
                                                                    MIN(ls.id) AS min_id,
                                                                    ls.`name` AS ls_name,
                                                                    GROUP_CONCAT(ls.id) AS ls_ids,
                                                                    CONCAT(i.institution_id) AS ins_id,
                                                                    GROUP_CONCAT(s.site_id) AS site_ids
                                                                FROM ophcocorrespondence_letter_string ls
                                                                LEFT JOIN `ophcocorrespondence_letter_string_institution` i ON ls.id = i.`letter_string_id`
                                                                LEFT JOIN `ophcocorrespondence_letter_string_site` s ON ls.id = s.`letter_string_id`
                                                                GROUP BY ls.`name`, i.`institution_id`")->queryAll();

        foreach($snippet_data_array as $index=>$snippet_data) {
            $snippet_data_array[$index]["site_ids"] = explode (",", $snippet_data_array[$index]["site_ids"]);

            $institution_sites = $this->dbConnection->createCommand("SELECT id
                                                            FROM `site`
                                                            WHERE institution_id = " . $snippet_data_array[$index]["ins_id"])->queryAll();

            if(count($institution_sites) === count($snippet_data_array[$index]["site_ids"])) {
                $this->execute("DELETE 
                    FROM `ophcocorrespondence_letter_string_site` 
                    WHERE letter_string_id IN ({$snippet_data_array[$index]["ls_ids"]})");

                $this->execute("DELETE 
                    FROM `ophcocorrespondence_letter_string_institution` 
                    WHERE letter_string_id IN ({$snippet_data_array[$index]["ls_ids"]})
                    AND letter_string_id != {$snippet_data_array[$index]["min_id"]}");

                $this->execute("DELETE 
                    FROM `ophcocorrespondence_letter_string` 
                    WHERE id IN ({$snippet_data_array[$index]["ls_ids"]})
                    AND id != {$snippet_data_array[$index]["min_id"]}");
            } else {
                $this->execute("DELETE 
                    FROM `ophcocorrespondence_letter_string_institution` 
                    WHERE letter_string_id IN ({$snippet_data_array[$index]["ls_ids"]})");

                $this->execute("UPDATE
                    ophcocorrespondence_letter_string_site
                    SET letter_string_id = {$snippet_data_array[$index]["min_id"]}
                    WHERE letter_string_id IN ({$snippet_data_array[$index]["ls_ids"]})"
                );

                $this->execute("DELETE 
                    FROM `ophcocorrespondence_letter_string` 
                    WHERE id IN ({$snippet_data_array[$index]["ls_ids"]})
                    AND id != {$snippet_data_array[$index]["min_id"]}"
                );
            }
        }
    }

    public function safeDown()
    {
        echo "m211222_130400_fix_letter_snippet_admin does not support migration down.";
        return false;
    }
}
