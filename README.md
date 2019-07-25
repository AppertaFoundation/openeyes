OpenEyes
========

Introduction
------------

OpenEyes is a collaborative, open source, project led by the OpenEyes Foundation (http://www.openeyes.org.uk/). The goal is to produce a framework which will allow the rapid, and continuous development of an electronic patient record (EPR for ophthalmology in particular and eye care in general). Clinical and technical contributions are made by Hospitals, Institutions, Academic departments, Companies, and Individuals.

The initial focus is on Ophthalmology, but the design is sufficiently flexible to be used for any clinical specialty.

Ophthalmic units of any size, from a single practitioner to a large eye hospital, should be able to make use of the structure, design, and code to produce a functional, easy to use EPR at minimal cost. By sharing experience, pooling ideas, and distributing development effort, it is expected that the range and capability of OpenEyes will evolve rapidly. OpenEyes should also be of values for use by
non-medical staff such as optometrists in the delivery of share programs of eye care.

Disclaimer
----------
OpenEyes is provided under an GNU Affero GPL v3.0  (AGPL v3.0)  license and all terms of that license apply (https://www.gnu.org/licenses/agpl-3.0.en.html). Use of the OpenEyes software or code is entirely at user risk. The Apperta Foundation does not accept any responsibility for loss or damage to any person, property or reputation as a result of using the software or code. No warranty is provided by any party, implied or otherwise. For use of the software or code.  This software and code is not guaranteed safe to use in a clinical environment; any user is advised to undertake a safety assessment to confirm that deployment matches local clinical safety requirements. 

Resources
---------

This is the main repository for development of the core OpenEyes framework.  Event type modules are being developed in other repositories both by ourselves and third party developers.  The [OpenEyes Project Overview](https://github.com/appertafoundation/OpenEyes/wiki#project-overview) provides a list of currently stable modules.  You may also be interested in our [EyeDraw repository](https://github.com/appertafoundation/EyeDraw) - this code is used by OpenEyes but may also be used independently.

The principal source of information on OpenEyes is [the OpenEyes website](http://www.openeyes.org.uk)

If you're interested in the OpenEyes project, or for general enquiries, email: <info@openeyes.org.uk>

You can find us on twitter at: http://twitter.com/openeyes_oef

Developers, developers, developers!
-----------------------------------

If you need to share repositories with members of the core development team, you can find them listed as _organizational members_ at: <https://github.com/openeyes>

OpenEyes follows the [gitflow](http://nvie.com/posts/a-successful-git-branching-model/) model for git branches. As such, the stable release branch is always on master. For bleeding edge development, use the develop branch.

We are beginning to evolve some documentation for developers on [our github wiki](https://github.com/appertafoundation/OpenEyes/wiki) including [coding guidelines](https://github.com/appertafoundation/openeyes/wiki/Coding-Guidelines), [working with the core team](https://github.com/appertafoundation/openeyes/wiki/Working-With-The-Core-Team) and our [Event type module development guide](https://github.com/appertafoundation/openeyes/wiki/Event-Type-Module-Development-Guide).

Issues in the core should be logged through the [github issues system](https://github.com/appertafoundation/OpenEyes/issues) for the moment.  Though please be aware that no service level agreement exists for the open source project and no support can be given via github. The team will do their best to fix any critical issues reported, but no gaurantees are given. For official support, with Service Level Agreements on resolution times, please contact our commercial partner, [ABEHR Digital](http://abehr.com)

Setup
---------
To make life easier and also help ensure consistency in environments we use Docker. Full setup instructions can be found in the [appertaopeneyes docker hub repository](https://hub.docker.com/r/appertaopeneyes/web-allin1)

Once the build has finished you can access OpenEyes using the link:

[http://localhost](http://localhost)

**Note:** [Google Chrome](https://www.google.com/chrome/) is the *only* supported browser for OpenEyes.

**Note:** There are many options/variables/tags avilable in the Docker conatiner. If the options are omitted then default values are used. More information can be found on the [docker hub page](https://hub.docker.com/r/appertaopeneyes/web-allin1)

