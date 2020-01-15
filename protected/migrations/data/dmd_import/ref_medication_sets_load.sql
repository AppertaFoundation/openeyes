#hmm... without this the import fails... still don't know why
SELECT medication_set_item.`medication_set_id`, id
FROM medication_set_item
WHERE medication_set_item.`medication_set_id` NOT IN (SELECT id FROM medication_set);

LOAD DATA INFILE '/tmp/medication_set.csv' INTO TABLE medication_set_item
(medication_id,medication_set_id,default_form_id,default_route_id)
SET default_dose = NULL, default_frequency_id = NULL, default_dose_unit_term = NULL, deleted_date = NULL;