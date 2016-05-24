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
    |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event         |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
    |admin|admin|Ludwig|A Dulku (Glaucoma)      |1009465   |Glaucoma  |Operation Note|41             |1      |1           |Test Comments|2      |3                 |3        |1      |Test Comments|1                 |

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
  |uname|pwd  |siteName/Number|firmName/Number             |hospNumber|speciality     |event         |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|Barking        |Admin User (Medical Retinal)|1009465   |Medical Retinal|Operation Note|321            |1      |6           |Test Comments|3      |2                 |2        |1      |Test Comments|1                 |



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
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event         |ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|Ludwig|A Dulku (Glaucoma)      |1009465   |Glaucoma  |Operation Note|1      |9           |Test Comments|3      |3                 |3        |1      |Test Comments|1                 |

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
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event         |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|Ludwig|A Dulku (Glaucoma)      |1009465   |Glaucoma  |Operation Note|41             |1      |4           |Test Comments|3      |2                 |3        |1      |Test Comments|1                 |



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
  |uname|pwd  |siteName/Number|firmName/Number             |hospNumber|speciality     |event         |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
  |admin|admin|Barking        |Admin User (Medical Retinal)|1009465   |Medical Retinal|Operation Note|41             |1      |5           |Test Comments|3      |2                 |3        |1      |Test Comments|1                 |

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
      |uname|pwd  |siteName/Number|firmName/Number             |hospNumber|speciality     |event         |commonProcedure|ASAgent|complication|ASComments   |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
      |admin|admin|Barking        |Admin User (Medical Retinal)|1009465   |Medical Retinal|Operation Note|41             |1      |5           |Test Comments|3      |2                 |3        |1      |Test Comments|1                 |






    @ON_ROUTE_7
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
        |uname|pwd  |site           |firm                    |hospitalNumber|speciality|event         |procedureId    |
        |admin|admin|Croydon        |Cataract firm (Cataract)|1009465       |Cataract  |Operation Note|173            |

  @ON_ROUTE_8
  Scenario Outline: To check that PCR Risk saves
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
    Then I select a cataract complication of "None"
    Then I select an iol type of "SN60WF"
    Then I set an iol power of "1.34"
    Then I set predicted refraction of "-2.00"

    Then I select PCR Risk
    Then I set the "Left" OpNote PCR option "Glaucoma" to be "No Glaucoma"
    Then I set the "Left" OpNote PCR option "PXF" to be "No"
    Then I set the "Left" OpNote PCR option "Diabetic" to be "No Diabetes"
    Then I set the "Left" OpNote PCR option "Pupil" to be "Small"
    Then I set the "Left" OpNote PCR option "Fundal" to be "No"
    Then I set the "Left" OpNote PCR option "Axial" to be "< 26"
    Then I set the "Left" OpNote PCR option "Cataract" to be "Yes"
    Then I set the "Left" OpNote PCR option "ARB" to be "No"
    Then I set the "Left" OpNote PCR option "Doctor" to be "Associate specialist"
    Then I set the "Left" OpNote PCR option "Lie" to be "No"

    Then I select a anaesthetic complication of "None"
    Then I choose Anaesthetic Type of GA

    Then I choose a Surgeon of "<surgeon>"
    And I choose a Supervising Surgeon of "<supervisingSurgeon>"
    Then I choose an Assistant of "<assistant>"

    Then I choose Per Operative Drugs of "<opDrugs>"

    And I choose Operation comments of "<opComments>"

    Then I choose Post Op instructions of "<postOpInstructions>"

    Then I save the Operation Note and confirm it has been created successfully

    Examples:
      |uname|pwd  |site           |firm                    |hospitalNumber|speciality|event         |procedureId    |surgeon|supervisingSurgeon|assistant|opDrugs|opComments   |postOpInstructions|
      |admin|admin|Croydon        |Cataract firm (Cataract)|1009465       |Cataract  |Operation Note|173            |3      |2                 |3        |1      |Test Comments|1                 |

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
