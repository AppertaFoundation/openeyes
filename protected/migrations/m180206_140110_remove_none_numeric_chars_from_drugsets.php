<?php

class m180206_140110_remove_none_numeric_chars_from_drugsets extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CException
     * @throws Exception
     */
    public function safeUp()
    {
        $iterator = $this->dbConnection->createCommand('SELECT id, dose FROM drug_set_item')
            ->queryAll();

        $data['model'] = 'DrugSetItem';
        $data['table'] = 'drug_set_item';
        $data['changes'] = [];

        foreach ($iterator as $item) {
            $item_data['old_dose'] = $item['dose'];

            //remove all non numeric character
            $new_dose = preg_replace('~\D~', '', $item['dose']);
            $item_data['new_dose'] = $new_dose;

            $this->update(
                'drug_set_item',
                array('dose' => $new_dose),
                'id = :id',
                array(':id' => $item['id'])
            );
            $data['changes'][] = ['id' => $item['id']] + $item_data;
            OELog::log("DrugSetItem(id:{$item['id']}) dose changeg from '{$item_data['old_dose']}' to '{$item_data['new_dose']}'");
        }
        Audit::add('admin', 'update', ('<pre>' . print_r($data, true) . '</pre>'), '', ['model' => 'DrugSetItem']);
        $data = [];

        // Remove none numeric characters from ophdrprescription_item_tapers
        $iterator = $this->dbConnection->createCommand('SELECT id, dose FROM drug_set_item_taper')
            ->queryAll();

        $data['model'] = 'DrugSetItemTaper';
        $data['table'] = 'ophdrprescription_item_taper';
        $data['changes'] = [];

        foreach ($iterator as $taper) {
            $taper_data['old_dose'] = $taper['dose'];

            //remove all non numeric character
            $new_dose = preg_replace('~\D~', '', $taper['dose']);
            $taper_data['new_dose'] = $new_dose;

            $this->update(
                'drug_set_item_taper',
                array('dose' => $new_dose),
                'id = :id',
                array(':id' => $taper['id'])
            );

            $data['changes'][] = ['id' => $taper['id']] + $taper_data;
            OELog::log("DrugSetItem(id:{$taper['id']}) dose changeg from '{$taper_data['old_dose']}' to '{$taper_data['new_dose']}'");
        }
        Audit::add('admin', 'update', ('<pre>' . print_r($data, true) . '</pre>'), '', ['model' => 'DrugSetItemTaper']);

        return true;
    }

    public function safeDown()
    {
        echo "m180206_140110_remove_none_numeric_chars_from_drugsets does not support migration down.\n";
        return false;
    }
}
