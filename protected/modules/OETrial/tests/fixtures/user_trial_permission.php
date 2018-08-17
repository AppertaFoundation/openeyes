<?php

return array(

    'user_trial_permission_1' => array(
        'user_id' => $this->getRecord('user', 'user1')->id,
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'permission' => UserTrialPermission::PERMISSION_MANAGE,
    ),

    'user_trial_permission_2' => array(
        'user_id' => $this->getRecord('user', 'user2')->id,
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'permission' => UserTrialPermission::PERMISSION_VIEW,
    ),

    'user_trial_permission_3' => array(
        'user_id' => $this->getRecord('user', 'user3')->id,
        'trial_id' => $this->getRecord('trial', 'trial1')->id,
        'permission' => UserTrialPermission::PERMISSION_EDIT,
    ),
);
