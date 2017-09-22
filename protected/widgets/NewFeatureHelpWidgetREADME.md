Written by Tom Ayoola - tom.ayoola@abehr.com
-----------------------------------------------
NewFeatureHelpWidget Documentation
=====================================
Goal of Widget (NewFeatureHelp)
----------------------------------
(ENSURE YOU USE NewFeatureHelp NOT FeatureHelp)
To provide a simple way for developers add help in the form of splash
screens and tours to any page on OpenEyes by simply writing 1 line of code in a chosen view file that will render the NewFeatureHelp widget
passing it 3 parameters splashScreen, tours, and downloadLinks (all as
strings and arrays). Then the widget will handle:
* The generating the relevant HTML (i.e. the help button trigger that in the bottom left of the page and the dynamically generated buttons and popup)
* Instatiating a NewFeatureHelpController that adds the event listeners
and handles the states and logic of the widget to prevent errors.



* This widget uses the bootstrap-tour library (http://bootstraptour.com/api/)
* This widget uses the popover.js library
https://www.w3schools.com/bootstrap/bootstrap_ref_js_popover.asp

* The purpose of the widget is to provide the user with a help button
  at the bottom right corner of the page (can be rendered in any view).

* The widget initially shows a splash screen which is an overlay over the
  current page which shows some additional information, for example new features.

* The widget also allows the user to select a "tour" which takes the
  user through some pre-defined steps and explains them.

* Lastly, the widget allows the user to select a document to download,
  for example, a PDF of old paperwork.

* Therefore, the widget takes in 3 parameters:
* $splash_screen = array(); - an array of popups (identical structure to tour steps which is defined in detail  the api documentation popover.js and bootstraptour.js)
* $tours = array(); - an array of tours where each tour is the tour steps used to construct the tour is JavaScript (with names)
* $download_links = array(); - simply an array of strings (with names)

Example widget rendering in a view
---------------------------------------------
<?php
$new_feature_help_parameters = array(
  'splash_screen' => array(
    array(
      'element' => '.large-6.medium-7.column',
      'title' => 'User Panel',
      'content' => 'This is where...',
      'contentLess' => 'This is....',
      'backdropContainer' => 'header'
    ),
    array(
      'element' => '.oe-find-patient:first',
      'title' => 'Paitent Search',
      'content' => 'This is where...',
      'contentLess' => 'This is....',
      'showParent' => 'true'
    )
  ),
  'tours' => array(
    'tour1' => array(
      array(
       'element' => '.large-6.medium-7.column',
       'title' => 'User Panel',
       'content' => 'This is where...',
       'contentLess' => 'This is....',
       'backdropContainer' => 'header'
     ),
     array(
       'element' => '.oe-find-patient:first',
       'title' => 'Paitent Search',
       'content' => 'This is where...',
       'contentLess' => 'This is....',
       'showParent' => 'true'
     )
   ),
   'tour2' => array(
     array(
      'element' => '.large-6.medium-7.column',
      'title' => 'User Panel',
      'content' => 'This is where...',
      'contentLess' => 'This is....',
      'backdropContainer' => 'header'
    ),
    array(
      'element' => '.oe-find-patient:first',
      'title' => 'Paitent Search',
      'content' => 'This is where...',
      'contentLess' => 'This is....',
      'showParent' => 'true'
    )
   )
 ),
  'download_links' => array(
    'pdf1' => 'http://www.axmag.com/download/pdfurl-guide.pdf',
    'pdf2' => 'http://www.axmag.com/download/pdfurl-guide.pdf'
  )
);

$this->widget('application.widgets.NewFeatureHelp', $new_feature_help_parameters);
?>

Files changed
-----------------------------------------------------------
See history of commits to see files added and changed.
