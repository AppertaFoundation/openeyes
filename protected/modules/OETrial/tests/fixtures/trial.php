<?php

return array(
    'trial1' => array(
        'id' => 1,
        'name' => 'Trial 1',
        'description' => 'Trial Description',
        'owner_user_id' => $this->getRecord('user', 'user1')->id,
        'pi_user_id' => $this->getRecord('user', 'user1')->id,
        'is_open' => 1,
        'trial_type' => Trial::TRIAL_TYPE_INTERVENTION,
    ),
    'trial2' => array(
        'id' => 2,
        'name' => 'Trial 2',
        'description' => 'Trial Description',
        'owner_user_id' => $this->getRecord('user', 'user1')->id,
        'pi_user_id' => $this->getRecord('user', 'user1')->id,
        'is_open' => 1,
        'trial_type' => Trial::TRIAL_TYPE_INTERVENTION,
        'closed_date' => '2000/02/29',
    ),
    'trial3' => array(
        'id' => 3,
        'name' => 'Trial 3',
        'description' => 'Trial Description',
        'owner_user_id' => $this->getRecord('user', 'user1')->id,
        'pi_user_id' => $this->getRecord('user', 'user1')->id,
        'is_open' => 0,
        'trial_type' => Trial::TRIAL_TYPE_INTERVENTION,
        'closed_date' => '2000/02/29',
    ),

    'non_intervention_trial_1' => array(
        'id' => 4,
        'name' => 'Trial 4 - Non Intervention',
        'description' => 'Trial Description',
        'owner_user_id' => $this->getRecord('user', 'user1')->id,
        'pi_user_id' => $this->getRecord('user', 'user1')->id,
        'is_open' => 1,
        'trial_type' => Trial::TRIAL_TYPE_NON_INTERVENTION,
    ),
);
