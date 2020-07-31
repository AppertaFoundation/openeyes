START TRANSACTION;

SELECT id INTO @amp_id FROM medication_set WHERE `name` = 'DM+D AMP';
SELECT id INTO @vmp_id FROM medication_set WHERE `name` = 'DM+D VMP';
SELECT id INTO @vtm_id FROM medication_set WHERE `name` = 'DM+D VTM';

INSERT INTO medication_set_item (
	medication_id,
  	medication_set_id,
  	default_form_id,
  	default_dose,
  	default_route_id,
  	default_dispense_location_id,
  	default_dispense_condition_id,
  	default_frequency_id,
  	default_dose_unit_term,
  	deleted_date,
  	default_duration_id,
  	last_modified_date,
  	created_date 
)
SELECT
  rm.id as medication_id,
  CASE rm.source_subtype
    WHEN 'AMP' THEN @amp_id
    WHEN 'VMP' THEN @vmp_id
    WHEN 'VTM' THEN @vtm_id
  END AS medication_set_id,
  NULL AS default_form_id,
  NULL AS default_dose,
  mr.id AS default_route_id,
  NULL AS default_dispense_location_id,
  NULL AS default_dispense_condition_id,
  NULL AS default_frequency_id,
  NULL AS default_dose_unit_term,
  NULL AS deleted_date,
  NULL AS default_duration_id,
  CURRENT_DATE() AS last_modified_date,
  CURRENT_DATE() AS created_date 
FROM medication rm
  LEFT JOIN {prefix}amp_amps amp2 ON amp2.apid = rm.amp_code
  LEFT JOIN {prefix}vmp_drug_form dft ON dft.vpid = amp2.vpid
  LEFT JOIN {prefix}lookup_form fhit ON fhit.cd = dft.formcd
  LEFT JOIN medication_form mf ON mf.term = fhit.desc AND mf.source_type = 'DM+D'
  LEFT JOIN {prefix}vmp_drug_route drt ON drt.vpid = amp2.vpid
  LEFT JOIN {prefix}lookup_route lr ON lr.cd = drt.routecd
  LEFT JOIN medication_route mr ON mr.term = lr.desc AND mr.source_type = 'DM+D'
WHERE
  rm.source_type='DM+D';

COMMIT;