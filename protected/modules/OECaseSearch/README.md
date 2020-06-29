# OECaseSearch
Advanced searching and custom analytics module for OpenEyes.

## Configuration
To enable this module in OpenEyes, add OECaseSearch to the module list in any common config file.

To enable/disable default parameters and/or variables or change search provider, simply add/remove them from their respective sections in `OECaseSearch/config/common.php`.

## Creating your own Case Search parameters
To create a new Case search parameter type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click *CaseSearchParameter Generator*.
4. On the next screen, enter the class name of the parameter.
5. The SQL alias prefix and parameter name fields will be pre-filled based on the value entered for the class name. If the values need to be different, feel free to change them.
6. Enter the name of each attribute the parameter will have in addition to its name and selected operator. This can be left blank.
7. Enter the class name of at least one search provider. If your parameter supports multiple search providers, separate the name of each search provider with a comma.
8. If you want the case search parameter to be placed in a different module, specify the Yii path alias of the module eg. `application.modules.Module`. This defaults to `application.modules.OECaseSearch`.
9. Click Preview. This will generate a snapshot of the parameter class.
10. If you are satisfied with the auto-generated code, click Generate. This will add the parameter to the `models` folder within the specified module path.
11. Once the code has been generated, add any functions required by each enabled search provider's interface.
12. Add any validation rules on the attributes to the rules function.
13. In `OECaseSearch/config/common.php`, add the class name to the parameters array.
14. You're all set! Save your changes and add the parameter to your next case search!

## Creating your own Case Search variables
To create a new Case search variable type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click *CaseSearchVariable Generator*.
4. On the next screen, enter the class name of the variable.
5. The variable name and variable label fields will be pre-filled based on the value entered for the class name. If the values need to be different, feel free to change them.
6. Enter the unit of measure for the variable. This can be left blank.
7. Enter the class name of at least one search provider. If your parameter supports multiple search providers, separate the name of each search provider with a comma.
8. If you want the case search variable to be placed in a different module, specify the Yii path alias of the module eg. `application.modules.Module`. This defaults to `application.modules.OECaseSearch`.
9. Click Preview. This will generate a snapshot of the parameter class.
10. If you are satisfied with the auto-generated code, click Generate. This will add the parameter to the `models` folder within the specified module path.
11. Once the code has been generated, add any functions required by each enabled search provider's interface.
12. Add any validation rules on the attributes to the rules function.
13. In `OECaseSearch/config/common.php`, add the class name to the variables array.
14. You're all set! Save your changes and add the variable to your next case search!

## Search Providers
The OECaseSearch module supports the use of different search providers depending on individual needs for searching. To change the search provider, simply change the class name for the searchProvider component in `OECaseSearch/config/common.php` to another class which extends the SearchProvider class, and add any desired property override values. All searching will be routed through a single common SearchProvider instance.

To define a new search provider, the subclass must implement the `executeSearch($parameters)` and `getVariableData($variables, $start_date, $end_date, $return_csv, $mode)` functions. Additionally, any defined case search parameter and variable classes should include handling for each different provider, whether it be MySQL or SOLR for instance. This is best achieved through interfaces for each search provider subclass.

A MySQL/MariaDB supported search provider, DBProvider, is provided and configured by default (which may also support other SQL-based databases as well due to common SQL structures). It is recommended to set the driver property to the name of the SQL dialect in use.
