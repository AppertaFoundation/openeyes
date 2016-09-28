<?php

class m131121_114658_booking_authitems extends CDbMigration
{
    private $authitems = array(
        array('name' => 'OprnEditTheatreSession', 'type' => 0),
        array('name' => 'OprnConfirmBookingLetterPrinted', 'type' => 0),
        array('name' => 'OprnConfirmTransport', 'type' => 0),
        array('name' => 'OprnEditTheatreSessionDetails', 'type' => 0),

        array('name' => 'TaskManageOperationBookings', 'type' => 1),
        array('name' => 'TaskManageTransport', 'type' => 1),
        array('name' => 'TaskEditTheatreSessionDetails', 'type' => 1),

        array('name' => 'Edit theatre session', 'type' => 2),
    );

    private $parents = array(
        'OprnEditTheatreSession' => 'TaskManageOperationBookings',
        'OprnConfirmBookingLetterPrinted' => 'TaskManageOperationBookings',
        'OprnConfirmTransport' => 'TaskManageTransport',
        'OprnEditTheatreSessionDetails' => 'TaskEditTheatreSessionDetails',

        'TaskManageOperationBookings' => 'Edit',
        'TaskManageTransport' => 'Edit',
        'TaskEditTheatreSessionDetails' => 'Edit theatre session',
    );

    public function up()
    {
        foreach ($this->authitems as $authitem) {
            $this->insert('authitem', $authitem);
        }

        foreach ($this->parents as $child => $parent) {
            if ($this->dbConnection->createCommand()->select()->from('authitem')->where('name = ?')->queryScalar(array($parent))) {
                $this->insert('authitemchild', array('parent' => $parent, 'child' => $child));
            }
        }

        $this->update('authassignment', array('itemname' => 'Edit theatre session'), 'itemname = :oldname', array('oldname' => 'purplerinse'));
    }

    public function down()
    {
        $this->update('authassignment', array('itemname' => 'purplerinse'), 'itemname = :oldname', array('oldname' => 'Edit theatre session'));

        foreach ($this->parents as $child => $parent) {
            $this->delete('authitemchild', 'parent = ? and child = ?', array($parent, $child));
        }

        foreach ($this->authitems as $authitem) {
            $this->delete('authitem', 'name = ?', array($authitem['name']));
        }
    }
}
