<?php

class m181107_031357_cera_ethnic_groups extends OEMigration
{

  // Use safeUp/safeDown to do migration with transaction
  public function safeUp()
  {

    $this->addColumn('ethnic_group', 'tag', 'varchar(20)');

    $ethnicGroups = EthnicGroup::model()->findAll();
    foreach ($ethnicGroups as $ethnicity) {
      $this->update('ethnic_group', array('tag'=>'UK'), 'id='.$ethnicity->id);
    }


    $this->insert('ethnic_group', array('name' => 'Australia', 'code' => 'A', 'display_order' => '10', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'China', 'code' => 'C', 'display_order' => '20', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'Greece', 'code' => 'G', 'display_order' => '30', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'Italy', 'code' => 'I', 'display_order' => '40', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'New Zealand', 'code' => 'N', 'display_order' => '50', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'United Kingdom', 'code' => 'U', 'display_order' => '60', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'India', 'code' => 'L', 'display_order' => '70', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'Other', 'code' => 'O', 'display_order' => '80', 'tag'=>'CERA'));
    $this->insert('ethnic_group', array('name' => 'Don\'t know', 'code' => 'D', 'display_order' => '90', 'tag'=>'CERA'));


  }

  public function safeDown()
  {
    $this->dropColumn('ethnic_group', 'tag');
    $this->delete('ethnic_group', 'name = "Australia"');
    $this->delete('ethnic_group', 'name = "China"');
    $this->delete('ethnic_group', 'name = "Greece"');
    $this->delete('ethnic_group', 'name = "Italy"');
    $this->delete('ethnic_group', 'name = "New Zealand"');
    $this->delete('ethnic_group', 'name = "United Kingdom"');
    $this->delete('ethnic_group', 'name = "India"');
    $this->delete('ethnic_group', 'name = "Other"');
    $this->delete('ethnic_group', 'name = "Don\'t know"');

  }

}