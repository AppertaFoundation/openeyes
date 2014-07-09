@coding
Feature: Coding only

  Scenario: Route 2A: Login and create a new Examination Event
  Create a Correspondence event including Findings from the Examination

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "1"
    Then I Add a Comorbiditiy of "2"
    Then I Add a Comorbiditiy of "3"
    Then I Add a Comorbiditiy of "4"
    Then I Add a Comorbiditiy of "5"
    Then I Add a Comorbiditiy of "6"
    Then I Add a Comorbiditiy of "7"
    Then I Add a Comorbiditiy of "8"
    Then I Add a Comorbiditiy of "9"
    Then I Add a Comorbiditiy of "10"
    Then I Add a Comorbiditiy of "11"
    Then I Add a Comorbiditiy of "12"
  #    Then I Add a Comorbiditiy of "13"
  #    Then I Add a Comorbiditiy of "14"
  #    Then I Add a Comorbiditiy of "15"

    Then I remove all comorbidities

    Then I choose to expand the Refraction section

    Then I enter left Refraction details of Sphere "1" integer "6" fraction "0.75"
    And I enter left cylinder details of of Cylinder "-1" integer "7" fraction "0.75"

    Then I enter left Axis degrees of "145"
#    Then I enter left Axis degrees of "145"

    And I enter a left type of "5"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"

    Then I enter right Axis degrees of "38"
#    Then I enter right Axis degrees of "38"
    And I enter a right type of "3"

    Then I choose to expand the Visual Function section

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "2"
    Then I choose a left Visual Acuity Snellen Metre "4" and a reading method of "5"
    Then I choose a right Visual Acuity Snellen Metre "4" and a reading method of "5"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "19" and Instrument "2"
    Then I choose a right Intraocular Pressure of "29" and Instrument "2"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "2" and drops of "5"
    Then I choose right Dilation of "6" and drops of "3"

    Then I choose to remove left Dilation treatment

    Then I Save the Examination and confirm it has been created successfully

    Then a check is made that a left Axis degrees of "145" was entered
    Then a check is made that a right Axis degrees of "38" was entered
