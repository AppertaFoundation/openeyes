@operationbooking @regression
Feature: Create New Operation Booking Event
@OperationBooking
         Regression coverage of this event is approx 50%

  @OB_Route_1
  Scenario Outline: Route 1: Login and create a Operation Booking Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    #Then I search for hospital number "1009465"
    #Given I am logged in as "admin" with site "Kings" and firm "Anderson Firm (Glaucoma)"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required
    And I select No for Any other doctor to do

    And I select No for Does the patient require pre-op assessment by an anaesthetist

    And I select a Anaesthetic type Topical

    And I select Patient preference for Anaesthetic choice

    And I select No for Patient needs to stop medication

    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    And I select Yes for Admission discussed with patient

    And I select As soon as possible for Schedule options

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    And I select an Available theatre slot date
    And I select an Available session time

#    Then I select a Ward of "2"
    And enter an admission time of "<admTime>"
    Then I add Session comments of "<sessionComments>"
    And I add Operation comments of "<opComments>"
    And enter RTT comments of "<RRTComments>"

    Then I confirm the operation slot

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event            |diagEye|opEye|procedure|opSite |admTime|sessionComments                  |opComments                           |RRTComments              |
    |admin|admin|Croydon        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Operation booking|Left   |Left |41       |Croydon|11:20  |Session Comments Session Comments|Operation Comments Operation Comments|RTT Comments RTT Comments|

  @OB_Route_2
  Scenario Outline: Route 2: Login and create a Operation Booking Anderson Cataract

    #Given I am logged in as "admin" with site "Queens" and firm "Anderson Firm (Cataract)"

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "24010005"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select Yes to Consultant required
    And I select "Godinho Paul" for "Named Consultant"

    And I select No for Any other doctor to do

    And I select No for Does the patient require pre-op assessment by an anaesthetist

    And I select a Anaesthetic type Topical

    And I select Patient preference for Anaesthetic choice

    And I select No for Patient needs to stop medication

    Then I select Yes to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Routine

    Then I add comments of "<comments>"

    And I select Yes for Admission discussed with patient

    And I select As soon as possible for Schedule options

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    And I select an Available theatre slot date
    And I select an Available session time

    Then I add Session comments of "<sessionComments>"
    And I add Operation comments of "<opComments>"

    Then I confirm the operation slot

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event            |diagEye|opEye|procedure|opSite  |comments |sessionComments                  |opComments                           |
  |admin|admin|Croydon        |Paul Godinho (Cataract)|1009465   |Cataract  |Operation booking|Right  |Right|79       |Croydon |Test Test|Session Comments Session Comments|Operation Comments Operation Comments|

  @OB_Route_3
  Scenario Outline: Route 3: Login and create a Operation Booking Anderson Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Diagnosis Eyes of "<diagEye>"
    And I select a Diagnosis of "255024002"
    Then I select Operation Eyes of "<opEye>"
    And I select a Procedure of "<procedure>"

    Then I select No to Consultant required
    And I select No for Any other doctor to do

    And I select No for Does the patient require pre-op assessment by an anaesthetist

    And I select a Anaesthetic type Topical

    And I select Patient preference for Anaesthetic choice

    And I select No for Patient needs to stop medication

    Then I select No to a Post Operative Stay

    And I select a Operation Site of "<opSite>"

    Then I select a Priority of Urgent

    Then I add comments of "<comments>"

    And I select Yes for Admission discussed with patient

    And I select As soon as possible for Schedule options

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    And I select an Available theatre slot date

    And I select an Available session time
    
    Then I add Session comments of "<sessionComments>"
    And I add Operation comments of "<opComments>"

    Then I confirm the operation slot

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number                |hospNumber|speciality     |event            |diagEye|opEye|procedure|opSite  |comments |sessionComments                  |opComments                           |
  |admin|admin|Croydon        |Angela Glasby (Medical Retinal)|1009465   |Medical Retinal|Operation booking|Both   |Both |327      |Croydon |Test Test|Session Comments Session Comments|Operation Comments Operation Comments|
