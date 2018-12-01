LOAD DATA INFILE '/tmp/medication_set.csv' INTO TABLE openeyes.medication_medication_set
(medication_id,medication_set_id,default_form,default_route)
SET default_dose = NULL, default_frequency = NULL, default_dose_unit_term = NULL, deleted_date = NULL;