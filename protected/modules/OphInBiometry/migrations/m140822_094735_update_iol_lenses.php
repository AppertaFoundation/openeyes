<?php

class m140822_094735_update_iol_lenses extends CDbMigration
{
    public function up()
    {
        foreach (array(
                'SN60WF' => array(
                    'acon' => 119.0,
                    'sf' => null,
                    'pACD' => 5.64,
                    'a0' => -0.782,
                    'a1' => 0.206,
                    'a2' => 0.221,
                ),
                'MA60AC' => array(
                    'acon' => 119.1,
                    'pACD' => 5.59,
                    'sf' => null,
                    'a0' => 1.498,
                    'a1' => 0.011,
                    'a2' => 0.147,
                ),
                'SA60AT' => array(
                    'acon' => 118.7,
                    'pACD' => 5.41,
                    'sf' => null,
                    'a0' => -0.091,
                    'a1' => 0.231,
                    'a2' => 0.179,
                ),
                'MTA3UO' => array(
                    'acon' => 115.54,
                    'pACD' => 3.53,
                    'a0' => -0.705,
                    'a1' => 0.4,
                    'a2' => 0.1,
                ),
            ) as $name => $attributes) {
            $this->update('ophinbiometry_lenstype_lens', $attributes, "name = '$name'");
        }
    }

    public function down()
    {
        foreach (array(
                'SN60WF' => array(
                    'acon' => 118.0,
                    'sf' => 1.85,
                    'pACD' => null,
                    'a0' => null,
                    'a1' => null,
                    'a2' => null,
                ),
                'MA60AC' => array(
                    'acon' => 118.9,
                    'pACD' => 1.90,
                    'sf' => null,
                    'a0' => null,
                    'a1' => null,
                    'a2' => null,
                ),
                'SA60AT' => array(
                    'acon' => 118.7,
                    'pACD' => null,
                    'sf' => null,
                    'a0' => -0.091,
                    'a1' => 0.231,
                    'a2' => 0.179,
                ),
                'MTA3UO' => array(
                    'acon' => 115.54,
                    'pACD' => 3.53,
                    'a0' => -0.705,
                    'a1' => 0.4,
                    'a2' => 0.1,
                ),
            ) as $name => $attributes) {
            $this->update('ophinbiometry_lenstype_lens', $attributes, "name = '$name'");
        }
    }
}
