<?php

class m181107_031357_cera_ethnic_groups extends CDbMigration
{

  // Use safeUp/safeDown to do migration with transaction
  public function safeUp()
  {
    /**
     * @var $ethnicity EthnicGroup
     * @var $patient Patient
     */
    // Save all ethnic groups so the original values can be versioned.
    $ethnicGroups = EthnicGroup::model()->findAll();
    foreach ($ethnicGroups as $ethnicity) {
      $ethnicity->save();
    }

    // Mark all patients whose ethnic group is being removed as having Other ethnicity.
    $patients = Patient::model()->findAll();
    foreach ($patients as $patient) {
      $patient->ethnic_group_id = 16;
      if (!$patient->save()) {
        throw new CDbException("Unable to save patient $patient->id: " . print_r($patient->getErrors(),true));
      }
    }

    // Remove the unused ethnic groups.
    $removedEthnicGroups = EthnicGroup::model()->findAll('id BETWEEN 8 AND 15');
    foreach ($removedEthnicGroups as $ethnicity) {
      if (!$ethnicity->delete()) {
        throw new CDbException("Unable to delete ethnic group $ethnicity->id");
      }
    }

    // Change the text of the remaining ethnic groups.
    $ethnicGroups = EthnicGroup::model()->findAll();
    foreach ($ethnicGroups as $ethnicity) {
      switch ($ethnicity->id) {
        case 1:
          $ethnicity->name = 'Australia';
          break;
        case 2:
          $ethnicity->name = 'China';
          break;
        case 3:
          $ethnicity->name = 'Greece';
          break;
        case 4:
          $ethnicity->name = 'Italy';
          break;
        case 5:
          $ethnicity->name = 'New Zealand';
          break;
        case 6:
          $ethnicity->name = 'United Kingdom';
          break;
        case 7:
          $ethnicity->name = 'India';
          break;
        case 16:
          $ethnicity->name = 'Other';
          break;
        case 17:
          $ethnicity->name = 'Don\'t know';
          break;
        default:
          throw new UnexpectedValueException('Invalid ethnic group ID detected.');
      }
      $ethnicity->save();
    }
  }

  public function safeDown()
  {
    return false;
  }

}