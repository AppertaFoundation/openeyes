<?php

class m230315_131105_update_cancel_patient_bookings_procedure_with_multi_tenancy extends OEMigration
{
    public function safeUp()
    {
        $storedProcedure = <<<EOL
		CREATE OR REPLACE PROCEDURE cancel_patient_bookings(IN patientToCancel INT)
		  BEGIN
			DECLARE cancel_status, rescheduling_status, rescheduled_status, cancel_reason, booking_type, stored_event_id, episode_id, booking_id, operation_id, episode_status_id, done, rows_affected, admin_user INT DEFAULT 0;
			DECLARE cancel_comment VARCHAR(255);
			DECLARE cur1 CURSOR FOR SELECT
									  event.id                                   AS stored_event_id,
									  episode.id                                 AS episode_id,
									  ophtroperationbooking_operation_booking.id AS booking_id,
									  et_ophtroperationbooking_operation.id      AS operation_id
									FROM episode
									  JOIN event ON episode.id = event.episode_id
									  JOIN et_ophtroperationbooking_operation
										ON event.id = et_ophtroperationbooking_operation.event_id
									  LEFT JOIN ophtroperationbooking_operation_booking ON et_ophtroperationbooking_operation.id =
																					  ophtroperationbooking_operation_booking.element_id
									  LEFT JOIN ophtroperationbooking_operation_session
										ON ophtroperationbooking_operation_session.id =
										   ophtroperationbooking_operation_booking.session_id
									WHERE episode.patient_id = patientToCancel
										  AND event.event_type_id = (SELECT id FROM event_type WHERE name = 'Operation booking')
										  AND (concat_ws(' ', ophtroperationbooking_operation_session.date,
														ophtroperationbooking_operation_session.start_time) > NOW() ||
														ophtroperationbooking_operation_session.date IS NULL ||
														et_ophtroperationbooking_operation.status_id in (SELECT id FROM ophtroperationbooking_operation_status WHERE name = 'Requires rescheduling' || name='Requires scheduling'));
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
			SET @cancel_comment = 'Automatically cancelled by system';
		
			SELECT id
			INTO @cancel_status
			FROM ophtroperationbooking_operation_status
			WHERE `name` = 'Cancelled' ORDER BY id DESC LIMIT 1;
			
			SELECT id
			INTO @rescheduling_status
			FROM ophtroperationbooking_operation_status
			WHERE `name` = 'Requires rescheduling' ORDER BY id DESC LIMIT 1;
			
			SELECT id
			INTO @rescheduled_status
			FROM ophtroperationbooking_operation_status
			WHERE `name` = 'Rescheduled' ORDER BY id DESC LIMIT 1;
		
			SELECT id
			INTO @cancel_reason
			FROM ophtroperationbooking_operation_cancellation_reason
			WHERE `text` = 'Patient has died' AND active ORDER BY id DESC LIMIT 1;
		
			SELECT id
			INTO @booking_type
			FROM event_type
			WHERE `name` = 'Operation booking' ORDER BY id DESC LIMIT 1;
		
			SELECT id
			INTO @episode_status_id
			FROM episode_status
			WHERE `name` = 'Discharged' ORDER BY id DESC LIMIT 1;
		
			SELECT DISTINCT user.id
			INTO @admin_user
			FROM user
			JOIN user_authentication ua on ua.user_id = user.id
			WHERE ua.`username` = 'admin';
		
			SELECT id
			INTO @audit_action_id
			FROM audit_action
			WHERE `name` = 'cancel';
		
			SELECT id
			INTO @audit_type_id
			FROM audit_type
			WHERE `name` = 'booking';
		
			OPEN cur1;
		
			read_loop: LOOP
			  FETCH cur1
			  INTO stored_event_id, episode_id, booking_id, operation_id;
			  IF done
			  THEN
				LEAVE read_loop;
			  END IF;
			  UPDATE et_ophtroperationbooking_operation
			  SET operation_cancellation_date = NOW(),
				cancellation_reason_id        = @cancel_reason,
				cancellation_comment          = @cancel_comment,
				cancellation_user_id          = @admin_user,
				status_id                     = @cancel_status
			  WHERE id = operation_id
					AND (cancellation_reason_id IS NULL OR status_id = @rescheduling_status OR status_id = @rescheduled_status);
		
			  UPDATE episode
			  SET episode_status_id = @episode_status_id
			  WHERE id = episode_id;
		
			  DELETE FROM event_issue WHERE event_id = stored_event_id;
		
			  UPDATE ophtroperationbooking_operation_booking
			  SET booking_cancellation_date = NOW(),
				cancellation_reason_id      = @cancel_reason,
				cancellation_comment        = @cancel_comment,
				cancellation_user_id        = @admin_user
			  WHERE id = booking_id
					AND cancellation_reason_id IS NULL;
		
			  INSERT INTO audit (action_id, type_id, patient_id, episode_id, event_id) VALUES (@audit_action_id, @audit_type_id, patientToCancel, episode_id, stored_event_id);
		
			END LOOP;
		
			CLOSE cur1;
		  END;
		
		EOL;

        $this->execute($storedProcedure);
    }

    public function safeDown()
    {
        $storedProcedure = <<<EOL
		CREATE OR REPLACE PROCEDURE cancel_patient_bookings(IN patientToCancel INT)
		  BEGIN
			DECLARE cancel_status, rescheduling_status, rescheduled_status, cancel_reason, booking_type, stored_event_id, episode_id, booking_id, operation_id, episode_status_id, done, rows_affected, admin_user INT DEFAULT 0;
			DECLARE cancel_comment VARCHAR(255);
			DECLARE cur1 CURSOR FOR SELECT
									  event.id                                   AS stored_event_id,
									  episode.id                                 AS episode_id,
									  ophtroperationbooking_operation_booking.id AS booking_id,
									  et_ophtroperationbooking_operation.id      AS operation_id
									FROM episode
									  JOIN event ON episode.id = event.episode_id
									  JOIN et_ophtroperationbooking_operation
										ON event.id = et_ophtroperationbooking_operation.event_id
									  LEFT JOIN ophtroperationbooking_operation_booking ON et_ophtroperationbooking_operation.id =
																					  ophtroperationbooking_operation_booking.element_id
									  LEFT JOIN ophtroperationbooking_operation_session
										ON ophtroperationbooking_operation_session.id =
										   ophtroperationbooking_operation_booking.session_id
									WHERE episode.patient_id = patientToCancel
										  AND event.event_type_id = (SELECT id FROM event_type WHERE name = 'Operation booking')
										  AND (concat_ws(' ', ophtroperationbooking_operation_session.date,
														ophtroperationbooking_operation_session.start_time) > NOW() ||
														ophtroperationbooking_operation_session.date IS NULL ||
														et_ophtroperationbooking_operation.status_id in (SELECT id FROM ophtroperationbooking_operation_status WHERE name = 'Requires rescheduling' || name='Requires scheduling'));
			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
			SET @cancel_comment = 'Automatically cancelled by system';
		
			SELECT id
			INTO @cancel_status
			FROM ophtroperationbooking_operation_status
			WHERE `name` = 'Cancelled' ORDER BY id DESC LIMIT 1;
			
			SELECT id
			INTO @rescheduling_status
			FROM ophtroperationbooking_operation_status
			WHERE `name` = 'Requires rescheduling' ORDER BY id DESC LIMIT 1;
			
			SELECT id
			INTO @rescheduled_status
			FROM ophtroperationbooking_operation_status
			WHERE `name` = 'Rescheduled' ORDER BY id DESC LIMIT 1;
		
			SELECT id
			INTO @cancel_reason
			FROM ophtroperationbooking_operation_cancellation_reason
			WHERE `text` = 'Patient has died' AND active ORDER BY id DESC LIMIT 1;
		
			SELECT id
			INTO @booking_type
			FROM event_type
			WHERE `name` = 'Operation booking' ORDER BY id DESC LIMIT 1;
		
			SELECT id
			INTO @episode_status_id
			FROM episode_status
			WHERE `name` = 'Discharged' ORDER BY id DESC LIMIT 1;
		
			SELECT user.id
			INTO @admin_user
			FROM user
			JOIN user_authentication ua on ua.user_id = user.id
			WHERE ua.`username` = 'admin';
		
			SELECT id
			INTO @audit_action_id
			FROM audit_action
			WHERE `name` = 'cancel';
		
			SELECT id
			INTO @audit_type_id
			FROM audit_type
			WHERE `name` = 'booking';
		
			OPEN cur1;
		
			read_loop: LOOP
			  FETCH cur1
			  INTO stored_event_id, episode_id, booking_id, operation_id;
			  IF done
			  THEN
				LEAVE read_loop;
			  END IF;
			  UPDATE et_ophtroperationbooking_operation
			  SET operation_cancellation_date = NOW(),
				cancellation_reason_id        = @cancel_reason,
				cancellation_comment          = @cancel_comment,
				cancellation_user_id          = @admin_user,
				status_id                     = @cancel_status
			  WHERE id = operation_id
					AND (cancellation_reason_id IS NULL OR status_id = @rescheduling_status OR status_id = @rescheduled_status);
		
			  UPDATE episode
			  SET episode_status_id = @episode_status_id
			  WHERE id = episode_id;
		
			  DELETE FROM event_issue WHERE event_id = stored_event_id;
		
			  UPDATE ophtroperationbooking_operation_booking
			  SET booking_cancellation_date = NOW(),
				cancellation_reason_id      = @cancel_reason,
				cancellation_comment        = @cancel_comment,
				cancellation_user_id        = @admin_user
			  WHERE id = booking_id
					AND cancellation_reason_id IS NULL;
		
			  INSERT INTO audit (action_id, type_id, patient_id, episode_id, event_id) VALUES (@audit_action_id, @audit_type_id, patientToCancel, episode_id, stored_event_id);
		
			END LOOP;
		
			CLOSE cur1;
		  END;
		
		EOL;

        $this->execute($storedProcedure);
    }
}
