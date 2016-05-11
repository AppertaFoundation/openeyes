  @phasing @regression
  Feature: Create New Phasing Event
  @Phasing
           Regression coverage of this event is 100%

  @Phasing_Route_1
  Scenario Outline: Route 1: Login and create a Phasing Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmNmae/Number>"

    Then I search for hospital number "<hospNumber> "

    Then I select Create or View Episodes and Events

    Then I expand the Glaucoma sidebar
    And I add a New Event "<event>"

    Then I choose a right eye Intraocular Pressure Instrument  of "1"

    And I choose right eye Dilation of Yes

    Then I choose a right eye Intraocular Pressure Reading Time of "<rightEyeIPRTime>"
    Then I choose a right eye Intraocular Pressure Reading of "<rightEyeIPR>"
    And I add right eye comments of "<rightEyeComm>"

    Then I choose a left eye Intraocular Pressure Instrument  of "5"

    And I choose left eye Dilation of Yes

    Then I choose a left eye Intraocular Pressure Reading Time of "<leftEyeIPRTime>"
    Then I choose a left eye Intraocular Pressure Reading of "<leftEyeIPR>"
    And I add left eye comments of "<leftEyeComm>"

    Then I Save the Phasing Event and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmNmae/Number|hospNumber|event  |rightEyeIPRTime|rightEyeIPR|rightEyeComm|leftEyeIPRTime|leftEyeIPR|leftEyeComm|
    |admin|admin|1              |3              |1009465   |Phasing|14:00          |5          |R TEST      |14:42         |7          |L TEST    |


    @Phasing_Route_2
    Scenario Outline: Route 2: Login and create a Phasing Event

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmNmae/Number>"

      Then I search for hospital number "<hospNumber> "

      Then I select Create or View Episodes and Events

      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I choose a right eye Intraocular Pressure Instrument  of "3"

      And I choose right eye Dilation of No

      Then I choose a right eye Intraocular Pressure Reading Time of "<rightEyeIPRTime>"
      Then I choose a right eye Intraocular Pressure Reading of "<rightEyeIPR>"
      And I add right eye comments of "<rightEyeComm>"

      Then I choose a left eye Intraocular Pressure Instrument  of "4"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "<leftEyeIPRTime>"
      Then I choose a left eye Intraocular Pressure Reading of "<leftEyeIPR>"
      And I add left eye comments of "<leftEyeComm>"

      Then I Save the Phasing Event and confirm it has been created successfully

    Examples:
    |uname|pwd  |siteName/Number|firmNmae/Number|hospNumber|speciality|event  |rightEyeIPRTime|rightEyeIPR|rightEyeComm|leftEyeIPRTime|leftEyeIPR|leftEyeComm|
    |admin|admin|1              |1              |1009465   |Cataract  |Phasing|21:00          |14         |R TEST      |04:42         |12         |L TEST     |

    @Phasing_Route_3
    Scenario Outline: Route 3: Login and create a Phasing Event

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmNmae/Number>"

      Then I search for hospital number "<hospNumber> "

      Then I select Create or View Episodes and Events

      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I choose a right eye Intraocular Pressure Instrument  of "3"

      And I choose right eye Dilation of No

      Then I choose a right eye Intraocular Pressure Reading Time of "<rightEyeIPRTime>"
      Then I choose a right eye Intraocular Pressure Reading of "<rightEyeIPR>"
      And I add right eye comments of "<rightEyeComm>"

      Then I choose a left eye Intraocular Pressure Instrument  of "3"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "<leftEyeIPRTime>"
      Then I choose a left eye Intraocular Pressure Reading of "<leftEyeIPR>"
      And I add left eye comments of "<leftEyeComm>"

      Then I Save the Phasing Event and confirm it has been created successfully

      Examples:
        |uname|pwd  |siteName/Number|firmNmae/Number|hospNumber|speciality|event  |rightEyeIPRTime|rightEyeIPR|rightEyeComm|leftEyeIPRTime|leftEyeIPR|leftEyeComm|
        |admin|admin|2              |3              |1009465   |Glaucoma  |Phasing|08:00          |5          |R TEST      |14:42         |9          |L TEST    |

    @Phasing_Route_4
    Scenario Outline: Route 4: Login and create a Phasing Event

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmNmae/Number>"

      Then I search for hospital number "<hospNumber>"

      Then I select Create or View Episodes and Events

      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I choose a right eye Intraocular Pressure Instrument  of "4"

      And I choose right eye Dilation of No

      Then I choose a right eye Intraocular Pressure Reading Time of "<rightEyeIPRTime>"
      Then I choose a right eye Intraocular Pressure Reading of "<rightEyeIPR>"
      And I add right eye comments of "<rightEyeComm>"

      Then I choose a left eye Intraocular Pressure Instrument  of "1"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "<leftEyeIPRTime>"
      Then I choose a left eye Intraocular Pressure Reading of "<leftEyeIPR>"
      And I add left eye comments of "<leftEyeComm>"

      Then I add a new Left Reading
      Then I choose a second left eye Intraocular Pressure Reading Time of "<leftEyeIPRTime-2>"
      Then I choose a second left eye Intraocular Pressure Reading of "<leftEyeIPR-2>"

      Then I add a new Right Reading
#      Then I choose a second right eye Intraocular Pressure Reading Time of "15:43"
      Then I choose a second right eye Intraocular Pressure Reading of "<rightEyeIPR-2>"

      Then I remove the last Right Reading
      Then I remove the last Left Reading

      Then I Save the Phasing Event and confirm it has been created successfully

      Examples:
        |uname|pwd  |siteName/Number|firmNmae/Number|hospNumber|speciality     |event  |rightEyeIPRTime|rightEyeIPR|rightEyeComm|leftEyeIPRTime|leftEyeIPR|leftEyeComm|leftEyeIPRTime-2|leftEyeIPR-2|rightEyeIPR-2|
        |admin|admin|1              |4              |1009465   |Medical Retinal|Phasing|08:00          |5          |R TEST      |14:89         |9         |L TEST     |11:07           |6           |20           |

    @Phasing_Route_5
    Scenario Outline: Route 5: Login and create a Phasing Event
              Invalid time entry validation tests (Intraocular Pressing Reading Times)

      Given I am on the OpenEyes "master" homepage
      And I enter login credentials "<uname>" and "<pwd>"
      And I select Site "<siteName/Number>"
      Then I select a firm of "<firmNmae/Number>"

      Then I search for hospital number "<hospNumber>"

      Then I select Create or View Episodes and Events

      Then I expand the "<speciality>" sidebar
      And I add a New Event "<event>"

      Then I choose a right eye Intraocular Pressure Instrument  of "1"

      And I choose right eye Dilation of Yes

      Then I choose a right eye Intraocular Pressure Reading Time of "<rightEyeIPRTime>"
      Then I choose a right eye Intraocular Pressure Reading of "<rightEyeIPR>"
      And I add right eye comments of "<rightEyeComm>"

      Then I choose a left eye Intraocular Pressure Instrument  of "5"

      And I choose left eye Dilation of Yes

      Then I choose a left eye Intraocular Pressure Reading Time of "<leftEyeIPRTime>"
      Then I choose a left eye Intraocular Pressure Reading of "<leftEyeIPR>"
      And I add left eye comments of "<leftEyeComm>"

      Then I Save the Phasing Event

      Then I Confirm that the Readings Invalid time error messages are displayed

      Examples:
        |uname|pwd  |siteName/Number|firmNmae/Number|hospNumber|speciality|event  |rightEyeIPRTime|rightEyeIPR|rightEyeComm|leftEyeIPRTime|leftEyeIPR|leftEyeComm|
        |admin|admin|1              |3              |1009465   |Glaucoma  |Phasing|25:12          |5          |R TEST      |34:47         |7         |L TEST     |
