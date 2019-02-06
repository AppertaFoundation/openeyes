INSERT INTO medication_set_item (medication_id, medication_set_id, default_form_id, default_route_id)

SELECT t2.id,
      rms.medication_id,
      rms.default_form_id,
      rms.default_route_id

FROM medication t1
LEFT JOIN medication_drug AS md ON md.id = t1.source_old_id
LEFT JOIN medication_set_item rms ON t1.id = rms.medication_id
LEFT JOIN medication t2 ON t2.vmp_code COLLATE utf8_unicode_ci = md.external_code COLLATE utf8_unicode_ci
WHERE
  t2.source_type = 'DM+D' AND
    t2.source_subtype = 'VMP'

UNION

SELECT t2.id,
  rms.medication_set_id,
  rms.default_form_id,
  rms.default_route_id

FROM medication t1
  LEFT JOIN medication_drug AS md ON md.id = t1.source_old_id
  LEFT JOIN medication_set_item rms ON t1.id = rms.medication_id
  LEFT JOIN medication t2 ON t2.vtm_code COLLATE utf8_unicode_ci = md.external_code COLLATE utf8_unicode_ci
WHERE
  t2.source_type = 'DM+D' AND
  t2.source_subtype = 'VTM';