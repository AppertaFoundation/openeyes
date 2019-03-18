<?php

return array(

    'user_trial_assignment_1' => array(
        'user_id' => $this->getRecord('user', 'user1')->id,
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'trial_permission_id' => $this->getRecord('trial_permission', 'trial_permission_manage')->id,
    ),

    'user_trial_assignment_2' => array(
        'user_id' => $this->getRecord('user', 'user2')->id,
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'trial_permission_id' => $this->getRecord('trial_permission', 'trial_permission_view')->id,
    ),

    'user_trial_assignment_3' => array(
        'user_id' => $this->getRecord('user', 'user3')->id,
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'trial_permission_id' => $this->getRecord('trial_permission', 'trial_permission_edit')->id,
    ),
);
