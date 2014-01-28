<?php

class m131104_112734_authitems extends CDbMigration
{
	private $authitems = array(
		array('name' => 'OprnLogin', 'type' => 0),
		array('name' => 'OprnViewClinical', 'type' => 0),
		array('name' => 'OprnPrint', 'type' => 0),
		array('name' => 'OprnViewProtectedFile', 'type' => 0),
		array('name' => 'OprnEditAllergy', 'type' => 0),
		array('name' => 'OprnEditContact', 'type' => 0),
		array('name' => 'OprnEditFamilyHistory', 'type' => 0),
		array('name' => 'OprnEditMedication', 'type' => 0),
		array('name' => 'OprnEditOphInfo', 'type' => 0),
		array('name' => 'OprnEditOtherOphDiagnosis', 'type' => 0),
		array('name' => 'OprnEditPatientInfo', 'type' => 0),
		array('name' => 'OprnEditPreviousOperation', 'type' => 0),
		array('name' => 'OprnEditSystemicDiagnosis', 'type' => 0),
		array('name' => 'OprnCreateEpisode', 'type' => 0),
		array('name' => 'OprnEditEpisode', 'type' => 0, 'bizrule' => 'canEditEpisode'),
		array('name' => 'OprnCreateEvent', 'type' => 0, 'bizrule' => 'canCreateEvent'),
		array('name' => 'OprnEditEvent', 'type' => 0, 'bizrule' => 'canEditEvent'),
		array('name' => 'OprnDeleteEvent', 'type' => 0, 'bizrule' => 'canDeleteEvent'),

		array('name' => 'TaskLogin', 'type' => 1),
		array('name' => 'TaskViewClinical', 'type' => 1),
		array('name' => 'TaskPrint', 'type' => 1),
		array('name' => 'TaskViewProtectedFile', 'type' => 1),
		array('name' => 'TaskEditPatientData', 'type' => 1),
		array('name' => 'TaskEditEpisode', 'type' => 1),
		array('name' => 'TaskEditEvent', 'type' => 1),
		array('name' => 'TaskPrescribe', 'type' => 1),

		array('name' => 'User', 'type' => 2),
		array('name' => 'View clinical', 'type' => 2),
		array('name' => 'Print', 'type' => 2),
		array('name' => 'Edit', 'type' => 2),
		array('name' => 'Prescribe', 'type' => 2)
	);

	private $parents = array(
		'OprnLogin' => 'TaskLogin',
		'OprnViewClinical' => 'TaskViewClinical',
		'OprnPrint' => 'TaskPrint',
		'OprnViewProtectedFile' => 'TaskViewProtectedFile',
		'OprnEditAllergy' => 'TaskEditPatientData',
		'OprnEditContact' => 'TaskEditPatientData',
		'OprnEditFamilyHistory' => 'TaskEditPatientData',
		'OprnEditMedication' => 'TaskEditPatientData',
		'OprnEditOphInfo' => 'TaskEditPatientData',
		'OprnEditOtherOphDiagnosis' => 'TaskEditPatientData',
		'OprnEditPatientInfo' => 'TaskEditPatientData',
		'OprnEditPreviousOperation' => 'TaskEditPatientData',
		'OprnEditSystemicDiagnosis' => 'TaskEditPatientData',
		'OprnCreateEpisode' => 'TaskEditEpisode',
		'OprnEditEpisode' => 'TaskEditEpisode',
		'OprnCreateEvent' => 'TaskEditEvent',
		'OprnEditEvent' => 'TaskEditEvent',
		'OprnDeleteEvent' => 'TaskEditEvent',

		'TaskLogin' => 'User',
		'TaskViewClinical' => 'View clinical',
		'TaskPrint' => 'Print',
		'TaskViewProtectedFile' => 'Edit',
		'TaskEditPatientData' => 'Edit',
		'TaskEditEpisode' => 'Edit',
		'TaskEditEvent' => 'Edit',
		'TaskPrescribe' => 'Prescribe'
	);

	public function up()
	{
		$this->createTable(
			'authitem_type',
			array(
				'id tinyint unsigned not null primary key',
				'name varchar(85) not null unique'
			),
			'engine=innodb charset=utf8 collate=utf8_unicode_ci'
		);
		$this->getDbConnection()->getCommandBuilder()->createMultipleInsertCommand(
			'authitem_type',
			array(
				array('id' => 0, 'name' => 'operation'),
				array('id' => 1, 'name' => 'task'),
				array('id' => 2, 'name' => 'role')
			)
		)->execute();

		$this->alterColumn('authitem', 'type', 'tinyint unsigned not null');
		$this->addForeignKey('authitem_type_fk', 'authitem', 'type', 'authitem_type', 'id');

		$this->addForeignKey('authitemchild_parent_fk', 'authitemchild', 'parent', 'authitem', 'name');
		$this->addForeignKey('authitemchild_child_fk', 'authitemchild', 'child', 'authitem', 'name');

		$this->addForeignKey('authassignment_itemname_fk', 'authassignment', 'itemname', 'authitem', 'name');
		$this->alterColumn('authassignment', 'userid', 'int unsigned not null');
		$this->addForeignKey('authassignment_userid_fk', 'authassignment', 'userid', 'user', 'id');

		foreach ($this->authitems as $authitem) {
			$this->insert('authitem', $authitem);
		}

		foreach ($this->parents as $child => $parent) {
			$this->insert('authitemchild', array('parent' => $parent, 'child' => $child));
		}

		$this->execute('insert into authassignment (itemname, userid) select "User", id from user where access_level >= 1');
		$this->execute('insert into authassignment (itemname, userid) select "View clinical", id from user where access_level >= 2');
		$this->execute('insert into authassignment (itemname, userid) select "Print", id from user where access_level >= 3');
		$this->execute('insert into authassignment (itemname, userid) select "Edit", id from user where access_level >= 4');
		$this->execute('insert into authassignment (itemname, userid) select "Prescribe", id from user where access_level >= 5');
	}

	public function down()
	{
		$this->delete('authassignment', 'itemname in ("User", "View clinical", "Print", "Edit", "Prescribe")');

		foreach ($this->parents as $child => $parent) {
			$this->delete('authitemchild', 'parent = ? and child = ?', array($parent, $child));
		}

		foreach ($this->authitems as $authitem) {
			$this->delete('authitem', 'name = ?', array($authitem['name']));
		}

		$this->dropForeignKey('authassignment_itemname_fk', 'authassignment');
		$this->dropForeignKey('authassignment_userid_fk', 'authassignment');
		$this->alterColumn('authassignment', 'userid', 'varchar(64) not null');

		$this->dropForeignKey('authitemchild_parent_fk', 'authitemchild');
		$this->dropForeignKey('authitemchild_child_fk', 'authitemchild');

		$this->dropForeignKey('authitem_type_fk', 'authitem');
		$this->alterColumn('authitem', 'type', 'int not null');
		$this->dropTable('authitem_type');
	}
}
