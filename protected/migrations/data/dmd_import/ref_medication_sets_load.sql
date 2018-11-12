LOAD DATA INFILE '/tmp/ref_medication_set.csv' INTO TABLE openeyes.ref_medication_set
(ref_medication_id,ref_set_id,default_form,default_route)
SET default_dose = NULL, default_frequency = NULL, default_dose_unit_term = NULL, deleted_date = NULL;