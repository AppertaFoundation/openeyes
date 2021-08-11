<?php

class m210629_122947_add_missing_interpreter_codes extends CDbMigration
{
    private array $codes = [
        "A" => "LARGE FONT APPT LETTER ONLY",
        "B" => "BRAILLE LETTER REQUIRED",
        "BENS" => "BENGALI (BANGLA SYLHETI)",
        "BRP" => "BRAZILIAN PORTUGUESE",
        "BSLD" =>  "BSL - DEAFBLIND COMUNICATION",
        "BSLL" => "BSL - LIP-SPEAKING",
        "BSLS" => "BSL - SPEECH TO TEXT OPERATOR",
        "D" => "BRAILLE LETTER + TEL CALL",
        "F" => "BRAILLE LETTER OR TEL CALL",
        "GHA" => "GHANA AKAN INTERPRETER",
        "GHT" => "GHANA TWI INTERPRETER",
        "L" => "STANDARD LETTER ONLY REQUIRED",
        "LIT" => "LITHUANIAN INTERPRETER",
        "OTH" => "OTHER LANG INTERPRETER NEEDED",
        "P" => "STANDARD / BRAILLE / TEL CALL",
        "POE" => "PORTUGUESE EUROPEAN",
        "S" => "STANDARD LETTER OR TEL CALL",
        "T" => "CONFIRM BY TELEPHONE",
        "X" => "REQUIREMENTS NOT KNOWN",
    ];

    public function safeUp()
    {
        $existing_codes = $this->dbConnection
            ->createCommand("SELECT DISTINCT LOWER(`interpreter_pas_code`) FROM `language`")
            ->queryColumn();
        $inserts = [];
        foreach ($this->codes as $code => $name) {
            $code = strtolower($code);
            if (!in_array($code, $existing_codes)) {
                $inserts[] = [
                    "interpreter_pas_code" => $code,
                    "name" => ucwords(strtolower($name))
                ];
            }
        }
        if (!empty($inserts)) {
            $this->insertMultiple("language", $inserts);
        }
    }

    public function safeDown()
    {
        echo "m210629_122947_add_missing_interpreter_codes does not support migration down.\n";
        return false;
    }
}
