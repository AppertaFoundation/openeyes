OpenEyes
========

Introduction
------------

OpenEyes is a collaborative, open source, project led by Moorfields Eye Hospital. The goal is to produce a framework which will allow the rapid, and continuous development of electronic patient records (EPR) with contributions from Hospitals, Institutions, Academic departments, Companies, and Individuals.

The initial focus is on Ophthalmology, but the design is sufficiently flexible to be used for any clinical specialty.

Ophthalmic units of any size, from a single practitioner to a large eye hospital, should be able to make use of the structure, design, and code to produce a functional, easy to use EPR at minimal cost. By sharing experience, pooling ideas, and distributing development effort, it is expected that the range and capability of OpenEyes will evolve rapidly.

Disclaimer
----------
OpenEyes is provided under a GPL v3 license and all terms of that license apply ([https://www.gnu.org/licenses/agpl-3.0.html](https://www.gnu.org/licenses/agpl-3.0.html)). Use of the OpenEyes software or code is entirely at your own risk. Neither the OpenEyes Foundation, ACROSS Health Ltd 
nor Moorfields Eye Hospital NHS Foundation Trust accept any responsibility for loss or damage to any person, property or
reputation as a result of using the software or code. No warranty is provided by any party, implied or
otherwise. This software and code is not guaranteed safe to use in a clinical environment and you
should make your own assessment on the suitability for such use.

Resources
---------

This is the main repository for development of the core OpenEyes framework.  Event type modules are being developed in other repositories both by ourselves and third party developers.  The [OpenEyes Project Overview](https://github.com/openeyes/OpenEyes/wiki#project-overview) provides a list of currently stable modules.  You may also be interested in our [EyeDraw repository](https://github.com/openeyes/EyeDraw) - this code is used by OpenEyes but may also be used independently.

The principal source of information on OpenEyes is [the OpenEyes website](http://www.openeyes.org.uk)

If you're interested in the OpenEyes project, join our announcements mailing list by sending a blank email to: <announcements+subscribe@openeyes.org.uk>

You can also send general enquiries to our main email address: <info@openeyes.org.uk>

You can find us on twitter at: http://twitter.com/openeyes_oef

Demo versions of OpenEyes featuring fictional patient data for testing purposes are available at: <http://demo.openeyes.org.uk> (u: username p: password).

Developers, developers, developers!
-----------------------------------

Developers can request to join our discussion list for third party developers by sending a blank email to: <dev+subscribe@openeyes.org.uk>

If you need to share repositories with members of the core development team, you can find them listed as _organizational members_ at: <https://github.com/openeyes>

OpenEyes follows the [gitflow](http://nvie.com/posts/a-successful-git-branching-model/) model for git branches. As such, the stable release branch is always on master. For bleeding edge development, use the develop branch.

Setup and installation documentation is available from the README file in the oe_installer repository.

We are beginning to evolve some documentation for developers on [our github wiki](https://github.com/openeyes/OpenEyes/wiki) including [coding guidelines](https://github.com/openeyes/OpenEyes/wiki/Coding-Guidelines), [working with the core team](https://github.com/openeyes/OpenEyes/wiki/Working-With-The-Core-Team) and our [Event type module development guide](https://github.com/openeyes/OpenEyes/wiki/Event-Type-Module-Development-Guide).

Issues in the core should be logged through the [github issues system](https://github.com/openeyes/OpenEyes/issues) for the moment.  Though we will be making our internal JIRA system available in due course, and will transition logged issues across to this so that we can keep everything in one place  Links for this will follow when this becomes available.

Dev Setup
---------
To make life easier and also help ensure consistency in environments we use [Vagrant](http://vagrantup.com) + [Ansible](https://www.ansible.com) to build our development environments, the basic requirements for this are to have the following installed and available on the host machine:

* [Vagrant](http://vagrantup.com)
* [Virtualbox](http://virtualbox.org) or [VMWare Fusion](http://www.vmware.com/products/fusion.html)

Running `vagrant up` the first time will install any missing vagrant plugins:

* [vagrant-auto_network](https://github.com/oscar-stack/vagrant-auto_network)
* [vagrant-hostsupdater](https://github.com/cogitatio/vagrant-hostsupdater)
* [vagrant-cachier](https://github.com/fgrehm/vagrant-cachier)


Once the plugins have been installed you will need to run `vagrant up` again and then go and make a cup of tea whilst a base box is installed and configured.  You'll be asked for your root password (Linux / OSX) as part of the set up process as your `/etc/hosts` files is updated with the machine IP and hostname.

Once the build has finished you can access OpenEyes using the link:

[http://openeyes.vm](http://openeyes.vm)

**Note:** [Google Chrome](https://www.google.com/chrome/) is the supported browser for OpenEyes.

## Command Line Options

To allow for multiple environments to be built at the same time the hostname and the servername (used in the VM GUI to identify machines) can be changed via the command line as below when building the VM:

	--hostname="openeyes.dev.local"
	--servername="My Open Eyes Dev Server"

Full usage:

	$ vagrant --hostname="openeyes.dev.local" --servername="My Open Eyes Dev Server" up

If either are omitted the default vales of "openeyes.vm"" and "OpenEyes Dev Server" are used for the hostname and servername respectively.

**Note:** if the options are omitted the default values are used, the command line options have to be before the vagrant command for them to work.

**Further Note:** These options must be used each time the box is brought up; at the moment vagrant does not respect the original values used, and will fall over. See [issue 457](https://github.com/openeyes/OpenEyes/issues/457)

#### XDebug

Is enabled in Apache by default and carries an up to 1 second time penalty on requests, if you don't need or won't be using XDebug at all then commenting changing lines 85 - 86 in `ansible/vars/all.yml` to comment out the package name will ensure it isn't installed.

	# php_xdebug:
	#   - php5-xdebug

By default Xdebug is disabled on the CLI due to [documented](https://getcomposer.org/doc/articles/troubleshooting.md#xdebug-impact-on-composer) performance issues when using composer.

#### Windows 10

You will need to have downloaded VC++ for Vagrant to be able to download base boxes for the build, (see this issue for more information [https://github.com/mitchellh/vagrant/issues/6754](https://github.com/mitchellh/vagrant/issues/6754))

Building and running under Windows has been tested using [Cygwin](https://www.cygwin.com) (run as administrator - right click on the shortcut "Run as Administrator") to run git clone against the repository and also run the various vagrant commands.

**Note:** You should ensure that that the path you clone the repository to doesn't have any spaces in as Virtualbox complains about this during the build process.

The following commands should be installed as part of the Cygwin install:

* git
* openssh
* rsync

### Useful Vagrant Commands

* `vagrant up` - Will build the environment if it hasn't already been built
* `vagrant provision` - Will update the machine with any Ansible configuration changes
* `vagrant status` - Will tell you the status of the box
* `vagrant halt` - Halt's the machine (going home for the night)
* `vagrant suspend` - Will suspend the machine
* `vagrant destroy` - Will remove the machine build

And if that's not enough there is the Vagrant [documentation](https://www.vagrantup.com/docs/) and also `vagrant help`

#### Todo:

Additional / Outstanding tasks to be completed:

* Resolve PHP 5.3 support [issue 398](https://github.com/openeyes/OpenEyes/issues/398)
* Changes to support AWS provisioning (although this may be better left to a build specific Ansible playbook)
* Tailor the roles better to the OE build rather than coding around more generic [Ansible Galaxy](https://galaxy.ansible.com) based roles.

#### Known issues

1. XDebug enabled in CLI - setting in `ansible/vars/all.yml` is ignored - https://github.com/geerlingguy/ansible-role-php-xdebug/issues/34


Printing
--------

OpenEyes now supports full PDF printing using wkhtmltopdf, but it needs to be compiled with a patched QT library in order to work properly. As of version 1.12 a pre-compiled binary
is shipped in the oe_installer repository. However, should you need to re-compile it, you can find instructions for doing this [here](https://github.com/openeyes/OpenEyes/wiki/Compiling-WKHtmlToPDF-to-enable-PDF-printing).
