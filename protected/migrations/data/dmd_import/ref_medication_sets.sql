SELECT id INTO @amp_id FROM medication_set WHERE `name` = 'DM+D AMP';
SELECT id INTO @vmp_id FROM medication_set WHERE `name` = 'DM+D VMP';
SELECT id INTO @vtm_id FROM medication_set WHERE `name` = 'DM+D VTM';

SELECT
  rm.id,
  CASE rm.source_subtype
    WHEN 'AMP' THEN @amp_id
    WHEN 'VMP' THEN @vmp_id
    WHEN 'VTM' THEN @vtm_id
  END AS setId,
  NULL AS formId,
  mr.id AS RouteId
INTO OUTFILE '/tmp/medication_set.csv'
FROM medication rm

  LEFT JOIN {prefix}amp_amps amp2 ON amp2.apid = rm.amp_code
  LEFT JOIN {prefix}vmp_drug_form dft ON dft.vpid = amp2.vpid
  LEFT JOIN {prefix}lookup_form fhit ON fhit.cd = dft.formcd
  LEFT JOIN medication_form mf ON mf.term = fhit.desc AND mf.source_type = 'DM+D'

  LEFT JOIN {prefix}vmp_drug_route drt ON drt.vpid = amp2.vpid
  LEFT JOIN {prefix}lookup_route lr ON lr.cd = drt.routecd
  LEFT JOIN medication_route mr ON mr.term COLLATE utf8_general_ci = lr.desc AND mr.source_type = 'DM+D'

WHERE
  rm.source_type='DM+D';
