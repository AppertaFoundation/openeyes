START TRANSACTION;
/*--------------------------------------- Map legacy routes to new -------------------------------------------------*/

DROP TEMPORARY TABLE IF EXISTS temp_route_names;

-- first map legacy names to DM+D names
CREATE TEMPORARY TABLE temp_route_names(
    legacy_name varchar(50),
    dmd_name varchar(50)
);
    
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Eye'          , 'Ocular');                                       
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('IM'           , 'Intramuscular'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('IV'           , 'Intravenous'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Nose'         , 'Nasal'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Ocular muscle', 'Intramuscular'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('PO'           , 'Oral'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('PR'           , 'Rectal'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('PV'           , 'Vaginal'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Sub-Conj'     , 'Subconjunctival'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Sub-lingual'  , 'Sublingual'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Subcutaneous' , 'Subcutaneous'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('To Nose'      , 'Nasal'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('To skin'      , 'Transdermal'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Topical'      , 'Route of administration not applicable'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('n/a'          , 'Route of administration not applicable'); 
INSERT INTO temp_route_names (legacy_name, dmd_name) VALUES ('Other'        , 'Route of administration not applicable');

UPDATE medication_route r JOIN temp_route_names t
SET r.term = t.dmd_name
WHERE r.term = t.legacy_name;


DROP TEMPORARY TABLE IF EXISTS temp_route_names;

DROP TEMPORARY TABLE IF EXISTS temp_route_map;

-- Then map any items where a legacy route is used that matches the name of a dm+d route
CREATE TEMPORARY TABLE temp_route_map AS
SELECT mr.id AS legacy_id, mr.term AS legacy_term, md.id AS new_id, md.term AS new_term
FROM medication_route mr INNER JOIN medication_route md ON mr.term = md.term
WHERE mr.source_type = 'LEGACY'
AND md.source_type = 'DM+D';

UPDATE medication m INNER JOIN temp_route_map r ON r.legacy_id = m.default_route_id
SET m.default_route_id = r.new_id;

UPDATE event_medication_use m INNER JOIN temp_route_map r ON r.legacy_id = m.route_id 
SET m.route_id = r.new_id;

UPDATE medication_set_auto_rule_medication m INNER JOIN temp_route_map r ON r.legacy_id = m.default_route_id
SET m.default_route_id = r.new_id;

UPDATE medication_set_item m INNER JOIN temp_route_map r ON r.legacy_id = m.default_route_id
SET m.default_route_id = r.new_id;

-- Finally, delete the legacy routes
DELETE FROM medication_route WHERE source_type = 'LEGACY';

DROP TEMPORARY TABLE IF EXISTS temp_route_map;

COMMIT;