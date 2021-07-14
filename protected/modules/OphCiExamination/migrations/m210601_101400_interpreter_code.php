<?php

class m210601_101400_interpreter_code extends OEMigration
{
    public function safeUp()
    {
        if (!isset($this->dbConnection->schema->getTable('language')->columns['interpreter_pas_code'])) {
            $this->addOEColumn(
                'language',
                'interpreter_pas_code',
                'varchar(5) DEFAULT null',
                true
            );
        }

        $this->update('language', ['interpreter_pas_code' => 'alb'], 'pas_term="alb"');
        $this->update('language', ['interpreter_pas_code' => 'ara'], 'pas_term="ara"');
        $this->update('language', ['interpreter_pas_code' => 'span'], 'pas_term="spa"');
        $this->update('language', ['interpreter_pas_code' => 'bul'], 'pas_term="bul"');
        $this->update('language', ['interpreter_pas_code' => 'ben'], 'pas_term="ben"');
        $this->update('language', ['interpreter_pas_code' => 'heb'], 'pas_term="heb"');
        $this->update('language', ['interpreter_pas_code' => 'fre'], 'pas_term="fre"');
        $this->update('language', ['interpreter_pas_code' => 'guj'], 'pas_term="guj"');
        $this->update('language', ['interpreter_pas_code' => 'jap'], 'pas_term="jpn"');
        $this->update('language', ['interpreter_pas_code' => 'pol'], 'pas_term="pol"');
        $this->update('language', ['interpreter_pas_code' => 'pun'], 'pas_term="pan"');
        $this->update('language', ['interpreter_pas_code' => 'rus'], 'pas_term="rus"');
        $this->update('language', ['interpreter_pas_code' => 'som'], 'pas_term="som"');
        $this->update('language', ['interpreter_pas_code' => 'tam'], 'pas_term="tam"');
        $this->update('language', ['interpreter_pas_code' => 'tur'], 'pas_term="tur"');
        $this->update('language', ['interpreter_pas_code' => 'urd'], 'pas_term="urd"');
        $this->update('language', ['interpreter_pas_code' => 'po'], 'pas_term="por"');
        $this->update('language', ['interpreter_pas_code' => 'kur'], 'pas_term="kur"');
        $this->update('language', ['interpreter_pas_code' => 'tig'], 'pas_term="tir"');
        $this->update('language', ['interpreter_pas_code' => 'rom'], 'pas_term="rum"');
        $this->update('language', ['interpreter_pas_code' => 'ita'], 'pas_term="ita"');
        $this->update('language', ['interpreter_pas_code' => 'amh'], 'pas_term="amh"');
        $this->update('language', ['interpreter_pas_code' => 'man'], 'pas_term="chm"');

        $this->update('language', ['interpreter_pas_code' => 'asl'], 'pas_term="q2"');
        $this->update('language', ['interpreter_pas_code' => 'bsl'], 'pas_term="q4"');

        $this->update('language', ['interpreter_pas_code' => 'can'], 'pas_term="chi"');
        $this->update('language', ['interpreter_pas_code' => 'far'], 'pas_term="per"');
        $this->update('language', ['interpreter_pas_code' => 'urd'], 'pas_term="urd"');
        $this->update('language', ['interpreter_pas_code' => 'gre'], 'pas_term="gre"');
    }

    public function safeDown()
    {
        $this->dropOEColumn('language', 'interpreter_pas_code', true);
    }
}
