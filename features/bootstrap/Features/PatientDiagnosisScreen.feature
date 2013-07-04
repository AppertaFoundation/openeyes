@NewDiagnosis
Feature: Open Eyes Login and Patient Diagnosis Screen

  Scenario Outline: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "1"

    #Then I search for hospital number "<hospnumber>"
    Then I search for patient name last name "<last>" and first name "<first>"
    #Then I search for NHS number "<nhs>"

    Then I Add an Ophthalmic Diagnosis selection of "<OphtDiagnosis>"
    And I select that it affects eye "<eye>"
    And I select a Opthalmic Diagnosis date of day "<day>" month "<month>" year "<year>"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "<SystDiagnosis>"
    And I select that it affects side "<side>"
    And I select a Systemic Diagnosis date of day "<day>" month "<month>" year "<year>"
    Then I save the new Systemic Diagnosis

    Then I edit the CVI Status "<CVIstatus>" day "<day>" month "<month>" year "<year>"

    And I Add Medication details medication "<medication>" route "<route>" frequency "<frequency>" date from "<datefrom>"

    Then I Add Allergy "<allergy>"

    Then I remove diagnosis test data
    Then I remove medication test data
    Then I remove allergy test data

    #Then I choose to close the browser

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  |OphtDiagnosis | eye   | day | month | year | SystDiagnosis | side | allergy | CVIstatus | medication | route | frequency | datefrom |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet |193570009     | Both  | 18  |  6    | 2012 | 195967001     | Left | 5       |   4       |   3        |  2    |  7        | 12       |


  # Site ID's:
  # Queens - 1

  # Firm 1 = Abderson Firm (Cataract)

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


