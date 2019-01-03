<?php

return array(
    'trial_permission_view' => array(
        'id' => 1,
        'name' => 'View',
        'code' => 'VIEW',
        'can_view' => true,
        'can_edit' => false,
        'can_manage' => false,
    ),
    'trial_permission_edit' => array(
        'id' => 2,
        'name' => 'Edit',
        'code' => 'EDIT',
        'can_view' => true,
        'can_edit' => true,
        'can_manage' => false,
    ),
    'trial_permission_manage' => array(
        'id' => 3,
        'name' => 'Manage',
        'code' => 'MANAGE',
        'can_view' => true,
        'can_edit' => true,
        'can_manage' => true,
    ),
);
