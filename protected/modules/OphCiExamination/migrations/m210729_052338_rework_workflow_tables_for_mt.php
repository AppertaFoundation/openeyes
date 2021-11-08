<?php

class m210729_052338_rework_workflow_tables_for_mt extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->dropOEColumn('ophciexamination_workflow_rule', 'institution_id', true);

        $connection = $this->getDbConnection();
        $connection->createCommand(
            "INSERT INTO ophciexamination_workflow
			(
				institution_id,
				`name`,
    			last_modified_user_id,
    			last_modified_date,
    			created_user_id,
    			created_date
			)
			SELECT 
				firm.institution_id AS institution_id,
				ophciexamination_workflow.name AS `name`,
				ophciexamination_workflow.last_modified_user_id AS last_modified_user_id,
    			ophciexamination_workflow.last_modified_date AS last_modified_date,
    			ophciexamination_workflow.created_user_id AS created_user_id,
    			ophciexamination_workflow.created_date AS created_date
			FROM ophciexamination_workflow
			JOIN ophciexamination_workflow_rule
			  ON ophciexamination_workflow_rule.workflow_id = ophciexamination_workflow.id
			JOIN firm
			  ON ophciexamination_workflow_rule.firm_id = firm.id
			WHERE ophciexamination_workflow.institution_id IS NULL

			UNION

			SELECT
				`event`.institution_id AS institution_id,
				ophciexamination_workflow.name AS `name`,
				ophciexamination_workflow.last_modified_user_id AS last_modified_user_id,
    			ophciexamination_workflow.last_modified_date AS last_modified_date,
    			ophciexamination_workflow.created_user_id AS created_user_id,
    			ophciexamination_workflow.created_date AS created_date
			FROM ophciexamination_workflow
			JOIN ophciexamination_element_set
			  ON ophciexamination_element_set.workflow_id = ophciexamination_workflow.id
			JOIN ophciexamination_event_elementset_assignment
			  ON ophciexamination_event_elementset_assignment.step_id = ophciexamination_element_set.id
			JOIN `event`
			  ON ophciexamination_event_elementset_assignment.event_id = `event`.id
			WHERE ophciexamination_workflow.institution_id IS NULL"
        )->execute();

        // Duplicate workflows with no institution that haven't been migrated above to all institutions
        $connection->createCommand(
            "INSERT INTO ophciexamination_workflow
			(
				institution_id,
				`name`,
    			last_modified_user_id,
    			last_modified_date,
    			created_user_id,
    			created_date
			)
			SELECT
				institution.id,
				ophciexamination_workflow.name,
				ophciexamination_workflow.last_modified_user_id,
    			ophciexamination_workflow.last_modified_date,
    			ophciexamination_workflow.created_user_id,
    			ophciexamination_workflow.created_date
			FROM ophciexamination_workflow
			CROSS JOIN institution
			WHERE ophciexamination_workflow.institution_id IS NULL
			  AND NOT EXISTS (
				  SELECT *
				  FROM ophciexamination_workflow_rule
				  WHERE ophciexamination_workflow_rule.workflow_id = ophciexamination_workflow.id
				    AND ophciexamination_workflow_rule.firm_id IS NOT NULL
			  )
			  AND NOT EXISTS (
				  SELECT *
				  FROM ophciexamination_element_set
				  JOIN ophciexamination_event_elementset_assignment
			  		ON ophciexamination_event_elementset_assignment.step_id = ophciexamination_element_set.id
				  JOIN `event`
					ON ophciexamination_event_elementset_assignment.event_id = `event`.id
			      WHERE ophciexamination_element_set.workflow_id = ophciexamination_workflow.id
			  )"
        )->execute();

        // Find all workflow migrations
        $connection->createCommand(
            "CREATE TEMPORARY TABLE temp_workflow_migration
			SELECT DISTINCT
				old_workflow.id AS old_id,
				new_workflow.id AS new_id,
				new_workflow.institution_id AS institution_id
			FROM ophciexamination_workflow AS old_workflow
			JOIN ophciexamination_workflow AS new_workflow
			  ON old_workflow.name = new_workflow.name
			 AND old_workflow.last_modified_user_id = new_workflow.last_modified_user_id
    		 AND old_workflow.last_modified_date = new_workflow.last_modified_date
    		 AND old_workflow.created_user_id = new_workflow.created_user_id
    		 AND old_workflow.created_date = new_workflow.created_date
			WHERE old_workflow.institution_id IS NULL
			  AND new_workflow.institution_id IS NOT NULL"
        )->execute();

        // Duplicate rules to the new workflows
        $connection->createCommand(
            "INSERT INTO ophciexamination_workflow_rule
			(
    			workflow_id,
				last_modified_user_id,
    			last_modified_date,
    			created_user_id,
    			created_date,
    			subspecialty_id,
    			firm_id,
    			episode_status_id
			)
			SELECT
				temp_workflow_migration.new_id,
				ophciexamination_workflow_rule.last_modified_user_id,
				ophciexamination_workflow_rule.last_modified_date,
				ophciexamination_workflow_rule.created_user_id,
				ophciexamination_workflow_rule.created_date,
				ophciexamination_workflow_rule.subspecialty_id,
				ophciexamination_workflow_rule.firm_id,
				ophciexamination_workflow_rule.episode_status_id
			FROM ophciexamination_workflow_rule
			JOIN temp_workflow_migration
			  ON ophciexamination_workflow_rule.workflow_id = temp_workflow_migration.old_id"
        )->execute();

        // Duplicate element sets to new workflows
        $connection->createCommand(
            "INSERT INTO ophciexamination_element_set
			(
    			workflow_id,
				`name`,
    			last_modified_user_id,
    			last_modified_date,
    			created_user_id,
    			created_date,
    			position,
    			is_active,
    			display_order_edited
			)
			SELECT
				temp_workflow_migration.new_id,
				ophciexamination_element_set.name,
    			ophciexamination_element_set.last_modified_user_id,
    			ophciexamination_element_set.last_modified_date,
    			ophciexamination_element_set.created_user_id,
    			ophciexamination_element_set.created_date,
    			ophciexamination_element_set.position,
    			ophciexamination_element_set.is_active,
    			ophciexamination_element_set.display_order_edited
			FROM ophciexamination_element_set
			JOIN temp_workflow_migration
			  ON ophciexamination_element_set.workflow_id = temp_workflow_migration.old_id"
        )->execute();

        // Find all element set migrations
        $connection->createCommand(
            "CREATE TEMPORARY TABLE temp_element_set_migration
			SELECT DISTINCT
				old_element_set.id AS old_id,
				new_element_set.id AS new_id,
				temp_workflow_migration.institution_id AS institution_id
			FROM ophciexamination_element_set AS old_element_set
			JOIN temp_workflow_migration
			  ON old_element_set.workflow_id = temp_workflow_migration.old_id
			JOIN ophciexamination_element_set AS new_element_set
			  ON new_element_set.workflow_id = temp_workflow_migration.new_id
			WHERE old_element_set.name = new_element_set.name
			  AND old_element_set.last_modified_user_id = new_element_set.last_modified_user_id
			  AND old_element_set.last_modified_date = new_element_set.last_modified_date
			  AND old_element_set.created_user_id = new_element_set.created_user_id
			  AND old_element_set.created_date = new_element_set.created_date
			  AND old_element_set.position = new_element_set.position
			  AND old_element_set.is_active = new_element_set.is_active
			  AND old_element_set.display_order_edited = new_element_set.display_order_edited"
        )->execute();

        // Duplicate element set items to new element sets
        $connection->createCommand(
            "INSERT INTO ophciexamination_element_set_item
			(
    			set_id,
    			element_type_id,
    			is_hidden,
    			is_mandatory,
    			display_order,
    			last_modified_user_id,
    			last_modified_date,
    			created_user_id,
    			created_date
			)
			SELECT
				temp_element_set_migration.new_id,
				ophciexamination_element_set_item.element_type_id,
    			ophciexamination_element_set_item.is_hidden,
    			ophciexamination_element_set_item.is_mandatory,
    			ophciexamination_element_set_item.display_order,
    			ophciexamination_element_set_item.last_modified_user_id,
    			ophciexamination_element_set_item.last_modified_date,
    			ophciexamination_element_set_item.created_user_id,
    			ophciexamination_element_set_item.created_date
			FROM ophciexamination_element_set_item
			JOIN temp_element_set_migration
			  ON ophciexamination_element_set_item.set_id = temp_element_set_migration.old_id"
        )->execute();

        // Migrate all event element set assignments
        $connection->createCommand(
            "UPDATE ophciexamination_event_elementset_assignment AS assignment
			SET step_id = (
				-- Find the migrated step with the same institution as this assignment's event
				SELECT temp_element_set_migration.new_id
				FROM temp_element_set_migration
				JOIN `event`
				  ON `event`.institution_id = temp_element_set_migration.institution_id
				WHERE assignment.step_id = temp_element_set_migration.old_id
				  AND assignment.event_id = `event`.id
			)
			-- Only update assignments whose steps have been migrated
			WHERE EXISTS (
				SELECT *
				FROM temp_element_set_migration
				WHERE assignment.step_id = temp_element_set_migration.old_id
			)"
        )->execute();

        // Clear migrated element set items
        $connection->createCommand(
            "DELETE FROM ophciexamination_element_set_item
			WHERE EXISTS (
				SELECT *
				FROM temp_element_set_migration
				WHERE ophciexamination_element_set_item.set_id = temp_element_set_migration.old_id
			)"
        )->execute();

        // Clear migrated element sets
        $connection->createCommand(
            "DELETE FROM ophciexamination_element_set
			WHERE EXISTS (
				SELECT *
				FROM temp_element_set_migration
				WHERE ophciexamination_element_set.id = temp_element_set_migration.old_id
			)"
        )->execute();

        // Clear migrated rules
        $connection->createCommand(
            "DELETE FROM ophciexamination_workflow_rule
			WHERE EXISTS (
				SELECT *
				FROM temp_workflow_migration
				WHERE ophciexamination_workflow_rule.workflow_id = temp_workflow_migration.old_id
			)"
        )->execute();

        // Clear migrated workflows
        $connection->createCommand(
            "DELETE FROM ophciexamination_workflow
			WHERE EXISTS (
				SELECT *
				FROM temp_workflow_migration
				WHERE ophciexamination_workflow.id = temp_workflow_migration.old_id
			)"
        )->execute();

        // Drop temporary tables
        $connection->createCommand("DROP TABLE temp_workflow_migration")->execute();
        $connection->createCommand("DROP TABLE temp_element_set_migration")->execute();

        // Add non-null constraint to workflow.institution_id
        $this->alterColumn('ophciexamination_workflow', 'institution_id', 'int(10) unsigned NOT NULL');
    }

    public function safeDown()
    {
        echo "m210729_052338_rework_workflow_tables_for_mt does not support migration down.\n";
        return false;
    }
}
