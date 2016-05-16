@laser @regression
Feature: Create New Laser event
@LASER
         Regression coverage of this event is 100%

  Scenario Outline: Route 1: Login and create a Laser event
            Site 2:  Kings
            Firm 3:  Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select a Laser site ID "1"
    And I select a Laser of "2"
    And I select a Laser Operator of "96"
    Then I select a Right Procedure of "62"
    Then I select a Right Procedure of "177"
    Then I select a Left Procedure of "364"
    Then I select a Left Procedure of "128"

    And I remove the last added Procedure

    Then I save the Laser Event and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event|
    |admin|admin|Croydon        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Laser|


  @Laser_Route_2
  Scenario Outline: Route 2: Login and validate a Laser Event.
            Site 2:  Kings
            Firm 3:  Anderson Glaucoma
            Save without mandatory fields validation check

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events
    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I save the Laser Event

    And I Confirm that the Laser Validation error messages are displayed

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event|
      |admin|admin|Croydon        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Laser|

  @Laser_Route_3
  Scenario Outline: Route 3: Login and create a Laser event
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

    Then I select a Laser site ID "1"
    And I select a Laser of "2"
    And I select a Laser Operator of "96"

    Then I remove the right eye

    And I add the right eye

    Then I select a Right Procedure of "62"
    Then I select a Left Procedure of "364"

    Then I add expand the Comments section
    And I add "Test comments" into the Comments section
    Then I remove the Comments section

    Then I save the Laser Event and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event|
      |admin|admin|Croydon        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Laser|


