UPDATE medication_route SET has_laterality = 0;
UPDATE medication_route SET has_laterlatiy = 1 WHERE `term` IN (
  'Eye',
  'Intravitreal',
  'Ocular muscle',
  'Sub-Conj',
  'Auricular',
  'Intrabursal',
  'Intraocular',
  'Intrapleural',
  'Ocular',
  'Subconjunctival',
  'Intravitreal',
  'Intracameral',
  'Subretinal'
);