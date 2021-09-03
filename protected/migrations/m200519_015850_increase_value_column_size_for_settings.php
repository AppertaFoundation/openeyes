<?php

class m200519_015850_increase_value_column_size_for_settings extends OEMigration
{
    public function up()
    {
        $this->alterOEColumn('setting_firm', 'value', 'text', true);
        $this->alterOEColumn('setting_installation', 'value', 'text', true);
        $this->alterOEColumn('setting_institution', 'value', 'text', true);
        $this->alterOEColumn('setting_internal_referral', 'value', 'text', true);
        $this->alterOEColumn('setting_site', 'value', 'text', true);
        $this->alterOEColumn('setting_specialty', 'value', 'text', true);
        $this->alterOEColumn('setting_subspecialty', 'value', 'text', true);
        $this->alterOEColumn('setting_user', 'value', 'text', true);
    }

    public function down()
    {
        $this->alterOEColumn('setting_user', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_subspecialty', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_specialty', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_site', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_institution', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_installation', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_internal_referral', 'value', 'varchar(255)', true);
        $this->alterOEColumn('setting_firm', 'value', 'varchar(255)', true);
    }
}
