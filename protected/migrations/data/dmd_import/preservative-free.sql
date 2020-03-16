-- Adds the preservative free attribute to any drugs named preservative free that do not already explicitly have this attribute
INSERT INTO medication_attribute_assignment (medication_id, medication_attribute_option_id )
SELECT m.id, (SELECT id FROM medication_attribute_option WHERE description = "preservative-free")
FROM medication m 
WHERE m.vmp_term like "%preservative free"
	AND m.id NOT IN (
	SELECT medication_id
	from medication_attribute_assignment maa INNER JOIN medication_attribute_option mao ON mao.id = maa.medication_attribute_option_id 
	WHERE mao.description = "preservative-free"
	)