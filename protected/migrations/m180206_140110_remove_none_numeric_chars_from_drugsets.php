<?php

class m180206_140110_remove_none_numeric_chars_from_drugsets extends CDbMigration
{
    public function safeUp()
    {
        $dataProvider = new CActiveDataProvider('DrugSetItem');
        $iterator = new CDataProviderIterator($dataProvider);

        $data['model'] = 'DrugSetItem';
        $data['table'] = 'drug_set_item';
        $data['changes'] = [];

        foreach ($iterator as $item) {
            $item_data['old_dose'] = $item->dose;

            //remove all non numeric character
            $item->dose = preg_replace('~\D~', '', $item->dose);
            $item_data['new_dose'] = $item->dose;

            if ($item->save()) {
                $data['changes'][] = ['id' => $item->id] + $item_data;
                \OELog::log("DrugSetItem(id:{$item->id}) dose changeg from '{$item_data['old_dose']}' to '{$item_data['new_dose']}'");
            }
        }
        \Audit::add('admin', 'update', ("<pre>" . print_r($data, true) . "</pre>"), '', ['model' => 'DrugSetItem']);
        $data = [];

        // Remove none numeric characters from ophdrprescription_item_tapers
        $dataProvider = new CActiveDataProvider('DrugSetItemTaper');
        $iterator = new CDataProviderIterator($dataProvider);

        $data['model'] = 'DrugSetItemTaper';
        $data['table'] = 'ophdrprescription_item_taper';
        $data['changes'] = [];

        foreach ($iterator as $taper) {
            $taper_data['old_dose'] = $taper->dose;

            //remove all non numeric character
            $taper->dose = preg_replace('~\D~', '', $taper->dose);
            $taper_data['new_dose'] = $taper->dose;

            if ($taper->save()) {
                $data['changes'][] = ['id' => $taper->id] + $taper_data;
                \OELog::log("DrugSetItem(id:{$taper->id}) dose changeg from '{$taper_data['old_dose']}' to '{$taper_data['new_dose']}'");
            }
        }
        \Audit::add('admin', 'update', ("<pre>" . print_r($data, true) . "</pre>"), '', ['model' => 'DrugSetItemTaper']);

        return true;
    }

    public function safeDown()
    {
        echo "m180206_140110_remove_none_numeric_chars_from_drugsets does not support migration down.\n";
        return false;
    }
}