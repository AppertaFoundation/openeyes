<?php

class m190426_090359_correct_the_values_of_ophciexamination_drgrading_nscretinopathy extends CDbMigration
{
    const NSCRETINOPATHY_TABLE = 'ophciexamination_drgrading_nscretinopathy';

    const name_changes = [
    'R0' => 'R0 - No retinopathy',
    'R1' => 'R1 – Mild NPDR',
    'R2' => 'R2 – Moderate NPDR',
    'R3A' => 'R3A – Active PDR',
    'R3S' => 'R3S – Stable treated PDR',
    'U' => 'U - Ungradable',
    ];

    const old_display_orders = [
    'R0' => 1,
    'R1' => 2,
    'R2' => 3,
    'R3S' => 4,
    'R3A' => 5,
    'U' => 6,
    ];

    const name_of_new_option = 'R2 – Severe NPDR';

    const new_display_orders = [
    self::name_changes['R0'] => 1,
    self::name_changes['R1'] => 2,
    self::name_changes['R2'] => 3,
    self::name_of_new_option => 4,
    self::name_changes['R3A'] => 5,
    self::name_changes['R3S'] => 6,
    self::name_changes['U'] => 7,
    ];

    const R2_old_description =
    'Pre-proliferative retinopathy' .
    '<ul>' .
      '<li>Venous beading</li>' .
      '<li>Venous reduplication</li>' .
      '<li>Multiple blot haemorrhages</li>' .
      '<li>Intraretinal microvascular abnormality (IRMA)</li>'.
    '</ul>';

    const R2_new_description = self::R2_old_description . '<br>' .
    'Hemorrhages and/or microaneurysms ≥ standard photograph 2A*; and/or:' .
    '<ul>' .
      '<li>soft exudates</li>' .
    '</ul>';

    const R2_new_option_description = self::R2_old_description . '<br>' .
    '<ul>' .
      '<li>Soft exudates, and intraretinal microvascular abnormalities all definitely present' .
        'in at least two of fields four through seven</li>' .
      '<li>or two of the preceding three lesions present in at least two of fields four through' .
        'seven and hemorrhages and microaneurysms present in these four fields, equaling or exceeding' .
        'standard photo 2A in at least one of them</li>' .
      '<li>or intraretinal microvascular abnormalities present in each of fields four through seven and' .
        'equaling or exceeding standard photograph 8A in at least two of them</li>' .
    '</ul>';

    private function getFieldFromName($name, $field) {
        return $this->dbConnection->createCommand()
        ->select($field)
        ->from(self::NSCRETINOPATHY_TABLE)
        ->where('name = :name', [':name' => $name])
        ->queryScalar();
    }

    private function getIdFromName($name){
        return $this->getFieldFromName($name, 'id');
    }

    private function updateField($name, $field, $new_value) {
        $this->update(self::NSCRETINOPATHY_TABLE,
        [$field => $new_value],
        'id = :id',
        [':id' => $this->getIdFromName($name)]
        );
    }

    private function updateName($old_name, $new_name) {
        $this->updateField($old_name, 'name', $new_name);
    }

    private function updateDisplayOrder($name, $display_order) {
        $this->updateField($name, 'display_order', $display_order);
    }

    private function updateDescription($name, $description) {
        $this->updateField($name, 'description', $description);
    }

    private function updateDisplayOrders($display_orders) {
        foreach ($display_orders as $name => $display_order) {
            $this->updateDisplayOrder($name, $display_order);
        }
    }

    public function safeUp()
    {
        foreach (self::name_changes as $old_name => $new_name) {
            $this->updateName($old_name, $new_name);
        }

        $this->insert(self::NSCRETINOPATHY_TABLE, [
        'name' => self::name_of_new_option,
        'description' => self::R2_new_option_description,
        'class' => 'severe',
        'code' => 'SR',
        ]);

        $this->updateDisplayOrders(self::new_display_orders);
        $this->updateDescription(self::name_changes['R2'], self::R2_new_description);
    }

    public function safeDown()
    {
        $this->updateDescription(self::name_changes['R2'], self::R2_old_description);
        $this->delete(self::NSCRETINOPATHY_TABLE, 'id = :id',
        [':id' => $this->getIdFromName(self::name_of_new_option)]);

        foreach (self::name_changes as $old_name => $new_name) {
            $this->updateName($new_name, $old_name);
        }

        $this->updateDisplayOrders(self::old_display_orders);
    }
}