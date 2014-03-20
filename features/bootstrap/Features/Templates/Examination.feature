
Feature: Create New Examination
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and create a new Examination Event Route 4: Site:1 Queens, Firm:1 Anderson Cataract

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
    And I choose to expand Cataract Management
    And I add Cataract Management Comments of "new glasses prescribed, "
    Then I select First Eye
    Then I select Second Eye
    Then I select First Eye
    And I choose City Road
    And I choose At Satellite
    And I choose Straightforward case
  #    Then I select a post operative refractive target in dioptres of "11.5"
    And the post operative target has been discussed with patient Yes
    And the post operative target has been discussed with patient No
    Then I select a suitable for surgeon of "3"
    And I tick the Supervised checkbox
    Then I select Previous Refractive Surgery Yes
    Then I select Previous Refractive Surgery No
    And I select Vitrectomised Eye Yes
    And I select Vitrectomised Eye No


    Then I choose to expand the Laser Management section
    And I choose a laser of "3"

  #    And I choose a laser of "2"
  #    Then I choose a deferral reason of "1"

    And I choose a left laser type of "1"
    And I choose a right laser type of "2"

    Then I choose to expand the Injection Management section

    And I select a Right Diagnosis of Macular retinal oedema
    Then I select Right Secondary of Venous retinal branch occlusion


    And I select a Left Diagnosis of Macular retinal oedema
    Then I select Left Secondary of Diabetic macular oedema


  #    And I tick the No Treatment checkbox
  #    Then I select a reason for No Treatment of "2"

#    And I select a Right Diagnosis of Choroidal Retinal Neovascularisation
#    Then I select Right Secondary to "267718000"
#
#    And I select a Left Diagnosis of Choroidal Retinal Neovascularisation
#    Then I select Left Secondary to "267718000"
#
#    Then I choose a Right CRT Increase <100 of Yes
#    Then I choose a Right CRT Increase <100 of No
#    Then I choose a Right CRT >=100 of Yes
#    Then I choose a Right CRT >=100 of No
#    Then I choose a Right Loss of 5 letters Yes
#    Then I choose a Right Loss of 5 letters No
#    Then I choose a Right Loss of 5 letters >5 Yes
#    Then I choose a Right Loss of 5 letters >5 No
#
#    Then I choose a Left CRT Increase <100 of Yes
#    Then I choose a Left CRT Increase <100 of No
#    Then I choose a Left CRT >=100 of Yes
#    Then I choose a Left CRT >=100 of No
#    Then I choose a Left Loss of 5 letters Yes
#    Then I choose a Left Loss of 5 letters No
#    Then I choose a Left Loss of 5 letters >5 Yes
#    Then I choose a Left Loss of 5 letters >5 No

    And I select a Right Diagnosis of Macular retinal oedema
    Then I select Right Secondary of Venous retinal branch occlusion

    Then I choose a Right Failed Laser of Yes
    Then I choose a Right Failed Laser of No
    Then I choose a Right Unsuitable Laser of Yes
    Then I choose a Right Unsuitable Laser of No
    Then I choose a Right Previous Ozurdex Yes
    Then I choose a Right Previous Ozurdex No


    And I select a Left Diagnosis of Macular retinal oedema
    Then I select Left Secondary of Diabetic macular oedema

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

  Scenario: Route 2: Login and create a new Examination Event
  Site 1:Queens
  Firm 3:Anderson Glaucoma
  DR Grading tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    Then I choose to expand the DR Grading section

    Then I select a Diabetes type of Mellitus Type one
    Then I select a Diabetes type of Mellitus Type two

    And I select a left Clinical Grading for Retinopathy of "2"
    And I select a left NSC Retinopathy of "2"
    And I select a left Retinopathy photocoagulation of Yes
    And I select a left Retinopathy photocoagulation of No
    And I select a left Clinical Grading for maculopathy of "2"
    And I select a left NSC maculopathy of "3"
    And I select a left Maculopathy photocoagulation of Yes
    And I select a left Maculopathy photocoagulation of No

    And I select a right Clinical Grading for Retinopathy of "2"
    And I select a right NSC Retinopathy of "2"
    And I select a right Retinopathy photocoagulation of Yes
    And I select a right Retinopathy photocoagulation of No
    And I select a right Clinical Grading for maculopathy of "2"
    And I select a right NSC maculopathy of "4"
    And I select a right Maculopathy photocoagulation of Yes
    And I select a right Maculopathy photocoagulation of No

    Then I Save the Examination and confirm it has been created successfully


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

#   Adnexal Comborbidity options: (must include trailing space)
#  "blepharitis, "
#  "blepharochalasis, "
#  "blepharospasm, "
#  "conjunctivitis, "
#  "crusting of lashes, "
#  "difficult access, "
#  "discharge, "
#  "dry eyes, "
#  "ectropion, "
#  "entropion, "
#  "injected lid margins, "
#  "lower lid ectropion, "
#  "none, "
#  "poor tear film, "
#  "punctal ectropian, "
#  "squint, "

#  Pupillary Abnormalities

#  1>Normal
#  2>RAPD
#  3>Holmes-Adie
#  4>Argyll Robertson
#  5>APD

#  Diagnoses
#  24010005>Aphakia
#  193570009>Cataract
#  79410001>Congenital cataract
#  193576003>Cortical cataract
#  230670003>Familial infantile myasthenia
#  267626000>Hypermature cataract
#  53889007>Nuclear cataract
#  34533008>Posterior subcapsular polar cataract
#  95217000">Pseudophakia
#  38583007>Toxic cataract
#  34361001>Traumatic cataract

#   Investigations

#  "field test, "
#  "Fluorescein angiography, "
#  "OCT, "
#  "refraction, "
#  "ultrasound, "

#  Cataract Management Comments

#  "discharged and glasses not required, "
#  "discharged with prescription for glasses, "
#  "listed for left cataract surgery under LA, "
#  "new glasses prescribed, "
#  "patient managing well and not keen for surgery, "

#Suitable for Surgeon

#  5">Consultant
#  "1">Senior Surgeon
#  2">Fellow
#  3">SpR
#  4">SHO

#Laser Status

#    1 Not Required
#    2 Deferred
#    3 Booked for a future date
#    4 Performed today

#Laser Type

#  0" value="1">Focal
#  0" value="2">Grid
#  0" value="3">Macular (focal/grid)
#  0" value="4">PRP
#  0" value="5">PRP & macular
#  1" value="6">Other

#No Treatment Reason
#
#  0" value="1">DNA
#  0" value="2">Infection
#  0" value="3">CVA
#  0" value="4">MI
#  0" value="5">Spontaneous improvement
#  1" value="6">Other

#Secondary To
#
#  267718000">Age related macular degeneration
#  57190000">Myopia
#  312950007">Punctate inner choroidopathy
#  240740001">Ocular histoplasmosis syndrome
#  314269007">Idiopathic choroidal neovascular membrane
#  86103006">Angioid streaks of choroid
#  255024002">Naevus of choroid
#  255025001">Osteoma of choroid
#  2532009">Choroidal rupture
#  31541009">Sarcoidosis
#  414783007">Multifocal choroiditis
#  416589006">Toxoplasma retinitis
#  312956001">Central serous chorioretinopathy
#  276436007">Hereditary macular dystrophy

#Conclusion Option
#
#  "booked for first eye, "
#  "booked for second eye, "
#  "booked for surgery, "
#  "Cataract: Corneal disease limiting postoperative outcome, "
#  "Cataract: Early cataract, not for surgery at present, "
#  "Cataract: Glaucoma disease limiting postoperative outcome, "
#  "Cataract: Macular disease limiting postoperative outcome, "
#  "Cataract: No significant cataract, surgery not required, "
#  "copy of clinical details provided for Optician, "
#  "discharge, "
#  "discharge, to be reviewed as necessary via A&E or the GP, "
#  "glasses prescribed, "
#  "good outcome from surgery listed for second eye, "
#  "offer of surgery declined by patient, "
#  "Optician: Review refraction with own optician, "
#  "personal information leaflet provided to patient, "
#  "removal of suture is done, "
#  "satisfactory post operative progress, "
#  "wean off topical medication, "
#  "YAG laser capsulotomy was performed with no complications, "