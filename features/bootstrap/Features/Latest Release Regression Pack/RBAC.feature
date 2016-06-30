@rbac @regression
Feature: Open Eyes Login RBAC user levels
@RBAC

  @RBAC_Route
  Scenario Outline: PREPARATION LASER EVENT: Login and create a Phasing Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    #Then I choose a right eye Intraocular Pressure Instrument  of "1"

    And I choose right eye Dilation of Yes

    Then I choose a right eye Intraocular Pressure Reading Time of "<rightIPRTime>"
    Then I choose a right eye Intraocular Pressure Reading of "<rightIPR>"
    And I add right eye comments of "<rightComm>"

    #Then I choose a left eye Intraocular Pressure Instrument  of "5"

    And I choose left eye Dilation of Yes

    Then I choose a left eye Intraocular Pressure Reading Time of "<leftIPRTime>"
    Then I choose a left eye Intraocular Pressure Reading of "<leftIPR>"
    And I add left eye comments of "<leftComm>"

    Then I Save the Phasing Event and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|speciality|event   |rightIPRTime|rightIPR|rightComm|leftIPRTime|leftIPR|leftComm|
  |admin|admin|1              |1              |1009465   |Cataract  |cataract|14:10       |5       |TEST     |14:42      |7      |TEST    |


  Scenario Outline: Route 0: Level 0 RBAC access: User with no login rights

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I confirm that an Invalid Login error message is displayed

    Examples:
    |uname |pwd     |
    |level0|password|

  Scenario Outline: Route 1: Level 1 RBAC access: Login access and only able to view patient demographics

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then a check is made to confirm that Patient details information is displayed
    Then a check is made to confirm the user has correct level one access

    Examples:
      |uname |pwd     |siteName/Number|firmName/Number|hospNumber|
      |level1|password|1              |1              |1009465   |




  Scenario Outline: Route 2: Level 2 RBAC access: Login access and view only, no printing

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then a check is made to confirm that Patient details information is displayed

    Then a check is made to confirm the user has correct level two access

    Then I select Create or View Episodes and Events

    Then additional checks are made for correct level two access

    And a check to see printing has been disabled

  Examples:
  |uname |pwd     |siteName/Number|firmName/Number|hospNumber|
  |level2|password|1              |1              |1009465   |




  Scenario Outline: Route 3: Level 3 RBAC access: Login access,view only rights and Printing Events

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then a check is made to confirm that Patient details information is displayed

    Then a check is made to confirm the user has correct level two access

    Then I select Create or View Episodes and Events

    Then additional checks are made for correct level two access

    And a check to see printing has been enabled

  Examples:
  |uname |pwd     |siteName/Number|firmName/Number|hospNumber|
  |level3|password|1              |1              |1009465   |




  Scenario Outline: Route 4 (Prep): Login and create a new Prescription
  Site 1:Queens
  Firm 3:Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a Common Drug "<commonDrug>"
    And I select a Standard Set of "<standardSet>"

    Then I enter a Dose of "<dose>" drops
    And I enter a route of "<route>"

    And I enter a frequency of "<freq>"
    Then I enter a duration of "<duration>"
    Then I enter a eyes option "<eyesOption>"

    Then I enter a item two eyes option of "<eyesOption-2>"
    Then I enter a item three eyes option of "<eyesOption-3>"

    And I add Prescription comments of "<presComm>"

    Then I Save the Prescription Draft and confirm it has been created successfully

    Examples:
      |uname |pwd     |siteName/Number|firmName/Number|hospNumber|speciality|event       |commonDrug|standardSet|dose|route|freq|duration|eyesOption|eyesOption-2|eyesOption-3|presComm|
      |level3|password|1              |1              |1009465   |Glaucoma  |Prescription|75        |10         |2   |1    |4   |1       |1         |1           |1           |TEST    |




  Scenario Outline: Route 5: Level 4 RBAC access: Login access, edit rights, Prescription event blocked

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then a check is made to confirm that Patient details information is displayed

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar

    Then a check is made to confirm the user has correct level four access

  Examples:
  |uname |pwd     |siteName/Number|firmName/Number|hospNumber|speciality|
  |level4|password|1              |3              |1009465   |Glaucoma  |



    #level 4 Prescription event disabled

#  Scenario: Route 5: Level 5 RBAC access: Login access, edit rights, Prescription event allowed
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "level4" and "password"
#    And I select Site "1"
#    Then I select a firm of "3"
#    Then I search for hospital number "1009465"
#
#    Then a check is made to confirm that Patient details information is displayed
#
#    Then I select the Latest Event


#  Scenario: Route 6: Level 6 RBAC access: TBC
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "level6" and "password"
#    And I select Site "1"
#    Then I select a firm of "3"
#    Then I search for hospital number "1009465"
#
#    Then a check is made to confirm that Patient details information is displayed
#
#    Then I select the Latest Event


