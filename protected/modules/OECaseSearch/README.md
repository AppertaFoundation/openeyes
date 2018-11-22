# OECaseSearch
Advanced Case searching module for OpenEyes.

## Configuration
To enable this module in OpenEyes, add OECaseSearch to the module list in any common config file.

If you wish to use the sample parameters, copy the .sample files to the relevant module's `models` folder or to `protected/models` and remove the .sample file extension. 

## Creating your own Case Search parameters
To create a new Case search parameter type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click *CaseSearchParameter Generator*.
4. On the next screen, enter in the class name of the parameter.
5. The SQL alias prefix and parameter name fields will be pre-filled based on the value entered for the class name. If the values need to be different, feel free to change them.
6. Enter in the name of each attribute the parameter will have in addition to its name and selected operator. This can be left blank.
7. Enter in the class name of at least one search provider. If your parameter supports multiple search providers, separate the name of each search provider with a comma.
8. If you want the case search parameter to be placed in a different module, specify the Yii path alias of the module eg. `application.modules.Module`. This defaults to `application`.
9. Click Preview. This will generate a snapshot of the parameter class.
10. If you are satisfied with the auto-generated code, click Generate. This will add the parameter to the models folder within the specified module path.
11. Once the code has been generated, implement the renderParameter function and any functions required by each enabled search provider's interface.
12. Add any validation rules on the attributes to the rules function.
13. In `OECaseSearch/config/common.php`, add the class name to the parameters and/or fixedParameters arrays.
14. You're all set! Save your changes and add the parameter to your next case search!

## Search Providers
The OECaseSearch module supports the use of several different search providers concurrently, whether that be an SQL implementation or an indexed search such as Elasticsearch or SOLR. Provisioning between search providers is performed within the CaseSearchController and so is user-defined.
By default, all searching is routed through a single DBProvider instance.

A MySQL-supported search provider, DBProvider, is provided by default (which may also support other SQL-based databases as well); however other search providers can be added by creating subclasses of the SearchProvider abstract class.

To use the default search provider, simply copy `sample/DBProvider.php.sample`, `sample/DBProviderInterface.php.sample` and `sample/SearchProvider.php.sample` (if any are not already in the components folder) to the `protected/components` folder and remove the `.sample` from the filename.

To define a new search provider, the subclass must implement the executeSearch($parameters) function and possess a unique providerID. Additionally, any defined case search parameter classes should include handling for each different provider, whether it be MySQL or SOLR, for instance. This is achieved through interface classes.

## Creating your own search providers
To create a new search provider type, simply:

1. Enable Gii.
2. Navigate to Gii in your web browser of choice using the following URL: `http://<openeyes_url>/gii`.
3. After logging in, click *SearchProvider Generator*.
4. On the next screen, enter in the class name of the search provider.
5. Click Preview. This will generate a snapshot of the search provider class and an interface for CaseSearchParameter subclasses to implement.
6. If you are satisfied with the auto-generated code, click Generate. This will add the search provider and its interface to the components folder of the application.
7. Once the code has been generated, implement the executeSearch function.
8. Add the class name to the 'providers' array in `OECaseSearch/config/common.php` using its unique identifier as the key eg. `'providers' = array('providerID' => 'ProviderClass'),`
9. You're all set! Save your changes and create some case search parameters that utilise your new search provider.