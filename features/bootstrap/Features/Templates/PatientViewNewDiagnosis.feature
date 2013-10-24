
Feature: Open Eyes Login and Patient Diagnosis Screen Template
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies
    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    Then I search for hospital number "<hospnumber>"

    Then I Add an Ophthalmic Diagnosis selection of "<OphtDiagnosis>"
    And I select that it affects eye "<eye>"
    And I select a Opthalmic Diagnosis date of day "<day>" month "<month>" year "<year>"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "<SystDiagnosis>"
    And I select that it affects Systemic side "<side>"
    And I select a Systemic Diagnosis date of day "<day>" month "<month>" year "<year>"

    Then I save the new Systemic Diagnosis
    Then I Add a Previous Operation of "1"
    And I select that it affects Operation side "<side>"
    And I select a Previous Operation date of day "<day>" month "<month>" year "<year>"
    Then I save the new Previous Operation

#    And I Add Medication details medication "3" route "2" frequency "8" date from "1" and Save

    Then I edit the CVI Status "4"
    And I select a CVI Status date of day "<day>" month "<month>" year "<year>"
    Then I save the new CVI status

#    Then I Remove existing Allergy
    Then I confirm the patient has no allergies and Save
#    Then I Add Allergy "5" and Save

    And I Add a Family History of relative "1" side "3" condition "1" and comments "Family History Comments" and Save

  # NOTES: The Medication detail date occasionally fails with the datepicker and a solution so far has not been found

  Examples: User details
    | environment    | hospnumber   | nhs        | last    | first  |OphtDiagnosis | eye   | day | month | year | SystDiagnosis | side |
    | master         | 1009465      | 8821388753 | Coffin, | Violet |193570009     | Both  | 18  |  6    | 2012 | 195967001     | Left |


  # Site ID's:
  # Queens - 1

  # Firm 1 = Anderson Firm (Cataract)

  # Last name to include a comma after to match search criteria i.e Coffin,

  # Ophthalmic Diagnosis  (Using SNOMED codes)
  # Cataract - 193570009

  # Systemic Diagnosis  (Using SNOMED codes)
  # Asthma - 195967001

  # Allergy (Values need to be defined not as 1,2,3 etc)
  # Sulphonamides - 2
  # Fluorescin - 13
  # Acetazolamide - 9
  # Tetracycline - 5

#Relative
#  1>Mother
#  2>Father
#  3>Brother
#  4>Sister
#  5>Uncle
#  6>Aunt
#  7>Cousin
#  8>Grandmother
#  9>Grandfather
#  10>Other


