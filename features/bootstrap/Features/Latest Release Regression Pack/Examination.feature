@examination @regression
Feature: Create New Examination Regression Tests
@EXAM
          Regression over 2 Sites and 4 Firms
          Regression coverage of this event is approx 70%

  @EXAM_Route_1 @OE-5606 @sprint22
  Scenario Outline: Route 1: Login and create a new Examination Event
            Site 1:Croydon
            Firm 3:A K Hamilton (Glaucoma)
            Add and then remove all Comorbidities
            Viusal Fields, Intraocular Pressure, Dilation
            Confirm that Refraction Axis entries are correctly validated when the Examination is saved

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNo.>"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "<sectionName1>" section
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

#    Then I remove all comorbidities

    Then I choose to expand the "<sectionName2>" section

    Then I enter left Refraction details of Sphere "1" integer "6" fraction "0.75"
    And I enter left cylinder details of of Cylinder "-1" integer "7" fraction "0.75"

    Then I enter left Axis degrees of "145"
#    Then I enter left Axis degrees of "145"

    And I enter a left type of "5"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"

    Then I enter right Axis degrees of "38"

    And I enter a right type of "3"

    Then I choose to expand the "<sectionName3>" section


    And I add Left RAPD comments of "Left RAPD Automation test comments"


    And I add Right RAPD comments of "Left RAPD Automation test comments"

    Then I choose to expand the "<sectionName4>" section
    And I choose a Left Colour Vision of "1"

    And I choose a Right Colour Vision of "2"


    Then I choose to expand the "<sectionName5>" section
    And I select a Visual Acuity of "2"
    Then I choose a left Visual Acuity Snellen Metre "4" and a reading method of "5"
    Then I choose a right Visual Acuity Snellen Metre "4" and a reading method of "5"

    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I choose to expand the "<sectionName6>" section
    Then I choose a left Intraocular Pressure of "19" and Instrument "2"
    Then I choose a right Intraocular Pressure of "29" and Instrument "2"

    Then I choose to expand the "<sectionName7>" section
    Then I choose left Dilation of "2" and drops of "5"
    Then I choose right Dilation of "6" and drops of "3"
    And I enter a left Dilation time of "10:00"
    And I enter a right Dilation time of "13:11"

    Then I Save the Examination and confirm it has been created successfully

    Then a check is made that a left Axis degrees of "145" was entered
    Then a check is made that a right Axis degrees of "38" was entered

    Examples:
    |uname|pwd  |site   |firm                   |hospNo.|speciality|event      |sectionName1 |sectionName2|sectionName3   |sectionName4 |sectionName5 |sectionName6         |sectionName7|
    |admin|admin|Croydon|A K Hamilton (Glaucoma)|1009465|Glaucoma  |Examination|Comorbidities|Refraction  |Visual Function|Colour Vision|Visual Acuity|Intraocular Pressure|Dilation     |

  @EXAM_Route_2
  Scenario Outline: Route 2:Login and create a new Examination Event
            Site:1 Ludwig
            Firm:2 A Dulku (Glaucoma)
            Viusal Fields, Intraocular Pressure, Dilation, Refraction
            Add a second Visual Acuity reading and then remove it
            Removal of one side test

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "<sectionName1>" section
    Then I Add a Comorbiditiy of "4"

    Then I choose to expand the "<sectionName2>" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

    Then I choose to expand the "<sectionName3>" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"


    Then I choose to expand the "<sectionName4>" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    #And I choose to add a new left Visual Acuity reading of "6" and a reading method of "4"
    #And I choose to add a new Right Visual Acuity reading of "3" and a reading method of "2"

    #Then I remove the newly added Left Visual Acuity
    #Then I remove the newly added Right Visual Acuity

    Then I choose to expand the "<sectionName5>" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the "<sectionName6>" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "20:00"
    And I enter a right Dilation time of "16:11"

    Then I choose to expand the "<sectionName7>" section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I remove Refraction right side

    Then I Save the Examination and confirm it has been created successfully

    Examples:
      |uname|pwd  |site  |firm              |speciality|event      |sectionName1 |sectionName2   |sectionName3 |sectionName4 |sectionName5        |sectionName6|sectionName7|
      |admin|admin|Ludwig|A Dulku (Glaucoma)|Glaucoma  |Examination|Comorbidities|Visual Function|Colour Vision|Visual Acuity|Intraocular Pressure|Dilation    |Refraction  |

  @EXAM_Route_3
  Scenario Outline: Route 3:Login and create a new Examination Event
  Site:1 Barking
  Firm:1 Paul Godinho (Cataract)
  Viusal Fields, Intraocular Pressure, Dilation, Refraction, Conclusion

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "<sectionName1>" section
    Then I Add a Comorbiditiy of "8"

#Then I choose to expand the Visual Function section
    Then I choose to expand the "<sectionName2>" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

#Then I choose to expand the Colour Vision section
    Then I choose to expand the "<sectionName3>" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"

#Then I choose to expand the Visual Acuity section
    Then I choose to expand the "<sectionName4>" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"


#Then I choose to expand the Intraocular Pressure section
    Then I choose to expand the "<sectionName5>" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

#Then I choose to expand the Dilation section
    Then I choose to expand the "<sectionName6>" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "08:35"
    And I enter a right Dilation time of "22:12"

#Then I choose to expand the Refraction section
    Then I choose to expand the "<sectionName7>" section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

#Then I choose to expand the Conclusion section
    Then I choose to expand the "<sectionName8>" section
    And I choose a Conclusion option of "booked for first eye, "

    Then I Save the Examination and confirm it has been created successfully
    Examples:
      |uname|pwd  |site   |firm                   |speciality|event      |sectionName1 |sectionName2   |sectionName3 |sectionName4 |sectionName5        |sectionName6|sectionName7|sectionName8      |
      |admin|admin|Barking|Paul Godinho (Cataract)|Cataract  |Examination|Comorbidities|Visual Function|Colour Vision|Visual Acuity|Intraocular Pressure|Dilation    |Refraction  |Conclusion        |
##
  @EXAM_Route_4
  Scenario Outline: Route 4: Login and create a new Examination Event
            Site:1 Barking
            Firm:1 Paul Godinho (Cataract)
            Opening every additional Optional Element that can be included in Automation tests (excluding EyeDraw elements)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "Comorbidities" section
    Then I Add a Comorbiditiy of "8"

#Then I choose to expand the Visual Function section
    Then I choose to expand the "Visual Function" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

 #Then I choose to expand the Colour Vision section
    Then I choose to expand the "Colour Vision" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"

#Then I choose to expand the Visual Acuity section
    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

#Then I choose to expand the Intraocular Pressure section
    Then I choose to expand the "Intraocular Pressure" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

#Then I choose to expand the Dilation section
    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "08:35"
    And I enter a right Dilation time of "22:12"

#Then I choose to expand the Refraction section
    Then I choose to expand the "Refraction" section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

#Then I choose to expand the Adnexal Comorbidity section
    Then I choose to expand the "Adnexal Comorbidity" section

    And I add a left Adnexal Comorbidity of "lower lid ectropion, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

     #Then I choose to expand the Pupillary Abnormalities section
    Then I choose to expand the "Pupillary Abnormalities" section
    And I add a left Abnormality of "3"
    And I add a right Abnormality of "4"

    #Then I choose to expand the Diagnoses section
    Then I choose to expand the "Diagnoses" section
    And I choose a left eye diagnosis
    And I select Diagnosis of Cataract

    #Then I choose to expand the Investigation section
    Then I choose to expand the "Investigation" section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    #Then I choose to expand the Clinical Management section
    Then I choose to expand the "Clinical Management" section
    #Then I choose to expand Cataract Surgical Management
    Then I choose to expand the "Cataract Surgical Management" section
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case

    And I select a post operative refractive target in dioptres of "2"

    And the post operative target has been discussed with patient Yes
    Then I select a suitable for surgeon of "3"
    Then I select Previous Refractive Surgery Yes
    And I select Vitrectomised Eye Yes

    #Then I choose to expand the Laser Management section
    Then I choose to expand the "Laser Management" section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    #Then I choose to expand the Injection Management section
    Then I choose to expand the "Injection Management" section
    And I select a Right Diagnosis of Choroidal Retinal Neovascularisation
    Then I select Right Secondary to "312956001"
    And I select a Right Intended Treatment of "1"

    And I select a Left Diagnosis of Choroidal Retinal Neovascularisation
    Then I select Left Secondary to "312956001"
    And I select a Left Intended Treatment of "7"

    Then I choose a Right CRT Increase <100 of Yes

    Then I choose a Right CRT >=100 of Yes

    Then I choose a Right Loss of 5 letters No
    Then I choose a Right Loss of 5 letters >5 No
    Then I choose a Left CRT Increase <100 of No
    Then I choose a Left CRT >=100 of No
    Then I choose a Left Loss of 5 letters Yes
    Then I choose a Left Loss of 5 letters >5 No

    #Then I choose to expand the Risks section
    Then I choose to expand the "Risks" section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    #Then I choose to expand the Clinic Outcome section
    Then I choose to expand the "Clinic Outcome" section
    And I choose a Clinical Outcome Status of Discharge

    #Then I choose to expand the Conclusion section
    Then I choose to expand the "Conclusion" section
    And I choose a Conclusion option of "booked for surgery, "

    Then I Save the Examination and confirm it has been created successfully

    Examples:
      |uname|pwd  |site   |firm                   |speciality|event      |sectionName1 |
      |admin|admin|Ludwig|A Dulku (Glaucoma)      |Glaucoma  |Examination|comorbidities|
#
  @EXAM_Route_5
  Scenario Outline: ROUTE 5: Login and create a new Examination Event
            Site:1 Barking
            Firm:1 Paul Godinho (Cataract)
            Injection management route

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "<sectionName1>" section
    Then I Add a Comorbiditiy of "8"

 #Then I choose to expand the Visual Function section
    Then I choose to expand the "Visual Function" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

 #Then I choose to expand the Colour Vision section
    Then I choose to expand the "Colour Vision" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"

#Then I choose to expand the Visual Acuity section
    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"


#Then I choose to expand the Intraocular Pressure section
    Then I choose to expand the "Intraocular Pressure" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

#Then I choose to expand the Dilation section
    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "09:14"
    And I enter a right Dilation time of "12:00"

#Then I choose to expand the Refraction section
    Then I choose to expand the "Refraction" section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

#Then I choose to expand the Adnexal Comorbidity section
    Then I choose to expand the "Adnexal Comorbidity" section

    And I add a left Adnexal Comorbidity of "crusting of lashes, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

     #Then I choose to expand the Pupillary Abnormalities section
    Then I choose to expand the "Pupillary Abnormalities" section
    And I add a left Abnormality of "3"
    And I add a right Abnormality of "4"

    #Then I choose to expand the Diagnoses section
    #And I choose a left eye diagnosis
    #And I select "Pseudophakia" for "Diagnosis"
    #Then I choose a principal diagnosis
    #Then I choose to expand the Investigation section
    Then I choose to expand the "Investigation" section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    #Then I choose to expand the Clinical Management section
    Then I choose to expand the "Clinical Management" section
    #Then I choose to expand Cataract Surgical Management
    Then I choose to expand the "Cataract Surgical Management" section
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case

    And I select a post operative refractive target in dioptres of "2"

    And the post operative target has been discussed with patient No
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery No
    And I select Vitrectomised Eye No

    #Then I choose to expand the Laser Management section
    Then I choose to expand the "Laser Management" section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    #Then I choose to expand the Injection Management section
    Then I choose to expand the "Injection Management" section
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

      #Then I choose to expand the Risks section
    Then I choose to expand the "Risks" section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    #Then I choose to expand the Clinic Outcome section
    Then I choose to expand the "Clinic Outcome" section

    And I choose a Clinical Outcome Status of Follow Up
    Then I choose a Follow Up quantity of "5"
    And I choose a Follow Up period of "2"
    And I tick the Patient Suitable for Community Patient Tariff
    Then I choose a Role of "4"

    #Then I choose to expand the Conclusion section
    Then I choose to expand the "Conclusion" section
    And I choose a Conclusion option of "glasses prescribed, "

    Then I Save the Examination and confirm it has been created successfully

  Examples:
  |uname|pwd  |site   |firm                   |speciality|event      |sectionName1 |
  |admin|admin|Ludwig|A Dulku (Glaucoma)      |Glaucoma  |Examination|Comorbidities|
##
  @EXAM_Route_6
  Scenario Outline: ROUTE 6: Login and create a new Examination Event:
            Site: Barking
            Firm: Paul Godinho (Cataract)
            Remaining additional Injection Management sections

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "<sectionName1>" section
    Then I Add a Comorbiditiy of "8"

  #Then I choose to expand the Visual Function section
    Then I choose to expand the "Visual Function" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

 #Then I choose to expand the Colour Vision section
    Then I choose to expand the "Colour Vision" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"

#Then I choose to expand the Visual Acuity section
    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

#Then I choose to expand the Intraocular Pressure section
    Then I choose to expand the "Intraocular Pressure" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

#Then I choose to expand the Dilation section
    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "06:05"
    And I enter a right Dilation time of "18:45"

#Then I choose to expand the Refraction section
    Then I choose to expand the "Refraction" section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

#Then I choose to expand the Adnexal Comorbidity section
    Then I choose to expand the "Adnexal Comorbidity" section
    And I add a left Adnexal Comorbidity of "crusting of lashes, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

     #Then I choose to expand the Pupillary Abnormalities section
    Then I choose to expand the "Pupillary Abnormalities" section
    And I add a left Abnormality of "3"
    And I add a right Abnormality of "4"

    #Then I choose to expand the Investigation section
    Then I choose to expand the "Investigation" section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    #Then I choose to expand the Clinical Management section
    Then I choose to expand the "Clinical Management" section
    #Then I choose to expand Cataract Surgical Management
    Then I choose to expand the "Cataract Surgical Management" section
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case
    And I select a post operative refractive target in dioptres of "2"

    And the post operative target has been discussed with patient No
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery No
    And I select Vitrectomised Eye No

    #Then I choose to expand the Laser Management section
    Then I choose to expand the "Laser Management" section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    #Then I choose to expand the Injection Management section
    Then I choose to expand the "Injection Management" section
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

    #Then I choose to expand the Risks section
    Then I choose to expand the "Risks" section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    #Then I choose to expand the Clinic Outcome section
    Then I choose to expand the "Clinic Outcome" section
    And I choose a Clinical Outcome Status of Discharge

    And I choose a Clinical Outcome Status of Follow Up
    Then I choose a Follow Up quantity of "2"
    And I choose a Follow Up period of "1"
    And I tick the Patient Suitable for Community Patient Tariff
    Then I choose a Role of "1"

    #Then I choose to expand the Conclusion section
    Then I choose to expand the "Conclusion" section
    And I choose a Conclusion option of "booked for surgery, "

    Then I Save the Examination and confirm it has been created successfully

    Examples:
      |uname|pwd  |site   |firm                   |speciality|event      |sectionName1 |
      |admin|admin|Ludwig|A Dulku (Glaucoma)      |Glaucoma  |Examination|Comorbidities|
#
  @EXAM_Route_7
  Scenario Outline: Route 7: Examination Validation Tests
            History and Conclusion validation error checks

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNum>"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I choose to expand the "<sectionName1>" section

    Then I Save the Examination

    Then I Confirm that the History Validation error message is displayed
    Then I Confirm that the Conclusion Validation error message is displayed

    #Then I cancel the Examnination event

  Examples:
    |uname|pwd  |site  |firm                  |hospNum|speciality|event      |sectionName1|
    |admin|admin|Ealing|Adam Digpal (Glaucoma)|1009465|Glaucoma  |Examination|Conclusion  |

  @EXAM_Route_8
  Scenario: Route 8: Examination Validation Tests (Anderson Cataract)
            History and Dilation validation error checks

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ealing"
    Then I select a firm of "Cataract firm (Cataract)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I choose to expand the "Dilation" section

    Then I Save the Examination

    Then I Confirm that the History Validation error message is displayed
    Then I Confirm that the Dilation Validation error message is displayed

  @EXAM_Route_9
  Scenario: Route 9: Examination Validation Tests (Anderson Medical Retinal)
            History and Dilation validation error checks

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Barking"
    Then I select a firm of "Admin User (Medical Retinal)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Medical Retinal sidebar
    And I add a New Event "Examination"

    Then I choose to expand the "Dilation" section

    Then I Save the Examination

    Then I Confirm that the History Validation error message is displayed
    Then I Confirm that the Dilation Validation error message is displayed

  @EXAM_Route_10
  Scenario: Route 10: Examination Validation Tests (Broom Glaucoma)
            History and Dilation validation error checks

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ealing"
    Then I select a firm of "Adam Digpal (Glaucoma)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the "glaucoma" sidebar
    And I add a New Event "Examination"

    Then I choose to expand the "Dilation" section

    Then I Save the Examination

    Then I Confirm that the History Validation error message is displayed
    Then I Confirm that the Dilation Validation error message is displayed

  @EXAM_Route_11
  Scenario: Route 11: Examination Validation Tests (Remove All Error)
            Close All elements and attempt to Save - validation errors

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ealing"
    Then I select a firm of "Adam Digpal (Glaucoma)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select Close All elements

    Then I Save the Examination

    Then I confirm that the Remove All Validation error message is displayed
#
  @EXAM_Route_12
  Scenario: Route 12: Examination Validation Tests (Select All and Save Validation errors)
            Select All elements and attempt to Save - validation errors

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ealing"
    Then I select a firm of "Adam Digpal (Glaucoma)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select Add All optional elements

    Then I Save the Examination

    Then I confirm that the Add All Validation error messages have been displayed

  @EXAM_Route_13
  Scenario: Route 13: Examination Validation Tests (Anderson Cataract)
  History and Dilation Invalid Time entry validation error checks

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ealing"
    Then I select a firm of "Cataract firm (Cataract)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "35:00"
    And I enter a right Dilation time of "93:00"

    Then I Save the Examination

    Then I Confirm that the History Validation error message is displayed
    Then I Confirm that the Dilation Invalid time error message is displayed

  @EXAM_Route_14
  Scenario: Route 14: Login and create a new Examination Event
            Site 1:Queens
            Firm 3:Anderson Glaucoma
            DR Grading tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ealing"
    Then I select a firm of "Adam Digpal (Glaucoma)"
    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I choose to expand the "DR Grading" section

    And I select a left Clinical Grading for Retinopathy of "2"
    And I select a left NSC Retinopathy of "2"
    And I select a left Retinopathy photocoagulation of Yes
    And I select a left Clinical Grading for maculopathy of "2"
    And I select a left NSC maculopathy of "3"
    And I select a left Maculopathy photocoagulation of Yes

    And I select a right Clinical Grading for Retinopathy of "2"
    And I select a right NSC Retinopathy of "2"
    And I select a right Retinopathy photocoagulation of No
    And I select a right Clinical Grading for maculopathy of "2"
    And I select a right NSC maculopathy of "4"
    And I select a right Maculopathy photocoagulation of No

    Then I Save the Examination and confirm it has been created successfully

  @EXAM_Route_15
  Scenario: Route 15: Login and create a new Examination Event
            Site 1:Queens
            Firm 4:Anderson Medical Retinal
            DR Grading tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Barking"
    Then I select a firm of "Admin User (Medical Retinal)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Medical Retinal sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I choose to expand the "DR Grading" section

    And I select a left Clinical Grading for Retinopathy of "1"
    And I select a left NSC Retinopathy of "1"
    And I select a left Retinopathy photocoagulation of Yes
    And I select a left Clinical Grading for maculopathy of "1"
    And I select a left NSC maculopathy of "1"
    And I select a left Maculopathy photocoagulation of Yes

    And I select a right Clinical Grading for Retinopathy of "1"
    And I select a right NSC Retinopathy of "1"
    And I select a right Retinopathy photocoagulation of No
    And I select a right Clinical Grading for maculopathy of "1"
    And I select a right NSC maculopathy of "1"
    And I select a right Maculopathy photocoagulation of No

    Then I choose to expand the "Intraocular Pressure" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I Save the Examination and confirm it has been created successfully

  @EXAM_Route_16
  Scenario: Route 16: Login and create a new Examination Event
            Site 1:Queens
            Firm 2:Broom Glaucoma
            DR Grading tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ludwig"
    Then I select a firm of "A Dulku (Glaucoma)"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I choose to expand the "DR Grading" section

    And I select a left Clinical Grading for Retinopathy of "3"
    And I select a left NSC Retinopathy of "3"
    And I select a left Retinopathy photocoagulation of Yes
    And I select a left Clinical Grading for maculopathy of "3"
    And I select a left NSC maculopathy of "3"
    And I select a left Maculopathy photocoagulation of No

    And I select a right Clinical Grading for Retinopathy of "3"
    And I select a right NSC Retinopathy of "1"
    And I select a right Retinopathy photocoagulation of No
    And I select a right Clinical Grading for maculopathy of "3"
    And I select a right NSC maculopathy of "3"
    And I select a right Maculopathy photocoagulation of Yes


    Then I choose to expand the "Intraocular Pressure" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"

    Then I Save the Examination and confirm it has been created successfully

  @EXAM_Route_17
  Scenario: Route 17 :Login and create a new Examination Event
            Site:1 Queens
            Firm:2 Broom Glaucoma
            Visual Fields: Unable to assess and Eye Missing checkboxes

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ludwig"
    Then I select a firm of "A Dulku (Glaucoma)"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "Comorbidities" section
    Then I Add a Comorbiditiy of "4"

    Then I choose to expand the "Visual Function" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

    Then I choose to expand the "Colour Vision" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"

    Then I choose to expand the "Visual Acuity" section

    And I remove the initial Left Visual Acuity
    And I select Left Unable to assess checkbox
    And I select Left Eye Missing checkbox

    And I remove the initial Right Visual Acuity
    And I select Right Unable to assess checkbox
    And I select Right Eye Missing checkbox

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I Save the Examination and confirm it has been created successfully

  @EXAM_Route_18
  Scenario: ROUTE 18: Login and create a new Examination Event:
  Site:1 Queens, Firm:1 Anderson Cataract.
  Clinical Management: Overall Management & Current Management

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "Ludwig"
    Then I select a firm of "A Dulku (Glaucoma)"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "glaucoma" sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the "Comorbidities" section
    Then I Add a Comorbiditiy of "8"

    Then I choose to expand the "Visual Function" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

    Then I choose to expand the "Colour Vision" section
    And I choose a Left Colour Vision of "1"
    And I choose a Right Colour Vision of "2"

    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "2"
    Then I choose a left Visual Acuity Snellen Metre "4" and a reading method of "5"
    Then I choose a right Visual Acuity Snellen Metre "4" and a reading method of "5"

    And I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I choose to expand the "Intraocular Pressure" section
    Then I choose a left Intraocular Pressure of "8" and Instrument "4"
    Then I choose a right Intraocular Pressure of "77" and Instrument "1"

    Then I choose to expand the "Dilation" section
    Then I choose left Dilation of "5" and drops of "4"
    Then I choose right Dilation of "2" and drops of "2"
    And I enter a left Dilation time of "06:05"
    And I enter a right Dilation time of "18:45"

    Then I choose to expand the "Refraction" section
    Then I enter left Refraction details of Sphere "-1" integer "11" fraction "0.50"
    And I enter left cylinder details of of Cylinder "1" integer "4" fraction "0.25"
    Then I enter left Axis degrees of "56"
    And I enter a left type of "2"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"
    Then I enter right Axis degrees of "167"
    Then I enter right Axis degrees of "167"
    And I enter a right type of "3"

    Then I choose to expand the "Adnexal Comorbidity" section
    And I add a left Adnexal Comorbidity of "crusting of lashes, "
    And I add a right Adnexal Comorbidity of "conjunctivitis, "

    Then I choose to expand the "Pupillary Abnormalities" section
    And I add a left Abnormality of "3"
    And I add a right Abnormality of "4"

    Then I choose to expand the "Diagnoses" section
    And I choose both eyes diagnosis
    And I select "Cataract" for "Diagnosis"
    Then I choose a principal diagnosis

    Then I choose to expand the "Investigation" section
    And I add an Investigation of "refraction, "
    And I add an Investigation of "Fluorescein angiography, "
    And I add an Investigation of "OCT, "
    And I add an Investigation of "ultrasound, "
    And I add an Investigation of "field test, "

    #Then I choose to expand the Clinical Management section
    Then I choose to expand the "Clinical Management" section
    #Then I choose to expand Cataract Surgical Management
    Then I choose to expand the "Cataract Surgical Management" section
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye

    And I choose Straightforward case
    And I select a post operative refractive target in dioptres of "2"

    And the post operative target has been discussed with patient No
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery No
    And I select Vitrectomised Eye No

    Then I choose to expand the "Laser Management" section
    And I choose a right laser choice of "4"
    And I choose a left laser choice of "4"
    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    Then I choose to expand the "Injection Management" section

    And I select a Right Diagnosis of Macular retinal oedema
    Then I select Right Secondary of Venous retinal branch occlusion
    And I select a Right Intended Treatment of "4"

    Then I choose a Right Failed Laser of Yes
    Then I choose a Right Failed Laser of No
    Then I choose a Right Unsuitable Laser of Yes
    Then I choose a Right Unsuitable Laser of No
    Then I choose a Right Previous Ozurdex Yes
    Then I choose a Right Previous Ozurdex No


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

    #And I choose a Left Risks of "1"
    Then I choose Left Injection Management Comments of "Automation Test Comments"

    Then I choose to expand the "Glaucoma Overall Management plan" section
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

    Then I choose to expand the "Glaucoma Current Management plan" section

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

    Then I choose to expand the "Risks" section
    And I add comments to the Risk section of "Risk section comments Automation Test"

    Then I choose to expand the "Clinic Outcome" section

    And I choose a Clinical Outcome Status of Discharge

    And I choose a Clinical Outcome Status of Follow Up
    Then I choose a Follow Up quantity of "2"
    And I choose a Follow Up period of "1"
    And I tick the Patient Suitable for Community Patient Tariff
    Then I choose a Role of "1"

    Then I choose to expand the "Conclusion" section
    And I choose a Conclusion option of "booked for surgery, "

    Then I Save the Examination and confirm it has been created successfully

  @EXAM_Route_19 @SP23_OE-5318 @SP23_OE-5343
  Scenario Outline: Route 2:Login and create a new Examination Event

  Clinical Management: Overall Management & Current Management

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    Then I choose to expand the "Visual Function" section

    And I add Left RAPD comments of "Left RAPD Automation test comments"
    And I add Right RAPD comments of "Left RAPD Automation test comments"

    Then I choose to expand the "Visual Acuity" section
    And I select a Visual Acuity of "1"
    Then I choose a left Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"
    Then I choose a right Visual Acuity ETDRS Letters Snellen Metre "119" and a reading method of "2"

    Then I choose to expand the "Near Visual Acuity" section
    And I select a Near Visual Acuity of "7"
    Then I choose a left Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"
    Then I choose a right Near Visual Acuity ETDRS Letters Snellen Metre "50" and a reading method of "2"

    Then I choose to expand the "Clinical Management" section
    Then I choose to expand the "Cataract Surgical Management" section
    Then I select a post operative refractive target in dioptres of "-2"
    And the post operative target has been discussed with patient Yes
    Then I select a suitable for surgeon of "3"
    Then I select Previous Refractive Surgery Yes
    Then I click on Left Eye PCR Risk
    Then I set the "Left" PCR option "Glaucoma" to be "No Glaucoma"
    Then I set the "Left" PCR option "PXF" to be "No"
    Then I set the "Left" PCR option "Diabetic" to be "No Diabetes"
    Then I set the "Left" PCR option "Pupil" to be "Small"
    Then I set the "Left" PCR option "Fundal" to be "No"
    Then I set the "Left" PCR option "Axial" to be "< 26"
    Then I set the "Left" PCR option "Cataract" to be "Yes"
    Then I set the "Left" PCR option "ARB" to be "No"
    Then I set the "Left" PCR option "Doctor" to be "Associate specialist"
    Then I set the "Left" PCR option "Lie" to be "No"

    Then I choose to expand the "Anterior Segment" section
    And I select a Segment of Tube patch and Material drop down of "Sclera"
    And I add Anterior Segment Description of "Anterior Segment Description"
    #And I check the colour of the tube patch is Grey

    Then I Save the Examination and confirm it has been created successfully

    Examples:
    |uname|pwd  |site  |firm              |speciality|event      |
    |admin|admin|Ludwig|A Dulku (Glaucoma)|Glaucoma  |Examination|


  @EXAM_Route_20 @sprint25
      Scenario Outline: To check whether PCR Risk is present and working
        Given I am on the OpenEyes "<page>" homepage
        And I enter login credentials "<username>" and "<password>"
        And I select Site "<site1>"
        Then I select a firm of "<firm>"

        Then I search for hospital number "<searchItem>"

        Then I select Create or View Episodes and Events
        Then I expand the "<specialty>" sidebar
        And I add a New Event "<event>"
        Then I choose to expand the "<sectionName1>" section
        Then I choose to expand the "<sectionName2>" section
        Then I click on Right Eye PCR Risk
        Then I click on Left Eye PCR Risk
        Then I set the "Left" PCR option "Glaucoma" to be "No Glaucoma"
        Then I set the "Left" PCR option "PXF" to be "No"
        Then I set the "Left" PCR option "Diabetic" to be "No Diabetes"
        Then I set the "Left" PCR option "Pupil" to be "Small"
        Then I set the "Left" PCR option "Fundal" to be "No"
        Then I set the "Left" PCR option "Axial" to be "< 26"
        Then I set the "Left" PCR option "Cataract" to be "Yes"
        Then I set the "Left" PCR option "ARB" to be "No"
        Then I set the "Left" PCR option "Doctor" to be "Associate specialist"
        Then I set the "Left" PCR option "Lie" to be "No"
        Then I should have a calculated "Left" PCR value of "4.80"

        #Validation1
        Then I should see reference link on PCR Right Eye block
        Then I should see reference link on PCR Left Eye block

        Then I click on reference link on PCR Right Eye block
        #Validation2
        Then I should see the reference Page

        Then I click on reference link on PCR Left Eye block
        #Validation3
        Then I should see the reference Page



        Examples:
        |page  |username|password|site1   |firm                       |searchItem|specialty |event      |sectionName1       |sectionName2                |
        |master|admin   |admin   |Barking |Paul Godinho (Cataract)    |1009465   |Cataract  |Examination|Clinical Management|Cataract Surgical Management|


  @EXAM_Route_21 @sprint25
  Scenario Outline: Route 2:Login and create a new Examination Event
  Site:1 Queens
  Firm:2 Broom Glaucoma
  Clinical Management: Overall Management & Current Management

    Given I am on the OpenEyes "<page>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site1>"
    Then I select a firm of "<site2>"

    Then I search for hospital number "<searchItem>"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    Then I choose to expand the "<sectionName1>" section
    Then I choose to expand the "<sectionName2>" section

    Then I click on Right Eye PCR Risk

    Then I click on Left Eye PCR Risk

    Then I should have the default PCR values

    Examples:
      |page  |username|password|site1   |site2                     |searchItem|speciality|event      |sectionName1       |sectionName2                |
      |master|admin   |admin   |Barking |Paul Godinho (Cataract)   |1009465   |Cataract  |Examination|Clinical Management|Cataract Surgical Management|

