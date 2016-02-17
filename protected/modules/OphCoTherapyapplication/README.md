OphCoTherapyApplication Module
==============================

This module is intended to generate applications for funding for therapies for patients. Once a therapy application event
is created, it can be submitted. The submission process generates the PDF application form(s) and sends this in an email
to the configured address(es).

The first release version of this module is 1.4.

Configuration
=============

See config/common.php for details of configuration variables. Setting up of therapies, email recipients etc can be managed
through OpenEyes admin.

Initialisation
==============

The createtherapyapplicationfilecollections command will import files from a nested directory structure.

    ./yiic help createtherapyapplicationfilecollections

for details

Dependencies
------------

1. Requires the following modules:
  1. OphTrIntravitrealinjection
2. An email server supported by SwiftMailer
3. Uses the following modules if they are present:
  1. OphCiExamination
  2. OphTrConsent

Items of note
-------------

1. For an application to be submitted, an Injection Management element needs to have been defined for the eye(s) to be
applied for in the current episode. Similarly, visual acuity must have been recorded for both eyes. If OphTrConsent is
installed an appropriate consent form for either Intravitreal Injection or PDT is required.

Templates
---------

When an application is processed, an email is generated for each eye. the templates for the content of this email exist in
<pre>
views/email/
	|
	\---- email_compliant.php  - email text for NICE Compliant therapy applications
	\---- email_noncomplaint.php - email text for non-NICE Compliant therapy applications
	\---- pdf_compliant.php - pdf template for compliant applications
	\---- pdf_compliant_[template_code].php - pdf template for compliant applications
	\---- pdf_noncompliant.php - pdf template for non compliant applications
	\---- pdf_noncompliant_[template_code].php - pdf template for non compliant applications
</pre>

If a specific drug needs a different attachment, then it should be assigned a template code in the admin for treatments.
The appropiately named template can then be included in the email directory.


Known Issues
------------

The admin functionality is not wholly complete. In particular:

1. Decision Tree management is not functionally complete:
 1. Rule Delete
 2. Decision Tree Delete (soft or otherwise)
 3. No way of viewing a whole tree in one go.
2. implement template overrides for email text.
3. setup gitignore appropriately to ignore custom templates that are put here.
4. The dependency on OphTrIntravitrealinjection is actually inappropriate given that therapies don't have to be
injections.
5. The consent form dependency should be a configuration option on the application.
