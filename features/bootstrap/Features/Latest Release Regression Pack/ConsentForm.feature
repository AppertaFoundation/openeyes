@consent @regression
Feature: Create New Consent Form
@Consent
         Regression coverage of this event is approx 70%

  Scenario Outline: Route 1: Login and create a new Consent Form
            Site 2: Kings
            Firm: Anderson Glaucoma
            Type 1 chosen
            2x Additional Procedures

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "<type>"

    Then I choose Procedure eye of "<eyeProcedure>"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "<commonProcedure>"

    Then I choose an additional procedure of "<addProc1>"
    Then I choose an additional procedure of "<addProc2>"

    Then I choose Permissions for images No

    And I select the Information leaflet checkbox

    Then I save the Consent Form and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event  |type|eyeProcedure|commonProcedure|addProc1|addProc2|
    |admin|admin|Barking        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Consent|1   |Both        |127            |41      |42      |


  Scenario Outline: Route 2: Login and create a new Consent Form
            Site 2: Kings
            Firm 2: Broom Glaucoma
            Type 2 chosen

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "<type>"

    Then I choose Procedure eye of "<eyeProcedure>"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "<commonProcedure>"

    Then I choose Permissions for images Yes

    Then I save the Consent Form and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event  |type|eyeProcedure|commonProcedure|
      |admin|admin|Barking        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Consent|2   |Left        |129            |


  Scenario Outline: Route 3: Login and create a new Consent Form
            Site 1: Queens
            Firm 4: Medical Retinal
            Type 3 chosen

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "<type>"

    Then I choose Procedure eye of "<eyeProcedure>"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "<commonProcedure>"

    Then I choose Permissions for images Yes

    Then I save the Consent Form and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number                |hospNumber|speciality    |event  |type|eyeProcedure|commonProcedure|
      |admin|admin|Barking        |Angela Glasby (Medical Retinal)|1009465   |Medical Retinal|Consent|3   |Left        |327            |


  Scenario Outline: Route 4: Login and create a new Consent Form
            Site 2: Kings
            Firm 3: Anderson Glaucoma
            Type 4 chosen

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "<type>"

    Then I choose Procedure eye of "<eyeProcedure>"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "<commonProcedure>"

    Then I choose Permissions for images Yes

    Then I save the Consent Form and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event  |type|eyeProcedure|commonProcedure|
  |admin|admin|Mile End       |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Consent|4   |Left        |129            |

  @Consent_Route5
  Scenario Outline: Route 5: Login and create a new Consent Form
            Site 2: Kings
            Firm: Anderson Glaucoma
            Validation error messages check

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    #Then I select Unbooked Procedures
    Then I select Add Consent Form

    Then I save the Consent Form Draft
    Then I confirm that the Consent Validation error messages have been displayed

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event  |
  |admin|admin|Mile End       |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Consent|


  @Consent_Route6A
  Scenario Outline: Route 6A: Login and create a Operation Booking for Route 6B test

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "<diag>"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<proc>"
          # Right Extracapsular cataract extraction

    Then I select No to Consultant required

    And I select No for Any other doctor to do

    And I select No for Does the patient require pre-op assessment by an anaesthetist

    And I select a Anaesthetic type "<anaestheticType>"

    And I select Patient preference for Anaesthetic choice

    And I select No for Patient needs to stop medication

    Then I select Yes to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Routine

   # And I select a decision date of "10"

    And I select Yes for Admission discussed with patient

    And I select As soon as possible for Schedule options

    Then I add comments of "<comments>"

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    #Then I select Next Month

    And I select an Available theatre slot date
    And I select an Available session time

    Then I add Session comments of "<sessionComments>"
    And I add Operation comments of "<opComments>"

    Then I confirm the operation slot

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event    |diagEye|diag    |opEye|proc|anaestheticType|opSite|comments|sessionComments|opComments|
      |admin|admin|Croydon        |Cataract firm (Cataract)|1009465   |Cataract  |OpBooking|Right  |24010005|Right|79  |LA            |Croydon     |Test    |Test           |Test      |


  @Consent_Route6B
  Scenario Outline: Route 6B: Login and create a new Consent Form choosing a previously created Operation Booking (from Route 6A)
            Site 2: Kings
            Firm: Anderson Glaucoma
            Type 1 chosen
            2x Additional Procedures

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select the previously created operation "<prevOp>"

    Then I select Add Consent Form

    Then I save the Consent Form and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event  |prevOp    |diagEye|opEye|
      |admin|admin|Croydon        |Cataract firm (Cataract)|1009465   |Cataract  |Consent|booking152|Right  |Right|




