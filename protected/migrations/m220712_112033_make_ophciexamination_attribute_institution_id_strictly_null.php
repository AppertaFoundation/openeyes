<?php

class m220712_112033_make_ophciexamination_attribute_institution_id_strictly_null extends OEMigration
{
	public function safeUp()
	{
		$institutions =$this->dbConnection->createCommand('SELECT i.id FROM institution i JOIN institution_authentication ia ON ia.institution_id = i.id')->queryColumn();

		if (count($institutions) === 0) {
			// No tenanted institutions, so use all institutions instead
			$institutions = $this->dbConnection->createCommand('SELECT id FROM institution')->queryColumn();

			if (count($institutions) === 0) {
				// No institutions at all, simply return here to prevent an error during the migration process
				return;
			}
		}

		// Simply set the existing attributes with NULL institution_id values to use the first institution found in the above query...
		$first = array_shift($institutions);

		$attributes_without_institutions = $this->dbConnection->createCommand('SELECT id, name, label, is_multiselect, display_order FROM ophciexamination_attribute WHERE institution_id IS NULL')
															  ->queryAll();

		// ...as opposed to the other institutions which require a deep copy of those attributes and their child tables,
		// ophciexamination_attribute_element, ophciexamination_attribute_option and ophciexamination_attribute_option_exclude
		foreach ($attributes_without_institutions as $attribute) {
			$original_attribute_id = $attribute['id'];

			// To reuse the rest of the array without manually copying the other fields out
			unset($attribute['id']);

			$new_attribute_ids = array_map(
				function ($institution_id) use ($attribute) {
					$this->insert('ophciexamination_attribute', array_merge($attribute, ['institution_id' => $institution_id]));
					return $this->dbConnection->getLastInsertID();
				},
				$institutions
			);

			$attribute_elements = $this->dbConnection->createCommand('SELECT id, element_type_id FROM ophciexamination_attribute_element WHERE attribute_id = :old_id')
													 ->queryAll(true, [':old_id' => $original_attribute_id]);

			foreach ($attribute_elements as $attribute_element) {
				$new_attribute_element_ids = array_map(function ($attribute_id) use ($attribute_element) {
					$this->insert('ophciexamination_attribute_element', ['attribute_id' => $attribute_id, 'element_type_id' => $attribute_element['element_type_id']]);
					return $this->dbConnection->getLastInsertID();
				}, $new_attribute_ids);

				$attribute_options = $this->dbConnection->createCommand(
					'SELECT id, `value`, `delimiter`, subspecialty_id, display_order FROM ophciexamination_attribute_option WHERE attribute_element_id = :old_id'
				)->queryAll(true, [':old_id' => $attribute_element['id']]);

				foreach ($attribute_options as $attribute_option) {
					$original_option_id = $attribute_option['id'];

					// To reuse the rest of the array without manually copying the other fields out
					unset($attribute_option['id']);

					foreach ($new_attribute_element_ids as $attribute_element_id) {
						$this->insert('ophciexamination_attribute_option', array_merge($attribute_option, ['attribute_element_id' => $attribute_element_id]));
						$new_option_id = $this->dbConnection->getLastInsertID();

						// The lowest level of the deep copy, so no need to keep track of new ids...
						$this->dbConnection->createCommand(
							'INSERT INTO ophciexamination_attribute_option_exclude (option_id, subspecialty_id) ' .
							'SELECT :new_option_id, subspecialty_id FROM ophciexamination_attribute_option_exclude WHERE option_id = :old_option_id'
						)->execute([':old_option_id' => $original_option_id, ':new_option_id' => $new_option_id]);
					}
				}
			}
		}

		// Remap the original values onto the first tenanted institution found
		$this->update('ophciexamination_attribute', ['institution_id' => $first], 'institution_id IS NULL');

		$this->alterColumn('ophciexamination_attribute', 'institution_id', 'int(10) unsigned NOT NULL');
	}

	public function safeDown()
	{
		$this->alterColumn('ophciexamination_attribute', 'institution_id', 'int(10) unsigned');
	}
}
