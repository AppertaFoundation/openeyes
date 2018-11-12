INSERT INTO ref_medication_set (ref_medication_id, ref_set_id, default_form, default_route)


SELECT t2.id,
      rms.ref_set_id,
      rms.default_form,
      rms.default_route

FROM ref_medication t1
LEFT JOIN medication_drug AS md ON md.id = t1.source_old_id
LEFT JOIN ref_medication_set rms ON t1.id = rms.ref_medication_id
LEFT JOIN ref_medication t2 ON t2.vmp_code COLLATE utf8_unicode_ci = md.external_code COLLATE utf8_unicode_ci
WHERE
  t2.source_type = 'DM+D' AND
    t2.source_subtype = 'VMP'

UNION

SELECT t2.id,
  rms.ref_set_id,
  rms.default_form,
  rms.default_route

FROM ref_medication t1
  LEFT JOIN medication_drug AS md ON md.id = t1.source_old_id
  LEFT JOIN ref_medication_set rms ON t1.id = rms.ref_medication_id
  LEFT JOIN ref_medication t2 ON t2.vtm_code COLLATE utf8_unicode_ci = md.external_code COLLATE utf8_unicode_ci
WHERE
  t2.source_type = 'DM+D' AND
  t2.source_subtype = 'VTM';