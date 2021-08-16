<?php

class m200331_013628_create_date_diff_function extends OEMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function up()
    {
        $this->execute("
        DROP FUNCTION IF EXISTS date_diff;");

        $this->execute("
		CREATE FUNCTION date_diff(unit VARCHAR(10) , start_date DATE, end_date DATE)
			RETURNS DOUBLE
			DETERMINISTIC
			BEGIN
                DECLARE ret DOUBLE;
                DECLARE temp DATE;
                IF start_date > end_date THEN
                  SET temp = start_date;
                  SET start_date = end_date;
                  SET end_date = temp;
                END IF;
				IF LOWER(unit) = 'month' THEN
					SET ret = TIMESTAMPDIFF(MONTH, start_date, end_date)
                        + DATEDIFF(end_date, start_date + INTERVAL TIMESTAMPDIFF(MONTH, start_date, end_date) MONTH)
                        / DATEDIFF(end_date + INTERVAL TIMESTAMPDIFF(MONTH, start_date, end_date) + 1 MONTH, start_date + INTERVAL TIMESTAMPDIFF(MONTH, start_date, end_date) MONTH
					);
				ELSEIF LOWER(unit) = 'week' THEN
					SET ret = TIMESTAMPDIFF(WEEK, start_date, end_date)
                        + DATEDIFF(end_date, start_date + INTERVAL TIMESTAMPDIFF(WEEK, start_date, end_date) WEEK)
                        / DATEDIFF(end_date + INTERVAL TIMESTAMPDIFF(WEEK, start_date, end_date) + 1 WEEK, start_date + INTERVAL TIMESTAMPDIFF(WEEK, start_date, end_date) WEEK
					);
				END IF;
				RETURN ret;
			END;
		");
    }

    public function down()
    {
        $this->execute("DROP FUNCTION date_diff");
    }
}
