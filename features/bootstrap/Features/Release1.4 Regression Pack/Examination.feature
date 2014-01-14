@examination @regression
Feature: Create New Examination Regression Tests
  Regression over 2 Sites and 4 Firms
  Coverage at 60%

  Scenario: Route 1: Login and create a new Examination Event: Site 1:Queens, Firm:3 Anderson Glaucoma

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
    Then I Add a Comorbiditiy of "4"

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

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "1" integer "6" fraction "0.75"
    And I enter left cylinder details of of Cylinder "-1" integer "7" fraction "0.75"
    And I enter a left type of "5"
    Then I enter left Axis degrees of "38"


    Then I enter right Refraction details of Sphere "1" integer "3" fraction "0.50"
    And I enter right cylinder details of of Cylinder "-1" integer "4" fraction "0.25"
    Then I enter right Axis degrees of "145"
    And I enter a right type of "1"

    Then I Save the Examination

    Then a check is made that a left Axis degrees of "38" was entered
    Then a check is made that a right Axis degrees of "145" was entered


  Scenario: Route 2:Login and create a new Examination Event: Site:1 Queens, Firm:2 Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "4"

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I Save the Examination
#
  Scenario: Route 3:Login and create a new Examination Event: Site:1 Queens, Firm:1 Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "8"

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I Save the Examination

  Scenario: Route 4: Login and create a new Examination Event: Site:1 Queens, Firm:1 Anderson Cataract.
            Opening every additional Optional Element that can be included in Automation tests (excluding EyeDraw elements)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "8"

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I choose to expand the Adnexal Comorbidity section
    Then I choose to expand the Adnexal Comorbidity section
    And I add a left Adnexal Comorbidity of "lower lid ectropion, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

    Then I choose to expand the Pupillary Abnormalities section
    And I add a left Abnormality of "2"
    And I add a right Abnormality of "4"

    Then I choose to expand the Diagnoses section
    And I choose a left eye diagnosis
    Then I choose a diagnoses of "230670003"
    And I choose a right eye diagnosis
    Then I choose a diagnoses of "53889007"
    And I choose both eyes diagnosis
    Then I choose a diagnoses of "193570009"

    Then I choose to expand the Investigation section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    Then I choose to expand the Clinical Management section
    And I choose to expand Cataract Surgical Management
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case

    And the post operative target has been discussed with patient Yes
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery Yes
    And I select Vitrectomised Eye Yes

    Then I choose to expand the Laser Management section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    Then I choose to expand the Injection Management section

    And I select a Right Diagnosis of Choroidal Retinal Neovascularisation
    Then I select Right Secondary to "267718000"

    And I select a Left Diagnosis of Choroidal Retinal Neovascularisation
    Then I select Left Secondary to "267718000"

    Then I choose a Right CRT Increase <100 of Yes

    Then I choose a Right CRT >=100 of Yes

    Then I choose a Right Loss of 5 letters No
    Then I choose a Right Loss of 5 letters >5 No
    Then I choose a Left CRT Increase <100 of No
    Then I choose a Left CRT >=100 of No
    Then I choose a Left Loss of 5 letters Yes
    Then I choose a Left Loss of 5 letters >5 No

    Then I choose to expand the Risks section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    Then I choose to expand the Clinic Outcome section

    And I choose a Clinical Outcome Status of Discharge

    Then I choose to expand the Conclusion section
    And I choose a Conclusion option of "booked for first eye, "

    Then I Save the Examination


  Scenario: ROUTE 5: Login and create a new Examination Event: Site:1 Queens, Firm:1 Anderson Cataract.
  Opening every additional Optional Element that can be included in Automation tests (excluding EyeDraw elements)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "8"

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I choose to expand the Adnexal Comorbidity section
    And I add a left Adnexal Comorbidity of "crusting of lashes, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

    Then I choose to expand the Pupillary Abnormalities section
    And I add a left Abnormality of "2"
    And I add a right Abnormality of "4"

    Then I choose to expand the Diagnoses section
    And I choose a left eye diagnosis
    Then I choose a diagnoses of "95217000"
    And I choose a right eye diagnosis
    Then I choose a diagnoses of "34361001"
    And I choose both eyes diagnosis
    Then I choose a diagnoses of "79410001"

    Then I choose to expand the Investigation section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    Then I choose to expand the Clinical Management section
    And I choose to expand Cataract Surgical Management
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case

    And the post operative target has been discussed with patient No
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery No
    And I select Vitrectomised Eye No

    Then I choose to expand the Laser Management section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    Then I choose to expand the Injection Management section

    And I select a Right Diagnosis of Choroidal Retinal Neovascularisation
    Then I select Right Secondary to "255025001"
    And I select a Right Intended Treatment of "1"

    And I select a Left Diagnosis of Choroidal Retinal Neovascularisation
    Then I select Left Secondary to "414783007"
    And I select a Left Intended Treatment of "7"

    Then I choose a Right CRT Increase <100 of No

    Then I choose a Right CRT >=100 of No

    Then I choose a Right Loss of 5 letters Yes
    Then I choose a Right Loss of 5 letters >5 Yes
    Then I choose a Left CRT Increase <100 of Yes
    Then I choose a Left CRT >=100 of Yes
    Then I choose a Left Loss of 5 letters No
    Then I choose a Left Loss of 5 letters >5 Yes

    Then I choose to expand the Risks section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    Then I choose to expand the Clinic Outcome section

    And I choose a Clinical Outcome Status of Follow Up
    Then I choose a Follow Up quantity of "5"
    And I choose a Follow Up period of "2"
    And I tick the Patient Suitable for Community Patient Tariff
    Then I choose a Role of "4"

    Then I choose to expand the Conclusion section
    And I choose a Conclusion option of "glasses prescribed, "

    Then I Save the Examination
#
  Scenario: ROUTE 6: Login and create a new Examination Event: Site:1 Queens, Firm:1 Anderson Cataract.
  Opening every additional Optional Element that can be included in Automation tests (excluding EyeDraw elements)
    This Route focuses on the remaining additional Injection Management sections

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "8"

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "145" and a reading method of "2"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I choose to expand the Adnexal Comorbidity section
    And I add a left Adnexal Comorbidity of "crusting of lashes, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

    Then I choose to expand the Pupillary Abnormalities section
    And I add a left Abnormality of "2"
    And I add a right Abnormality of "4"

    Then I choose to expand the Diagnoses section
    And I choose a left eye diagnosis
    Then I choose a diagnoses of "95217000"
    And I choose a right eye diagnosis
    Then I choose a diagnoses of "34361001"
    And I choose both eyes diagnosis
    Then I choose a diagnoses of "79410001"

    Then I choose to expand the Investigation section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    Then I choose to expand the Clinical Management section
    And I choose to expand Cataract Surgical Management
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case

    And the post operative target has been discussed with patient No
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery No
    And I select Vitrectomised Eye No

    Then I choose to expand the Laser Management section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    Then I choose to expand the Injection Management section

    And I select a Right Diagnosis of Macular retinal oedema
    Then I select Right Secondary of Venous retinal branch occlusion
    And I select a Right Intended Treatment of "4"

    Then I choose a Right Failed Laser of Yes
    Then I choose a Right Failed Laser of No
    Then I choose a Right Unsuitable Laser of Yes
    Then I choose a Right Unsuitable Laser of No
    Then I choose a Right Previous Ozurdex Yes
    Then I choose a Right Previous Ozurdex No


    And I select a Left Diagnosis of Macular retinal oedema
    Then I select Left Secondary of Diabetic macular oedema
    And I select a Left Intended Treatment of "2"

    Then I choose a Left CRT above Four Hundred of Yes
    Then I choose a Left CRT above Four Hundred of No
    Then I choose a Left Foveal Structure Damage Yes
    Then I choose a Left Foveal Structure Damage No
    Then I choose a Left Failed Laser of Yes
    Then I choose a Left Failed Laser of No
    Then I choose a Left Unsuitable Laser of Yes
    Then I choose a Left Unsuitable Laser of No
    Then I choose a Left Previous Anti VEGF of Yes
    Then I choose a Left Previous Anti VEGF of No

    Then I choose to expand the Risks section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    Then I choose to expand the Clinic Outcome section

    And I choose a Clinical Outcome Status of Discharge

    And I choose a Clinical Outcome Status of Follow Up
    Then I choose a Follow Up quantity of "2"
    And I choose a Follow Up period of "1"
    And I tick the Patient Suitable for Community Patient Tariff
    Then I choose a Role of "1"

    Then I choose to expand the Conclusion section
    And I choose a Conclusion option of "booked for first eye, "

    Then I Save the Examination

#  BLOCKED OE-3959 Scenario: Route 7: Examination Validation Tests (Anderson Glaucoma)
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "1"
#    Then I select a firm of "3"
#
#    Then I search for hospital number "1009465"
#
#    Then I select the Latest Event
#    Then I expand the Glaucoma sidebar
#    And I add a New Event "Examination"
#
#    Then I Save the Examination
#
#    Then I Confirm that the History Validation error message is displayed
#    Then I Confirm that the Conclusion Validation error message is displayed
#
  Scenario: Route 8: Examination Validation Tests (Anderson Cataract)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I Save the Examination

    Then I Confirm that the History Validation error message is displayed
    Then I Confirm that the Dilation Validation error message is displayed
#
#  BLOCKED OE-3959 Scenario: Route 9: Examination Validation Tests (Anderson Medical Retinal)
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "1"
#    Then I select a firm of "4"
#
#    Then I search for hospital number "1009465"
#
#    Then I select the Latest Event
#    Then I expand the Medical Retinal sidebar
#    And I add a New Event "Examination"
#
#    Then I Save the Examination
#
#    Then I Confirm that the History Validation error message is displayed
#    Then I Confirm that the Dilation Validation error message is displayed
#
#  BLOCKED OE-3959 Scenario: Route 10: Examination Validation Tests (Broom Glaucoma)
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "1"
#    Then I select a firm of "2"
#
#    Then I search for hospital number "1009465"
#
#    Then I select the Latest Event
#    Then I expand the Glaucoma sidebar
#    And I add a New Event "Examination"
#
#    Then I Save the Examination
#
#    Then I Confirm that the History Validation error message is displayed
#    Then I Confirm that the Dilation Validation error message is displayed
##
  Scenario: Route 11: Examination Validation Tests (Remove All Error)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select Close All elements

    Then I Save the Examination

    Then I confirm that the Remove All Validation error message is displayed

  Scenario: Route 12: Examination Validation Tests (Select All and Save Validation errors)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select Add All optional elements

    Then I Save the Examination

    Then I confirm that the Add All Validation error messages have been displayed