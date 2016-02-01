@diagnosis @regression
Feature: Open Eyes Login and Patient Diagnosis Screen
@PV_Diagnosis
         Regression coverage of this event is approx 98%

  @PV_Diagnosis_Route_1
  Scenario Outline: Route 1: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then I Add an Ophthalmic Diagnosis selection of "<ophDiag>"
    And I select that it affects eye "<affectsEye>"
    And I select a Opthalmic Diagnosis date of day "<ophDay>" month "<ophMonth>" year "<ophYear>"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "<sysDiag>"
    And I select that it affects Systemic side "<sysSide>"
    And I select a Systemic Diagnosis date of day "<sysDay>" month "<sysMonth>" year "<sysYear>"

    Then I save the new Systemic Diagnosis

    Then I Add a Previous Operation of "<prevOp>"
    And I select that it affects Operation side "<opSide>"
    #And I select a Previous Operation date of day "9" month "9" year "2012"
    Then I save the new Previous Operation

    Then I edit the CVI Status "<CVIStatus>"
    And I select a CVI Status date of day "<CVIStatusDay>" month "<CVIStatusMonth>" year "<CVIStatusYear>"
    Then I save the new CVI status

    Then I Remove existing Allergy
    Then I Add Allergy "<allergy>" and Save

    And I Add a Family History of relative "<FmlHisRel>" side "<fmlHisSide>" condition "<fmlHisCon>" and comments "<fmlHisComments>" and Save

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number|hospNumber|ophDiag  |affectsEye|ophDay|ophMonth|ophYear|sysDiag  |sysSide|sysDay|sysMonth|sysYear|prevOp|opSide|CVIStatus|CVIStatusDay|CVIStatusMonth|CVIStatusYear|allergy|FmlHisRel|fmlHisSide|fmlHisCon|fmlHisComments|
    |admin|admin|1              |1              |1009465   |193570009|Left      |18    |6       |2012   |195967001|Left   |18    |6       |2012   |1     |Left  |4        |18          |6             |2012         |5      |1        |1         |1        |TEST          |

 

  @PV_Diagnosis_Route_2
  Scenario Outline: Route 3: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies
            Remove Medication

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then I Add an Ophthalmic Diagnosis selection of "<ophDiag>"
    And I select that it affects eye "<affectsEye>"
    And I select a Opthalmic Diagnosis date of day "<ophDay>" month "<ophMonth>" year "<ophYear>"
    Then I save the new Opthalmic Diagnosis

    Then I remove the Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "<sysDiag>"
    And I select that it affects Systemic side "<sysSide>"
    And I select a Systemic Diagnosis date of day "<sysDay>" month "<sysMonth>" year "<sysYear>"
    Then I save the new Systemic Diagnosis

    Then I remove the Systemic Diagnosis

    And I Add Medication details medication "<medication>" route "<medRoute>" frequency "<medFreq>" date from "<medDateFrom>" and Save

    Then I remove the Medication

    And I Add a Family History of relative "<FmlHisRel>" side "<fmlHisSide>" condition "<fmlHisCon>" and comments "<fmlHisComments>" and Save

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number|hospNumber|ophDiag |affectsEye|ophDay|ophMonth|ophYear|sysDiag  |sysSide|sysDay|sysMonth|sysYear|prevOp|opSide|CVIStatus|CVIStatusDay|CVIStatusMonth|CVIStatusYear|allergy|medication|medRoute|medFreq|medDateFrom|FmlHisRel|fmlHisSide|fmlHisCon|fmlHisComments|
      |admin|admin|1              |2              |1009465   |24010005|Both      |9     |7       |2012   |414545008|Both   |9     |7       |2012   |1     |Left  |4        |18          |6             |2012         |5      |3         |2       |8      |1          |4        |3         |2        |TEST          |

  @PV_Diagnosis_Route_3
  Scenario Outline: Route 4: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies
            Social History Tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"
    Then I search for hospital number "<hospNumber>"

    Then I Add an Ophthalmic Diagnosis selection of "<ophDiag>"
    And I select that it affects eye "<affectsEye>"
    And I select a Opthalmic Diagnosis date of day "<ophDay>" month "<ophMonth>" year "<ophYear>"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "<sysDiag>"
    And I select that it affects Systemic side "<sysSide>"
    And I select a Systemic Diagnosis date of day "<sysDay>" month "<sysMonth>" year "<sysYear>"

    Then I save the new Systemic Diagnosis

    Then I Add a Previous Operation of "<prevOp>"
    And I select that it affects Operation side "<opSide>"
    #And I select a Previous Operation date of day "9" month "9" year "2012"
    Then I save the new Previous Operation

    Then I edit the CVI Status "<CVIStatus>"
    And I select a CVI Status date of day "<CVIStatusDay>" month "<CVIStatusMonth>" year "<CVIStatusYear>"
    Then I save the new CVI status

    Then I Remove existing Allergy
    Then I Add Allergy "<allergy>" and Save

    And I Add a Family History of relative "<" side "1" condition "1" and comments "Family History Comments" and Save

    Then I expand Social History
    And I add an Occupation of "<socHisOcc>"
    And I add an Occupation Other type of "<socHisOccOther>"
    #And I select "Motor vehicle" for "Driving Status"
    And I add a Smoking status of "<socHisSmok>"
    And I add an Accommodation status of "<socHisAccom>"
    Then I add Social Comments of "<socHisComm>"
    And I select a Carer status of "<socHisCarer>"
    Then I set an Alcohol intake of "<socHisAlcUnits>" units a week
    And I select a Substance Misuse status of "<socHisSubMisuseStat>"
    Then I Save the Social History

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number|hospNumber|ophDiag  |affectsEye|ophDay|ophMonth|ophYear|sysDiag  |sysSide|sysDay|sysMonth|sysYear|prevOp|opSide|CVIStatus|CVIStatusDay|CVIStatusMonth|CVIStatusYear|allergy|medication|medRoute|medFreq|medDateFrom|FmlHisRel|fmlHisSide|fmlHisCon|fmlHisComments|socHisOcc|socHisOccOther   |socHisSmok|socHisAccom|socHisComm          |socHisCarer|socHisAlcUnits|socHisSubMisuseStat|
  |admin|admin|1              |1              |1009465   |193570009|Left      |18    |6       |2012   |195967001|Left   |18    |6       |2012   |1     |Left  |4        |18          |6             |2012         |5      |3         |2       |8      |1          |1        |1         |1        |TEST          |7        |Nuclear Scientist|2         |3          |Test Social comments|1          |100           |1                  |



