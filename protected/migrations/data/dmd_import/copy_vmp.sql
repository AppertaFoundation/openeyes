ALTER TABLE {prefix}lookup_unit_o{prefix}measure ADD INDEX lookup_unit_o_idx (`cd`);

ALTER TABLE {prefix}vmp_vmps ADD INDEX cmp_vmps_idx (`vtmid`);
ALTER TABLE {prefix}vmp_vmps ADD INDEX vpid_idx (`vpid`);
ALTER TABLE {prefix}vmp_vmps ADD INDEX udfs_uomcd_idx (`udfs_uomcd`);

ALTER TABLE {prefix}lookup_route ADD INDEX lookup_route_idx (`cd`);
ALTER TABLE {prefix}lookup_route ADD INDEX udfs_uomcd_idx (`desc`);

ALTER TABLE {prefix}vmp_drug_route ADD INDEX vpid_idx (`vpid`);
ALTER TABLE {prefix}vmp_drug_route ADD INDEX vpid_idx (`routecd`);

ALTER TABLE {prefix}vmp_drug_form ADD INDEX vpid_idx (`formcd`);
ALTER TABLE {prefix}vmp_drug_form ADD INDEX vpid_id (`vpid`);

ALTER TABLE {prefix}vtm_vtm ADD INDEX vpid_id (`vtmid`);

ALTER TABLE {prefix}lookup_form ADD INDEX vpid_id (`cd`);
ALTER TABLE {prefix}lookup_form ADD INDEX vpid_id (`desc`);

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
    LEFT JOIN (SELECT id, term FROM medication_form WHERE medication_form.source_type = 'DM+D') mf ON mf.term  COLLATE utf8_general_ci = fhit.desc

    LEFT JOIN {prefix}vmp_drug_route drt ON drt.vpid = vmp.vpid
    LEFT JOIN {prefix}lookup_route lr ON lr.cd = drt.routecd
    LEFT JOIN (SELECT id, term FROM medication_route WHERE medication_route.source_type = 'DM+D') mr ON mr.term COLLATE utf8_general_ci = lr.desc

    LEFT JOIN {prefix}lookup_unit_o{prefix}measure uom ON uom.cd = vmp.udfs_uomcd
