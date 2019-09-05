<?php

class m181127_142335_add_disorder_metallic_body extends CDbMigration
{
    public function up()
    {
        $this->insert('disorder', [
            'id' => 422321007,
            'fully_specified_name' => 'Metallic foreign body',
            'term' => 'Metallic foreign body',
        ]);
    }

    public function down()
    {
        $this->delete('disorder', 'id = ?', [422321007]);
    }
}