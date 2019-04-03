INSERT INTO medication (source_type,source_subtype,preferred_term,preferred_code,vtm_term,vtm_code,vmp_term,vmp_code, default_form_id, default_route_id, default_dose_unit_term)
  SELECT
    'DM+D' AS source_type,
    'VMP' AS source_subtype,

    vmp.nm AS preferred_term,
    vmp.vpid AS preferred_code,

    vtm.nm AS vtm_term,
    vtm.vtmid AS vtm_code,

    vmp.nm AS vmp_term,
    vmp.vpid AS vmp_code,

    mf.id AS formId,
    mr.id AS RouteId,

    uom.desc

  FROM
    {prefix}vmp_vmps vmp
    LEFT JOIN {prefix}vtm_vtm vtm ON vtm.vtmid = vmp.vtmid

    LEFT JOIN {prefix}vmp_drug_form dft ON dft.vpid = vmp.vpid
    LEFT JOIN {prefix}lookup_form fhit ON fhit.cd = dft.formcd
    LEFT JOIN medication_form mf ON mf.term = fhit.desc AND mf.source_type = 'DM+D'

    LEFT JOIN {prefix}vmp_drug_route drt ON drt.vpid = vmp.vpid
    LEFT JOIN {prefix}lookup_route lr ON lr.cd = drt.routecd
    LEFT JOIN medication_route mr ON mr.term COLLATE utf8_general_ci = lr.desc AND mr.source_type = 'DM+D'

    LEFT JOIN {prefix}lookup_unit_o{prefix}measure uom ON uom.cd = vmp.udfs_uomcd
