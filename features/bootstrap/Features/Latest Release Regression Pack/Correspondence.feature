@correspondence @regression
Feature: Create New Correspondence
@COR
         Regression coverage of this event is approx 95%

  Scenario Outline: Route 1:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNo>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Site ID "<siteId>"
    And I select Address Target "<addressTarget>"
    #Then I choose a Macro of "site1"

    #And I select Clinic Date "7"

    Then I choose an Introduction of "<intro>"
    And I choose a Diagnosis of "<diag>"
    Then I choose a Management of "<management>"
    And I choose Drugs "<drugs>"
    Then I choose Outcome "<outcome>"

    And I choose CC Target "<CCTarget>"

    Given I add a New Enclosure of "<enclosure>"

    Then I Save the Correspondence Draft and confirm it has been created successfully

    Examples:
    |uname|pwd  |site           |firm                   |hospNo |speciality|event         |siteId      |addressTarget|intro |diag   |management|drugs  |outcome|CCTarget    |enclosure     |
    |admin|admin|Example        |A K Hamilton (Glaucoma)|1009465|Glaucoma  |Correspondence|Example     |Gp1          |site21|site541|site181   |site301|site341|Patient19434|Test Enclosure|

  Scenario Outline: Route 2:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Alternative Diagnosis, Management, Drugs & Outcome options

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNo>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Site ID "<siteId>"
    And I select Address Target "Gp1"
    #Then I choose a Macro of "site1"

    #And I select Clinic Date "7"

    Then I choose an Introduction of "site21"
    And I choose a Diagnosis of "site85"
    Then I choose a Management of "site161"
    And I choose Drugs "site265"
    Then I choose Outcome "site325"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

    Examples:
    |uname|pwd  |site           |firm                   |hospNo |speciality|event         |siteId     |
    |admin|admin|Example        |A K Hamilton (Glaucoma)|1009465|Glaucoma  |Correspondence|Example    |

  Scenario Outline: Route 3:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Alternative Diagnosis, Management, Drugs & Outcome options

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNo>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Site ID "<siteId>"
    And I select Address Target "Gp1"
    #Then I choose a Macro of "site1"

    #And I select Clinic Date "7"

    Then I choose an Introduction of "site41"
    And I choose a Diagnosis of "site541"
    Then I choose a Management of "site141"
    And I choose Drugs "site281"
    Then I choose Outcome "site361"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

    Examples:
      |uname|pwd  |site           |firm                   |hospNo |speciality|event         |siteId      |
      |admin|admin|Example        |A K Hamilton (Glaucoma)|1009465|Glaucoma  |Correspondence|Barking     |

  Scenario Outline: Route 4:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Alternative Diagnosis, Management, Drugs & Outcome options

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNo>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Site ID "<siteId>"
    And I select Address Target "Gp1"
    #hen I choose a Macro of "site1"

    #And I select Clinic Date "7"

    Then I choose an Introduction of "site61"
    And I choose a Diagnosis of "site85"
    Then I choose a Management of "site121"
    And I choose Drugs "site265"
    Then I choose Outcome "site401"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

    Examples:
    |uname|pwd  |site           |firm                   |hospNo |speciality|event         |siteId      |
    |admin|admin|Example        |A K Hamilton (Glaucoma)|1009465|Glaucoma  |Correspondence|Barking     |

  @COR_Scenario_5
  Scenario Outline: Route 5:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Saving without mandatory fields validation tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for hospital number "<hospNo>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I Save the Correspondence Draft

    Then I Confirm that the Mandatory Correspondence fields validation error messages are displayed

    Examples:
    |uname|pwd  |site           |firm                   |hospNo |speciality|event         |
    |admin|admin|Example        |A K Hamilton (Glaucoma)|1009465|Glaucoma  |Correspondence|
