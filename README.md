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
OpenEyes is provided under an GNU Affero GPL v3.0  (AGPL v3.0)  license and all terms of that license apply (https://www.gnu.org/licenses/agpl-3.0.en.html). Use of the OpenEyes software or code is entirely at user risk. The OpenEyes Foundation does not accept any responsibility for loss or damage to any person, property or reputation as a result of using the software or code. No warranty is provided by any party, implied or otherwise. For use of the software or code.  This software and code is not guaranteed safe to use in a clinical environment; any user is advised to undertake a safety assessment to confirm that deployment matches local clinical safety requirements. 

Resources
---------

This is the main repository for development of the core OpenEyes framework.  Event type modules are being developed in other repositories both by ourselves and third party developers.  The [OpenEyes Project Overview](https://github.com/openeyes/OpenEyes/wiki#project-overview) provides a list of currently stable modules.  You may also be interested in our [EyeDraw repository](https://github.com/openeyes/EyeDraw) - this code is used by OpenEyes but may also be used independently.

The principal source of information on OpenEyes is [the OpenEyes website](http://www.openeyes.org.uk)

If you're interested in the OpenEyes project, or for general enquiries, email: <info@openeyes.org.uk>

You can find us on twitter at: http://twitter.com/openeyes_oef

Developers, developers, developers!
-----------------------------------

If you need to share repositories with members of the core development team, you can find them listed as _organizational members_ at: <https://github.com/openeyes>

OpenEyes follows the [gitflow](http://nvie.com/posts/a-successful-git-branching-model/) model for git branches. As such, the stable release branch is always on master. For bleeding edge development, use the develop branch.

Setup and installation documentation is available from the README file in the [oe_installer repository](https://github.com/openeyes/oe_installer.)

We are beginning to evolve some documentation for developers on [our github wiki](https://github.com/openeyes/OpenEyes/wiki) including [coding guidelines](https://github.com/openeyes/OpenEyes/wiki/Coding-Guidelines), [working with the core team](https://github.com/openeyes/OpenEyes/wiki/Working-With-The-Core-Team) and our [Event type module development guide](https://github.com/openeyes/OpenEyes/wiki/Event-Type-Module-Development-Guide).

Issues in the core should be logged through the [github issues system](https://github.com/openeyes/OpenEyes/issues) for the moment.  Though we will be making our internal JIRA system available in due course, and will transition logged issues across to this so that we can keep everything in one place  Links for this will follow when this becomes available.

Dev Setup
---------
To make life easier and also help ensure consistency in environments we use [Vagrant](http://vagrantup.com). Full setup instructions can be found in the [oe_installer repository](https://github.com/openeyes/oe_installer)

Once the build has finished you can access OpenEyes using the link:

[http://openeyes.vm](http://openeyes.vm)

**Note:** [Google Chrome](https://www.google.com/chrome/) is the *only* supported browser for OpenEyes.

## Command Line Options

To allow for multiple environments to be built at the same time the hostname and the servername (used in the VM GUI to identify machines) can be changed via the command line as below when building the VM:

	--hostname="openeyes.dev.local"
	--servername="My Open Eyes Dev Server"

Full usage:

	$ vagrant --hostname="openeyes.dev.local" --servername="My Open Eyes Dev Server" up

If either are omitted the default vales of "openeyes.vm"" and "OpenEyes Dev Server" are used for the hostname and servername respectively.

**Note:** if the options are omitted the default values are used, the command line options have to be before the vagrant command for them to work.

**Further Note:** These options must be used each time the box is brought up; at the moment vagrant does not respect the original values used, and will fall over. See [issue 457](https://github.com/openeyes/OpenEyes/issues/457)

### Useful Vagrant Commands

* `vagrant up` - Will build the environment if it hasn't already been built
* `vagrant provision` - Will update the machine with any Ansible configuration changes
* `vagrant status` - Will tell you the status of the box
* `vagrant halt` - Halt's the machine (going home for the night)
* `vagrant suspend` - Will suspend the machine
* `vagrant destroy` - Will remove the machine build

And if that's not enough there is the Vagrant [documentation](https://www.vagrantup.com/docs/) and also `vagrant help`


Printing
--------

OpenEyes now supports full PDF printing using wkhtmltopdf, but it needs to be compiled with a patched QT library in order to work properly. As of version 1.12 a pre-compiled binary
is shipped in the oe_installer repository. However, should you need to re-compile it, you can find instructions for doing this [here](https://github.com/openeyes/OpenEyes/wiki/Compiling-WKHtmlToPDF-to-enable-PDF-printing).
