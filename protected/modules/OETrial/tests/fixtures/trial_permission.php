<?php

return array(
    'trial_permission_view' => array(
        'id' => 1,
        'name' => 'View',
        'code' => 'VIEW',
        'can_view' => 1,
        'can_edit' => 0,
        'can_manage' => 0,
    ),
    'trial_permission_edit' => array(
        'id' => 2,
        'name' => 'Edit',
        'code' => 'EDIT',
        'can_view' => 1,
        'can_edit' => 1,
        'can_manage' => 0,
    ),
    'trial_permission_manage' => array(
        'id' => 3,
        'name' => 'Manage',
        'code' => 'MANAGE',
        'can_view' => 1,
        'can_edit' => 1,
        'can_manage' => 1,
    ),
);
