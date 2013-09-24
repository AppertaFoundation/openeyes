@examination @regression
Feature: Create New Examination Regression Tests
  Regression over 2 Sites and 4 Firms
  Coverage at TBC %

  Scenario: Login and create a new Examination Event Route 1: Site 1 Queens, Firm 3 Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"
    #Then I search for patient name last name "<last>" and first name "<first>"

#    Then I select Add First New Episode and Confirm
#   Then I select Create or View Episodes and Events
    Then I select the Latest Event
    #Then I expand the Cataract sidebar
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week
    #Hardcoded as actual data selected is not re-used on this form

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
    Then I choose left Dilation of "4" and drops of "5"
    Then I choose right Dilation of "6" and drops of "3"

    Then I choose to expand the Refraction section

    Then I enter left Refraction details of Sphere "1" integer "6" fraction "0.75"
    And I enter left cylinder details of of Cylinder "-1" integer "7" fraction "0.75"
    Then I enter left Axis degrees of "12"
    And I enter a left type of "5"

    Then I enter right Refraction details of Sphere "1" integer "3" fraction "0.50"
    And I enter right cylinder details of of Cylinder "-1" integer "4" fraction "0.25"
    Then I enter right Axis degrees of "34"
    And I enter a right type of "1"

    Then I Save the Examination

  Scenario: Login and create a new Examination Event Route 1: Site 2 Queens, Firm 2 Broom Glaucoma

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

  Scenario: Login and create a new Examination Event Route 1: Site 2 Queens, Firm 1 Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1                                                                                                                                                                                                                                                                                                       "

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






  # Disorder/Comorbidities
  # 1=Angina
  #"2=Asthma#
  #"3=Blood Loss#
  #"5=CVA#
  #"4=Cardiac Surgery#
  #"13=Ethnicity#
  #"6=FOH#
  #"7=Hyperopia#
  #"8=Hypotension#
  #"10=Migraine#
  #"9=Myopia#
  #"14=Other#
  #"11=Raynaud's#
  #"15=Refractive Surgery#
  #"12=SOB#

  # Snellen Metre
  #94=6/5
  #90=6/6
  #81=6/9
  #75=6/12
  #66=6/18
  #60=6/24
  #51=6/36
  #40=6/60
  #25=3/60
  #4=CF
  #3=HM
  #2=PL
  #1=NPL

  # Reasing Method
  #1=Unaided
  #2=Glasses
  #3=Contact lens
  #4=Pinhole
  #5=Auto-refraction
  #6=Formal refraction

  # Intraocular Pressure 1-40
  # 1=Goldmann
  # 2=Tono-pen
  # 3=I-care
  # 4=Perkins
  # 6=Dynamic Contour Tonometry
  # 5=Other

  # Dilation
  #1=Atropine 1%
  #2=Cyclopentolate 0.5%
  #3=Cyclopentolate 1%
  #4=Phenylephrine 2.5%
  #5=Tropicamide 0.5%
  #6=Tropicamide 1%

  # Refraction Options
  # 1=Auto-refraction
  # 2=Ophthalmologist
  # 3=Optometrist
  # 5=Own Glasses
  # ""=Other