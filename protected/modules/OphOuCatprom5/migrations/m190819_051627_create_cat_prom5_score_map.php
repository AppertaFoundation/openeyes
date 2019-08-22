<?php

class m190819_051627_create_cat_prom5_score_map extends OEMigration
{

	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->createOETable('cat_prom5_score_map', array(
			'id' => 'pk',
			'raw_score' => 'int(3)',
			'rasch_measure' => 'DECIMAL(5,2)',
		),false);
		$this->insert('cat_prom5_score_map', array('raw_score'=> 0, 'rasch_measure'=> -9.18));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 1, 'rasch_measure'=> -6.80));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 2, 'rasch_measure'=> -4.92));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 3, 'rasch_measure'=> -4.03));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 4, 'rasch_measure'=> -3.37));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 5, 'rasch_measure'=> -2.81));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 6, 'rasch_measure'=> -2.29));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 7, 'rasch_measure'=> -1.80));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 8, 'rasch_measure'=> -1.31));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 9, 'rasch_measure'=> -0.82));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 10, 'rasch_measure'=> -0.32));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 11, 'rasch_measure'=> 0.18));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 12, 'rasch_measure'=> 0.69));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 13, 'rasch_measure'=> 1.22));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 14, 'rasch_measure'=> 1.76));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 15, 'rasch_measure'=> 2.33));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 16, 'rasch_measure'=> 2.93));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 17, 'rasch_measure'=> 3.56));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 18, 'rasch_measure'=> 4.23));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 19, 'rasch_measure'=> 4.95));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 20, 'rasch_measure'=> 6.01));
		$this->insert('cat_prom5_score_map', array('raw_score'=> 21, 'rasch_measure'=> 7.45));

	}

	public function safeDown()
	{
		$this->dropTable('cat_prom5_score_map');
	}
}