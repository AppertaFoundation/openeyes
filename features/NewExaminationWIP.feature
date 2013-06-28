@NewExamWIP
Feature: Create New Examination

  Scenario Outline: Login and create a new Examination Event

    Given I am on the OpenEyes "<environment>" homepage
    And I select Site "<site>"
    And I enter login credentials "<username>" and "<password>"

    Then I select a firm of "18"

    #Then I search for hospital number "<hospnumber>"
    Then I search for patient name last name "<last>" and first name "<first>"
    #Then I search for NHS number "<nhs>"

    Then I select Create or View Episodes and Events
    And I add a New Event "<EventType>"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week
    #Hardcoded as actual data selected is not re-used on this form

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "4"

    Then I choose to expand the Visual Acuity section
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

    #WIP - These Optional sections are to be coded (Dependant on changing firms)
    Then I choose to expand the Gonioscopy section
    Then I choose to expand the Adnexal Comorbidity section
    Then I choose to expand the Anterior Segment section
    Then I choose to expand the Pupillary Abnormalities section
    Then I choose to expand the Optic Disc section
    Then I choose to expand the Posterior Pole section
    Then I choose to expand the Diagnoses section
    Then I choose to expand the Investigation section
    Then I choose to expand the Clinical Management section
    Then I choose to expand the Risks section
    Then I choose to expand the Clinic Outcome section
    Then I choose to expand the Conclusion section

    #Then I Save the Examination
    #Then I Cancel the Examination

    #Then I choose to close the browser

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Examination   |


  # Site ID's:
  # City Road - 1
  # Last name to include a comma after to match search criteria i.e Coffin,
  # Anaesthetist - non = Non-Consultant, no = No Consultant

 # Firm 18 = Allan Bruce (Cataract)

  # Event Type
  # Consent = Consent Form

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