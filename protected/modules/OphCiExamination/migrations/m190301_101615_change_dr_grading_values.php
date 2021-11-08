<?php

class m190301_101615_change_dr_grading_values extends CDbMigration
{
    public $maculopathy_table = 'ophciexamination_drgrading_nscmaculopathy';
    public $drgrading_table = 'et_ophciexamination_drgrading';


    function get_M1A($col)
    {
        return $this->dbConnection->createCommand()
            ->select($col)
            ->from($this->maculopathy_table)
            ->where('name = :name', [':name' => 'M1A'])
            ->queryScalar();
    }

    function get_M1S($col)
    {
        return $this->dbConnection->createCommand()
            ->select($col)
            ->from($this->maculopathy_table)
            ->where('name = :name', [':name' => 'M1S'])
            ->queryScalar();
    }

    public function safeUp()
    {
        // switch all "M1S" values to "M1A"
        $this->update($this->drgrading_table, array('left_nscmaculopathy_id' => $this->get_M1A('id')), 'left_nscmaculopathy_id=:id', array(':id'=>$this->get_M1S('id')));
        $this->update($this->drgrading_table, array('right_nscmaculopathy_id' => $this->get_M1A('id')), 'right_nscmaculopathy_id=:id', array(':id'=>$this->get_M1S('id')));

        // Rename "M1A" to "M1"
        $this->update($this->maculopathy_table, array('name' => 'M1'), 'id = :id', array(':id' => $this->get_M1A('id'))); // won't update on :name

        // Delete the "M1S" value
        $this->delete($this->maculopathy_table, 'id=:id', array(':id' => $this->get_M1S('id')));
    }

    public function safeDown()
    {
        $this->insert($this->maculopathy_table, array(
            'id' => 3,
            'name' => 'M1S',
            'description' => 'Stable maculopathy needs no further treatment',
            'display_order' => 2,
            'booking_weeks' => null,
            'class' => 'moderate',
            'code' => 'MO',
            'active' => 1,
        ));

        $this->update($this->maculopathy_table, array('name' => 'M1A'), 'id = :id', array(':id' => 2));

        $this->update($this->drgrading_table, array('left_nscmaculopathy_id' => 3), 'left_nscmaculopathy_id=:id', array(':id'=>2));
        $this->update($this->drgrading_table, array('right_nscmaculopathy_id' => 3), 'right_nscmaculopathy_id=:id', array(':id'=>2));
    }
}
