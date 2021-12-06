<?php

class m200130_044814_add_logos_to_site extends OEMigration
{
    public function safeUp()
    {
        // Creating Table
        $this->createOETable('site_logo', array(
            'id' => 'pk',
            'primary_logo' => 'mediumblob',
            'secondary_logo' => 'mediumblob',
            'parent_logo' => 'integer'
        ));

        // Adding column to sites to save foreign key relationship
        $this->addOEColumn('site', 'logo_id', 'integer', true);

        // Adding foreign key to sites
        $this->addForeignKey('site_logo_id_fk', 'site', 'logo_id', 'site_logo', 'id');

        // Adding column to institution to save foreign key relationship
        $this->addOEColumn('institution', 'logo_id', 'integer', true);

        // Adding foreign key to institution
        $this->addForeignKey('institution_logo_id_fk', 'site', 'logo_id', 'site_logo', 'id');

        // finding current Default logo -- currently null but this could pull the current logo
        $logos = array();

        $directory = \Yii::getPathOfAlias('application.runtime');
        $images = glob("$directory/*.{jpg,png,gif}", GLOB_BRACE);

        foreach ($images as $image_path) {
            if (strpos($image_path, 'header') !== false) {
                $logos['primaryLogo'] = $image_path;
            }
            if (strpos($image_path, 'secondary') !== false) {
                $logos['secondaryLogo'] = $image_path;
            }
        }

        $primaryLogo = null;
        $secondaryLogo =null;
        if (array_key_exists('primaryLogo', $logos)) {
            $primaryLogo = file_get_contents($logos['primaryLogo']);
        }
        if (array_key_exists('secondaryLogo', $logos)) {
            $secondaryLogo = file_get_contents($logos['secondaryLogo']);
        }

        // Adding Default logo to db
        $this->insert('site_logo', array(
            'primary_logo' => $primaryLogo,
            'secondary_logo' => $secondaryLogo));
    }

    public function safeDown()
    {
        // Dropping foreign key from sites
        $this->dropForeignKey('site_logo_id_fk', 'site');

        // Dropping column from sites that save foreign key relationship
        $this->dropOEColumn('site', 'logo_id', true);

        // Dropping foreign key from institutions
        $this->dropForeignKey('institution_logo_id_fk', 'site');

        // Dropping column from institutions that save foreign key relationship
        $this->dropOEColumn('institution', 'logo_id', true);

        // Dropping Table
        $this->dropOETable('site_logo');
    }
}
