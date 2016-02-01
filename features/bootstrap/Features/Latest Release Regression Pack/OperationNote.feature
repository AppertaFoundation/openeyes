@opnote @regression
Feature: Create New Operation Note Event
@OperationNote
  Regression coverage of this event is approx TBC%

  @ON_ROUTE_1
  Scenario Outline: Route 1: Login and create a Operation Note Event
            Site 2: Kings
            Firm 3: Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Right Eye

    And I select a common Procedure of "<commonProcedure>"

    Then I choose Anaesthetic Type of Topical

    And I choose Given by Anaesthetist

    Then I choose Delivery by Retrobulbar

    Then I choose an Anaesthetic Agent of "<ASAgent>"

    Then I choose a Complication of "<complication>"

    And I add Anaesthetic comments of "<ASComments>"

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
    |admin|admin|2              |3              |1009465   |glaucoma  |OpNote|41             |1      |1           |Test Comments|2      |3                 |3        |1      |Test Comments|1                 |

  @ON_ROUTE_2
  Scenario Outline: Route 2: Login and create a Operation Note Event
  Site 2: Kings
  Firm 4: Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Left Eye

    And I select a common Procedure of "<commonProcedure>"

    Then I choose Anaesthetic Type of LA

    And I choose Given by Surgeon

    Then I choose Delivery by Peribulbar

    Then I choose an Anaesthetic Agent of "<ASAgent>"

    Then I choose a Complication of "<complication>"

    And I add Anaesthetic comments of "<ASComments>"

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality    |event |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|2              |4              |1009465   |medicalRetinal|OpNote|321            |1      |6           |Test Comments|3      |2                 |2        |1      |Test Comments|1                 |



  @ON_ROUTE_3
  Scenario Outline: Route 3: Login and create a Operation Note Event
  Site 1: Queens
  Firm 3: Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Right Eye

    And I select "Anterior vitrectomy" for "Add a procedure"

    Then I choose Anaesthetic Type of LAC

    And I choose Given by Nurse

    Then I choose Delivery by Subtenons

    Then I choose an Anaesthetic Agent of "<ASAgent>"

    Then I choose a Complication of "<complication>"

    And I add Anaesthetic comments of "<ASComments>"

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event |ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|1              |3              |1009465   |glaucoma  |OpNote|1      |9           |Test Comments|3      |3                 |3        |1      |Test Comments|1                 |

  @ON_ROUTE_4
  Scenario Outline: Route 4: Login and create a Operation Note Event
  Site 1: Queens
  Firm 2: Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Right Eye

    And I select a common Procedure of "<commonProcedure>"

    Then I choose Anaesthetic Type of LAS

    And I choose Given by Anaesthetist Tehnician

    Then I choose Delivery by Subconjunctival

    Then I choose an Anaesthetic Agent of "<ASAgent>"

    Then I choose a Complication of "<complication>"

    And I add Anaesthetic comments of "<ASComments>"

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|1              |2              |1009465   |glaucoma  |OpNote|41             |1      |4           |Test Comments|3      |2                 |3        |1      |Test Comments|1                 |



  @ON_ROUTE_5
  Scenario Outline: Route 5: Login and create a Operation Note Event
  Site 1: Queens
  Firm 4: Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Left Eye

    And I select a common Procedure of "<commonProcedure>"

    Then I choose Anaesthetic Type of LA

    And I choose Given by Other

    Then I choose Delivery by Topical
    Then I choose Delivery by Topical & Intracameral
    Then I choose Delivery by Other

    Then I choose an Anaesthetic Agent of "<ASAgent>"

    Then I choose a Complication of "<complication>"

    And I add Anaesthetic comments of "<ASComments>"

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality    |event |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|1              |4              |1009465   |medicalRetinal|OpNote|41             |1      |5           |Test Comments|3      |2                 |3        |1      |Test Comments|1                 |

  @ON_ROUTE_6
  Scenario Outline: Route 6: Login and create a Operation Note Event
  Site 1: Queens
  Firm 4: Medical Retinal
  Anaesthetic Type: GA

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Left Eye

    And I select a common Procedure of "<commonProcedure>"

    Then I choose Anaesthetic Type of GA

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality    |event |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
      |admin|admin|1              |4              |1009465   |medicalRetinal|OpNote|41             |1      |5           |Test Comments|3      |2                 |3        |1      |Test Comments|1                 |


  @sprint22
  Scenario: Patient has no risks
    Given I search for hospital number "<Hospital-number>"
      |<Hospital-number>|
      |1009465|
    And I click on edit button in risks section
    When I select that patient has no risks
    And I click on save button
    Then I should see the no risks information at risks section

  Scenario: Add a risk to a patient
    Given I search for hospital number "<Hospital-number>"
      |<Hospital-number>|
      |1009465          |
    And I click on edit button in risks section
    When I select that patient has risks
    And I select "<risk>"
    And I add "<comment>"

    And I click save button
    Then I should the "<risk>" and "<comment>" in risks section
    And I should see the risks warning icon on the top of the patient summary screen
      |<risk>         |<comment>|
      |cannot lie flat|         |

  Scenario: Add and remove risk to a patient
    Given I search for patient name last name "<lastname>" and first name "<firstname>"
      |lastname|firstname|
      |violet  |coffin   |
    And I click on edit button in risks section
    When I select that patient has risks
    And I select "<risk>"
    And I add "<comment>"
      |<risk>|<comment>|
      |CJD Risk|       |
    And I click save button
    And I remove the risk added
    Then I should see that the patient risks are unknown message on risks section

  Scenario: Add more than one risks to patient
    Given I search for NHS number "<nhs>"
      |nhs|
    And I click on edit button in risks section
    When I select that patient has risks
    And I select "<risk1>","<risk2>"
    And I add "<comment1>","<comment2>"
    And I click save button
    Then I should see Patient has risks with "<risk1>","<risk2>" mentioned in the top of the patient summary page
      |<risk1>|<risk2>|<comment1>|<comment2>
      |cannot lie flat|CJD Risk|     |     |


    @OE-5649
    Scenario Outline: To check that cataract complications are mandatory
      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<site>"
      Then I select a firm of "<firm>"

      Then I search for hospital number "<hospitalNumber>"

      Then I select Create or View Episodes and Events

      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select an Emergency Operation Note
      And I select Create Operation Note

      Then I select Procedure Left Eye
      And I select a common Procedure of "<procedureId>"
      #procedure id are listed below in the comments

      Then I click save button
      Then I should see cataract complications empty error message
      Then I should see anaesthetic complications empty error message
      Examples:
        |uname|pwd  |site|firm|hospitalNumber|speciality|event|procedureId    |
        |admin|admin|1   |1   |1009465       |cataract|OpNote|Insertion of IOL|

    #procedure id 79,323,173,308,46,45,322 contains doodles and other PCR, whereas other ids contain only comments box.

    #<select name="select_procedure_id_procs" id="select_procedure_id_procs">

    #<option value="48">Anterior capsulotomy</option>
    #<option value="47">Capsulectomy</option>#
    #<option value="61">Capsulotomy (surgical)</option>
    #<option value="62">Capsulotomy (YAG)</option>
    #<option value="73">Corneal suture adjustment</option>
    #<option value="79">Extracapsular cataract extraction</option>
    #<option value="323">Extracapsular cataract extraction and insertion of IOL</option>
    #<option value="324">Injection into anterior chamber</option>
    #<option value="173">Insertion of IOL</option>
    #<option value="50">Irrigation of anterior chamber</option>
    #<option value="340">Limbal relaxing incision </option>
    #<option value="175">Peripheral iridectomy</option>
    #<option value="42">Phakoemulsification</option>
    #<option value="308">Phakoemulsification and IOL</option>
    #<option value="40">Removal of corneal suture</option>
    #<option value="46">Removal of IOL</option>
    #<option value="52">Repair of prolapsed iris</option>
    #<option value="45">Repositioning of IOL</option>
    #<option value="322">Revision of IOL</option>
    #<option value="53">Surgical iridotomy</option>
    #<option value="38">Suture of cornea</option>


  @OE-5379 @admin @sprint18
    Scenario Outline: To add,edit,delete anaesthetic agents via admin
    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospitalNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note
    And I select Create Operation Note

    Then I sele


    Examples:
      |uname|pwd  |site|firm|hospitalNumber|speciality|event|
      |admin|admin|1   |1   |1009465       |cataract|OpNote|


    @OE-5742 @sprint25
    Scenario Outline: To check that cataract complications are mandatory
      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<site>"
      Then I select a firm of "<firm>"

      Then I search for hospital number "<hospitalNumber>"

      Then I select Create or View Episodes and Events

      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I select an Emergency Operation Note
      And I select Create Operation Note

      Then I select procedure "<side>" eye
      And I select a common Procedure of "<procedureId>"

      Then I select PCR Risk
      Then I select "<glaucomaOption>" Glaucoma
      And I select "<diabeticOption>" Diabetic
      And I select "<fundalOption>" fundalview/vitreousOpacities
      And I select "<cataractOption>" Brunescent/whiteCataract
      And I select "<surgeonOption>" Surgeon
      And I select "<PXFOption>" PXF
      And I select "<pupilSize>" pupil size
      And I select "<axialLength>" axial length
      And I select "<lieOption>" Can lie flat

      Then I check the PCR Risk is "<PCRValue>"




      Examples:
        |uname|pwd  |site|firm|hospitalNumber|speciality|event|procedureId    |side|glaucomaOption|diabeticOption|fundalOption|cataractOption|surgeonOption|PXFOption|pupilSize|axialLength|lieOption|PCRValue|
        |admin|admin|15  |5   |1009465       |cataract|OpNote|Insertion of IOL|left| | | | | | | | | | |

     #procedure ids 79,323,173,308,46,45,322 contains doodles and other PCR, whereas other ids contain only comments box.

    #<select name="select_procedure_id_procs" id="select_procedure_id_procs">

    #<option value="48">Anterior capsulotomy</option>
    #<option value="47">Capsulectomy</option>#
    #<option value="61">Capsulotomy (surgical)</option>
    #<option value="62">Capsulotomy (YAG)</option>
    #<option value="73">Corneal suture adjustment</option>
    #<option value="79">Extracapsular cataract extraction</option>
    #<option value="323">Extracapsular cataract extraction and insertion of IOL</option>
    #<option value="324">Injection into anterior chamber</option>
    #<option value="173">Insertion of IOL</option>
    #<option value="50">Irrigation of anterior chamber</option>
    #<option value="340">Limbal relaxing incision </option>
    #<option value="175">Peripheral iridectomy</option>
    #<option value="42">Phakoemulsification</option>
    #<option value="308">Phakoemulsification and IOL</option>
    #<option value="40">Removal of corneal suture</option>
    #<option value="46">Removal of IOL</option>
    #<option value="52">Repair of prolapsed iris</option>
    #<option value="45">Repositioning of IOL</option>
    #<option value="322">Revision of IOL</option>
    #<option value="53">Surgical iridotomy</option>
    #<option value="38">Suture of cornea</option>


  @OE-5695 @sprint25 @testtt
  Scenario Outline: To check that cataract complications are mandatory
    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospitalNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select an Emergency Operation Note
    And I select Create Operation Note

    Then I select procedure "<side>" eye
    And I select a common Procedure of "<procedureId>"

    Then I select PCR Risk

    #Validation1
    Then I should see PCR reference

    Then I click on reference link on PCR block
    #Validation2
    Then I should see the reference Page

    Examples:
      |uname|pwd  |site|firm|hospitalNumber|speciality|event|procedureId    |side|
      |admin|admin|15  |5   |1009465       |cataract|OpNote|Insertion of IOL|left|