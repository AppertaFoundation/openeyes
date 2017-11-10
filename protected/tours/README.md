Tours Documentation
===================

Goal of Tours
-------------
To provide a simple way to add help in the form of splash
screens and tours to any page on OpenEyes.

Configuration
-------------

The definition of tours has been abstracted away into two files:

* `protected/tours/common.php` - this file defines all the tours that are a core of OpenEyes.
* `protected/tours/local.php` - This file must be created for a specific installation and is provided to allow individual sites to define additional information to their users. The `local.sample.php` file should be used to start this file.

Tours use jQuery and CSS selectors to attach steps to elements on the page. More info on how to use selectors can be found at [https://api.jquery.com/category/selectors/](https://api.jquery.com/category/selectors/)
[https://www.w3schools.com/cssref/css_selectors.asp](https://www.w3schools.com/cssref/css_selectors.asp)

Implementation Details
----------------------

The implementation of the tours utilises the [bootstap-tour](http://bootstraptour.com/api/) library to render the steps.

The tours have been implemented extending work done previously. The original implementation provided for 3 different options:
 * The widget initially shows a splash screen which is an overlay over the
   current page which shows some additional information, for example new features.
 * The widget also allows the user to select a "tour" which takes the
   user through some pre-defined steps and explains them.
 * Lastly, the widget allows the user to select a document to download,
   for example, a PDF of old paperwork.

However the efforts to get this functionality ready for production has solely focused on the tours functionality.

Key Components
--------------

* `widgets/NewFeatureHelp.php` The PHP widget handles the loading of the configuration, and providing the necessary tour data to the javascript component.
* `widgets/js/NewFeatureHelp.js` Custom JS wrapper around the bootstrap-tour component to manage multiple tours, and tracking of user interaction with them.
* `controllers/FeatureTourController.php` Simple endpoint controller to receive notifications from the frontend regarding user interaction.
* `models/UserFeatureTourState` Lightweight active record model for tracking user state on the different tours.
