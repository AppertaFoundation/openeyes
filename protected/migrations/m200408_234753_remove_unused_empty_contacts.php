<?php

class m200408_234753_remove_unused_empty_contacts extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("
            update contact cont
            left join address a on cont.id = a.contact_id
            left join commissioning_body b on cont.id = b.contact_id
            left join commissioning_body_service c on cont.id = c.contact_id
            left join contact_location d on cont.id = d.contact_id
            left join gp e on cont.id = e.contact_id
            left join institution f on cont.id = f.contact_id
            left join patient g on cont.id = g.contact_id
            left join patient_contact_assignment h on cont.id = h.contact_id
            left join person i on cont.id = i.contact_id
            left join practice j on cont.id = j.contact_id
            left join site k on cont.id = k.contact_id
            left join site l on cont.id = l.replyto_contact_id
            left join user m on cont.id = m.contact_id

            left join address_version aa on cont.id = aa.contact_id
            left join commissioning_body_version bb on cont.id = bb.contact_id
            left join commissioning_body_service_version cc on cont.id = cc.contact_id
            left join contact_location_version dd on cont.id = dd.contact_id
            left join gp_version ee on cont.id = ee.contact_id
            left join institution_version ff on cont.id = ff.contact_id
            left join patient_version gg on cont.id = gg.contact_id
            left join patient_contact_assignment_version hh on cont.id = hh.contact_id
            left join person_version ii on cont.id = ii.contact_id
            left join practice_version jj on cont.id = jj.contact_id
            left join site_version kk on cont.id = kk.contact_id
            left join site_version ll on cont.id = ll.replyto_contact_id
            left join user_version mm on cont.id = mm.contact_id

            set cont.active = 99
            where a.id is null
            and b.id is null
            and c.id is null
            and d.id is null
            and e.id is null
            and f.id is null
            and g.id is null
            and h.id is null
            and i.id is null
            and j.id is null
            and k.id is null
            and l.id is null
            and m.id is null

            and aa.id is null
            and bb.id is null
            and cc.id is null
            and dd.id is null
            and ee.id is null
            and ff.id is null
            and gg.id is null
            and hh.id is null
            and ii.id is null
            and jj.id is null
            and kk.id is null
            and ll.id is null
            and mm.id is null

            and (cont.nick_name is null or cont.nick_name = 'NULL' or cont.nick_name = '' )
            and (cont.primary_phone is null or cont.primary_phone = '' )
            and (cont.title is null or cont.title = '' )
            and (cont.first_name is null or cont.first_name = '' )
            and (cont.last_name is null or cont.last_name = '' )
            and (cont.maiden_name is null or cont.maiden_name = '' )
            and (cont.qualifications is null or cont.qualifications = '' )
            and (cont.contact_label_id is null or cont.contact_label_id = '' )
            and (cont.national_code is null or cont.national_code = '' )
            and (cont.fax is null or cont.fax = '' )
            ;
        ");

        $this->delete('contact', 'active = 99');
    }

    public function down()
    {
        echo "m200408_234753_remove_unused_empty_contacts does not support migration down.\n";
        return false;
    }
}
