@coding
Feature: Coding only

  Scenario: ROUTE 18: Login and create a new Examination Event:
  Site:1 Queens, Firm:1 Anderson Cataract.
  Clinical Management: Overall Management & Current Management

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

    Then I choose to expand the Visual Function section

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "2"
    Then I choose a left Visual Acuity Snellen Metre "4" and a reading method of "5"
    Then I choose a right Visual Acuity Snellen Metre "4" and a reading method of "5"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "06:05"
    And I enter a right Dilation time of "18:45"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I choose to expand the Adnexal Comorbidity section
    And I add a left Adnexal Comorbidity of "crusting of lashes, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

    Then I choose to expand the Pupillary Abnormalities section
    And I add a left Abnormality of "3"
    And I add a right Abnormality of "4"

    Then I choose to expand the Diagnoses section
    And I choose a left eye diagnosis
    Then I choose a diagnoses of "95217000"
    And I choose a right eye diagnosis
    Then I choose a diagnoses of "34361001"
    And I choose both eyes diagnosis
    Then I choose a diagnoses of "79410001"
    Then I choose a principal diagnosis

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

    And I choose a Right Risks of "1"
    Then I choose Right Injection Management Comments of "Automation Test Comments"

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

    And I choose a Left Risks of "1"
    Then I choose Left Injection Management Comments of "Automation Test Comments"

    Then I choose to expand the Overall Management section
    And I choose a Clinical Interval of "1"
    And I choose a Photo of "1"
    And I choose a OCT of "4"
    And I choose a Visual Fields of "5"
    And I choose Overall Management Section Comments of "Automation Test Comments"
    And I choose a Gonio of "2"
    And I choose a Right Target IOP of "15"
  #    And I choose a Right Gonio of "2"
    And I choose a Left Target IOP of "15"
  #    And I choose a Left Gonio of "3"

    Then I choose to expand the Current Management section

  #    And I choose a Referral of Other Service
  #    And I choose a Referral of Refraction
  #    And I choose a Referral of LVA
  #    And I choose a Referral of Orthopics
  #    And I choose a Referral of CL clinic
  #
  #    Then I choose Investigations of VF
  #    Then I choose Investigations of US
  #    Then I choose Investigations of Biometry
  #    Then I choose Investigations of OCT
  #    Then I choose Investigations of HRT
  #    Then I choose Investigations of Disc Photos
  #    Then I choose Investigations of EDT

    And I select a Left Glaucoma Status of "1"
    And I select a Left Drop related problem of "2"
    And I select a Left Drops of "4"
    And I select a Left Surgery of "5"

    And I select a Right Glaucoma Status of "1"
    And I select a Right Drop related problem of "2"
    And I select a Right Drops of "4"
    And I select a Right Surgery of "5"

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

    Then I Save the Examination and confirm it has been created successfully