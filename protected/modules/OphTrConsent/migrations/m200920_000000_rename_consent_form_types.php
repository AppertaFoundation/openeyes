<?php

class m200920_000000_rename_consent_form_types extends OEMigration
{
    public function up()
    {
        $this->execute("UPDATE ophtrconsent_type_type SET name = '1. Patient agreement to investigation or treatment for adults with mental capacity to give valid consent' WHERE id = 1;");
        $this->execute("UPDATE ophtrconsent_type_type SET name = '2. Parental agreement to investigation or treatment for a child or young person' WHERE id = 2;");
        $this->execute("UPDATE ophtrconsent_type_type SET name = '3. Patient/parental agreement to investigation or treatment (procedures where consciousness not impaired)' WHERE id = 3;");
        $this->execute("UPDATE ophtrconsent_type_type SET name = '4. Form for adults who are unable to consent to investigation or treatment' WHERE id = 4;");
    }

    public function down()
    {
        $this->execute("UPDATE ophtrconsent_type_type SET name = 'Patient agreement to investigation or treatment' WHERE id = 1;");
        $this->execute("UPDATE ophtrconsent_type_type SET name = 'Parental agreement to investigation or treatment for a child or young person' WHERE id = 2;");
        $this->execute("UPDATE ophtrconsent_type_type SET name = 'Patient' WHERE id = 3;");
        $this->execute("UPDATE ophtrconsent_type_type SET name = 'Form for adults who are unable to consent to investigation or treatment' WHERE id = 4;");
    }
}
