<?php

class m140210_132147_address_foreign_key extends CDbMigration
{
    public function up()
    {
        $orphans = $this->dbConnection->createCommand()->select('count(*)')
            ->from('address a')->leftJoin('contact c', "a.parent_class = 'Contact' and a.parent_id = c.id")
            ->where('c.id is null')->queryScalar();
        if ($orphans) {
            echo "{$orphans} orphaned address entries were found.  Please remove them using CleanupAddressesCommand before running this migration.\n";

            return false;
        }

        $sql = 'alter table address '.
            'drop parent_class, '.
            'change parent_id contact_id int unsigned not null, '.
            'add constraint address_contact_id_fk foreign key (contact_id) references contact (id)';
        $this->execute($sql);
    }

    public function down()
    {
        $sql = 'alter table address '.
            'add parent_class varchar(40) collate utf8_bin not null after created_date, '.
            'change contact_id parent_id int unsigned not null, '.
            'drop foreign key address_contact_id_fk';
        $this->execute($sql);

        $this->update('address', array('parent_class' => 'Contact'));
    }
}
