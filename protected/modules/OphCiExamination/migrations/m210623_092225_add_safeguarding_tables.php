<?php

class m210623_092225_add_safeguarding_tables extends OEMigration
{
    public function up()
    {
        $concerns = array(
            'Adult safeguarding concern',
            'Alleged victim of sexual assault',
            'At risk for deliberate self harm',
            'At risk for other-directed violence',
            'At risk of discriminatory abuse',
            'At risk of domestic violence',
            'At risk of emotional abuse',
            'At risk of female genital mutilation',
            'At risk of financial abuse',
            'At risk of forced marriage',
            'At risk of honour based violence',
            'At risk of human trafficking',
            'At risk of institutional abuse',
            'At risk of physical abuse',
            'At risk of psychological abuse',
            'At risk of radicalisation',
            'At risk of sexual abuse',
            'At risk of sexual exploitation',
            'Carer behaviour is cause for safeguarding concern',
            'Child at risk',
            'Child is cause for safeguarding concern',
            'Delay in seeking medical advice',
            'Disclosure of being subjected to abuse',
            'Domestic abuse victim in household',
            'Family history of alcohol misuse',
            'Family history of substance misuse',
            'Family is cause for concern',
            'Family member subject of child protection plan',
            'Frequent attender of emergency department',
            'Has child subject of child protection plan',
            'No safeguarding issues identified',
            'Self-neglect',
            'Suspected alcohol abuse',
            'Suspected domestic abuse',
            'Suspected drug abuse',
            'Suspected non-accidental injury to child',
            'Suspected victim of bullying',
            'Suspected victim of child abuse',
            'Suspected victim of child neglect',
            'Suspected victim of child sexual abuse',
            'Suspected victim of emotional abuse',
            'Suspected victim of physical abuse',
            'Suspected victim of sexual abuse',
            'Suspected victim of sexual grooming',
            'Unborn child is cause for safeguarding concern',
            'Vulnerable adult',
        );

        $this->createOETable('ophciexamination_safeguarding_outcome', array(
            'id' => 'pk',
            'term' => 'tinytext',
        ));

        $this->createOETable('et_ophciexamination_safeguarding', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned',
            'no_concerns' => 'bool',
            'outcome_id' => 'int(11)',
            'outcome_comments' => 'text',
            'display_order' => 'int(10)',
        ), true);
        $this->addForeignKey(
            'et_ophciexamination_safeguarding_ev_fk',
            'et_ophciexamination_safeguarding',
            'event_id',
            'event',
            'id'
        );
        $this->addForeignKey(
            'et_ophciexamination_safeguarding_ou_fk',
            'et_ophciexamination_safeguarding',
            'outcome_id',
            'ophciexamination_safeguarding_outcome',
            'id'
        );

        $this->createOETable('ophciexamination_safeguarding_concern', array(
            'id' => 'pk',
            'term' => 'tinytext',
        ), true);

        $this->createOETable('ophciexamination_safeguarding_entry', array(
            'id' => 'pk',
            'element_id' => 'int(11)',
            'concern_id' => 'int(11)',
            'comment' => 'tinytext',
        ), true);
        $this->addForeignKey(
            'ophciexamination_safeguarding_entry_el_fk',
            'ophciexamination_safeguarding_entry',
            'element_id',
            'et_ophciexamination_safeguarding',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_safeguarding_entry_co_fk',
            'ophciexamination_safeguarding_entry',
            'concern_id',
            'ophciexamination_safeguarding_concern',
            'id'
        );

        $connection = $this->getDbConnection();
        $examination_event_type_id = $connection->createCommand("SELECT id FROM event_type WHERE name = 'Examination'")->queryScalar();
        $element_group_id = $connection->createCommand("SELECT id FROM element_group WHERE name = 'History'")->queryScalar();

        $this->insertOEElementType(
            array(
                'OEModule\\OphCiExamination\\models\\Element_OphCiExamination_Safeguarding' => array(
                    'name' => 'Safeguarding',
                    'required' => 0,
                    'default' => 1,
                    'element_group_id' => $element_group_id,
                ),
            ),
            $examination_event_type_id
        );

        $this->insertMultiple(
            'ophciexamination_safeguarding_concern',
            array_map(
                function ($item) {
                    return array('term' => $item);
                },
                $concerns
            )
        );

        $this->insertMultiple(
            'ophciexamination_safeguarding_outcome',
            array(
                array('id' => 1, 'term' => 'No safeguarding concern'),
                array('id' => 2, 'term' => 'Confirm safeguarding concerns'),
                array('id' => 3, 'term' => 'Follow up required'),
            ),
        );

        $this->insert(
            'authitem',
            array(
                'name' => 'Safeguarding',
                'type' => 2,
                'description' => 'Role for safeguarding team'
            )
        );

        $this->insert(
            'ophciexamination_risk',
            array(
                'name' => 'Safeguarding',
                'active' => 1,
            )
        );
    }

    public function down()
    {
        echo "m210623_092225_add_safeguarding_tables does not support migration down.\n";
        return false;
    }
}
