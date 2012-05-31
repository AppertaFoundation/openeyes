OpenEyes
========

Introduction
------------

OpenEyes is a collaborative, open source, project led by Moorfields Eye Hospital. The goal is to produce a framework which will allow the rapid, and continuous development of electronic patient records (EPR) with contributions from Hospitals, Institutions, Academic departments, Companies, and Individuals.

The initial focus is on Ophthalmology, but the design is sufficiently flexible to be used for any clinical specialty.

Ophthalmic units of any size, from a single practitioner to a large eye hospital, should be able to make use of the structure, design, and code to produce a functional, easy to use EPR at minimal cost. By sharing experience, pooling ideas, and distributing development effort, it is expected that the range and capability of OpenEyes will evolve rapidly.

Resources
---------

This is the main repository for development of the core OpenEyes framework.  Event type modules are being developed in other repositories both by ourselves and third party developers.  (EG: [OphCoCorrespondence](https://github.com/openeyes/OphCoCorrespondence)).  You may also be interested in our [EyeDraw repository](https://github.com/openeyes/EyeDraw) - this code is used by OpenEyes but may also be used independently.

The principal source of information on OpenEyes is [the OpenEyes website](http://www.openeyes.org.uk)

If you're interested in the OpenEyes project, join our announcements mailing list by sending a blank email to: <announcements+subscribe@openeyes.org.uk>

You can also send general enquiries to our main email address: <info@openeyes.org.uk>

You can find us on twitter at: http://twitter.com/openeyes_oef

A demo version of OpenEyes featuring fictional patient data for testing purposes is available at: <http://staging.openeyes.org.uk> and you can access the original prototype for OpenEyes at <http://aylwards.co.uk/demo/login.php> with username: johnsaunders and password: secret

Developers, developers, developers!
-----------------------------------

Developers can request to join our discussion list for third party developers by sending a blank email to: <dev+subscribe@openeyes.org.uk>

If you need to share repositories with members of the core development team, you can find them listed as _organizational members_ at: <https://github.com/openeyes>

We will be moving shortly to the 'development' branch becoming a stable basis for development to pivot around and will make clear when our recommendation changes to this branch.  Meanwhile we currently recommend that developers checkout the [release/0.10.0-moorfields](https://github.com/openeyes/OpenEyes/tree/release/0.10.0-moorfields) branch if they plan to work on the code.  You can find a test dataset compatible with the 0.10.0-moorfields branch at the [dumps directory of the sample repository](https://github.com/openeyes/Sample/tree/master/dump) (It may be easiest to cat this into an empty database, then run the yiic migrations afterwards).

Setup and installation documentation is available from the [documentation section of the website](http://www.openeyes.org.uk/documentation.html)

We are beginning to evolve some documentation for developers on [our github wiki](https://github.com/openeyes/OpenEyes/wiki) including [coding guidelines](https://github.com/openeyes/OpenEyes/wiki/Coding-Guidelines), [working with the core team](https://github.com/openeyes/OpenEyes/wiki/Working-With-The-Core-Team) and our [Event type module development guide](https://github.com/openeyes/OpenEyes/wiki/Event-Type-Module-Development-Guide).

Issues in the core should be logged through the [github issues system](https://github.com/openeyes/OpenEyes/issues) for the moment.  Though we will be making our internal JIRA system available in due course, and will transition logged issues across to this so that we can keep everything in one place  Links for this will follow when this becomes available.

News
----

After a successful launch of the OpenEyes core and the Booking module in January 2012, as of May 2012 we are completing work on three ophthalmic modules for initial deployment at Moorfields: Prescription, Operation Note, and Correspondence.  These will be followed rapidly by an Examination module after which the core team should have more time to help facilitate third party development.  We will shortly be launching a more up to date and revised version of our website.  


