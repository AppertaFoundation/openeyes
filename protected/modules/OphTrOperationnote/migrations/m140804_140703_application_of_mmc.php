<?php

class m140804_140703_application_of_mmc extends OEMigration
{
	public function safeUp()
	{
		$this->createOETable(
			'ophtroperationnote_antimetabolite_application_type',
			array(
				'id' => 'pk',
				'name' => 'string not null',
				'display_order' => 'integer unsigned not null',
				'constraint ophtroperationnote_antimetabolite_application_type_unique unique (name)',
			),
			true
		);

		$this->createOETable(
			'ophtroperationnote_mmc_concentration',
			array(
				'id' => 'pk',
				'value' => 'decimal(2,1) not null',
				'constraint ophtroperationnote_mmc_concentration_unique unique (value)',
			),
			true
		);

		$this->createOETable(
			'ophtroperationnote_mmc_volume',
			array(
				'id' => 'pk',
				'value' => 'decimal(2,1) not null',
				'constraint ophtroperationnote_mmc_volume_unique unique (value)',
			),
			true
		);

		$this->createOETable(
			'et_ophtroperationnote_mmc',
			array(
				'id' => 'pk',
				'event_id' => 'integer unsigned not null',
				'application_type_id' => 'integer not null',
				'concentration_id' => 'integer not null',
				'volume_id' => 'integer',
				'duration' => 'integer',
				'number' => 'integer',
				'washed' => 'boolean',
				'constraint et_ophtroperationnote_mmc_event_id_fk foreign key (event_id) references event (id)',
				'constraint et_ophtroperationnote_mmc_application_type_id_fk foreign key (application_type_id) references ophtroperationnote_antimetabolite_application_type (id)',
				'constraint et_ophtroperationnote_mmc_concentration_id_fk foreign key (concentration_id) references ophtroperationnote_mmc_concentration (id)',
				'constraint et_ophtroperationnote_mmc_volume_id_fk foreign key (volume_id) references ophtroperationnote_mmc_volume (id)',
			),
			true
		);

		$element_type_id = $this->createElementType(
			'OphTrOperationnote',
			'Application of MMC',
			array(
				'class_name' => 'Element_OphTrOperationnote_Mmc',
				'display_order' => 20,
				'parent_name' => 'ProcedureList'
			)
		);

		$proc_id = $this->dbConnection->createCommand('select id from proc where term = "Application of MMC"')->queryScalar();
		if ($proc_id) {
			$this->insert('ophtroperationnote_procedure_element', array('procedure_id' => $proc_id, 'element_type_id' => $element_type_id));
		}

		$this->initialiseData(__DIR__);
	}

	public function safeDown()
	{
		$element_type_id = $this->dbConnection->createCommand('select id from element_type where class_name = "Element_OphTrOperationnote_Mmc"')->queryScalar();

		$this->delete('ophtroperationnote_procedure_element', 'element_type_id = ?', array($element_type_id));
		$this->delete('element_type', 'id = ?', array($element_type_id));

		$this->dropOETable('et_ophtroperationnote_mmc', true);
		$this->dropOETable('ophtroperationnote_mmc_volume', true);
		$this->dropOETable('ophtroperationnote_mmc_concentration', true);
		$this->dropOETable('ophtroperationnote_antimetabolite_application_type', true);
	}
}
