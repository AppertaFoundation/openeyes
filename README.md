---

<center><img src="https://raw.githubusercontent.com/openeyes/openeyes.github.io/master/img/logo.png" alt="OpenEyes logo" width="200" height="200"></center>


# OpenEyes

OpenEyes™ is the leading open source Electronic Patient Record (EPR) for ophthalmology.
  
- [View Website](https://openeyes.apperta.org/) 
- [Report an issue](https://github.com/AppertaFoundation/openeyes/issues/new)
- [Request feature](https://openeyes.apperta.org/)

## Table of contents

- [Description](#Description)
- [Disclaimer](#Disclaimer)
- [Quick Start](#Quick-Start)
- [Issues and support](#issues-and-support)
- [Resources](#Resources)
- [Contributing](#Contributing)
- [Copyright and license](#Copyright-and-license)


------------
# Description 
OpenEyes is a collaborative, open source, project led by the Apperta Foundation (http://openeyes.apperta.org). The goal is to produce a framework which will allow the rapid, and continuous development of an electronic patient record (EPR for ophthalmology in particular and eye care in general). Clinical and technical contributions are made by Hospitals, Institutions, Academic departments, Companies, and Individuals.

The initial focus is on Ophthalmology, but the design is sufficiently flexible to be used for any clinical specialty.

Ophthalmic units of any size, from a single practitioner to a large eye hospital, should be able to make use of the structure, design, and code to produce a functional, easy to use EPR at minimal cost. By sharing experience, pooling ideas, and distributing development effort, it is expected that the range and capability of OpenEyes will evolve rapidly. OpenEyes should also be of value for use by non-medical staff such as optometrists in the delivery of shared programs of eye care.

# Disclaimer
----------
OpenEyes is provided under an GNU Affero GPL v3.0  (AGPL v3.0)  license and all terms of that license apply (https://www.gnu.org/licenses/agpl-3.0.en.html). Use of the OpenEyes software or code is entirely at user risk. The Apperta Foundation does not accept any responsibility for loss or damage to any person, property or reputation as a result of using the software or code. No warranty is provided by any party, implied or otherwise, for use of the software or code.  This software and code is not guaranteed safe to use in a clinical environment; any user is advised to undertake a safety assessment to confirm that deployment matches local clinical safety requirements. 

# Setup
---------
1.	These instructions are for an Ubuntu 18.04 machine, although it may work for 16.04, 20.04 and Ubuntu variants but it was built and tested for Ubuntu 18.04. So step one is to build an Ubuntu 18.04 machine, and perform software updates as required sudo apt-get update
2.	Install Git sudo apt-get install git
3.	Clone the openeyes repo:
    - a.	cd /var
    - b.	sudo mkdir www
    - c.	cd www
    - d.	sudo git clone https://github.com/AppertaFoundation/openeyes.git
4.	Run `sudo bash /var/www/openeyes/protected/scripts/install-system.sh`
    - (this installs and configures the necessary services, such as the Apache webserver)
5.	When that completes successfully, restart the Apache service using `sudo systemctl restart apache2`
6. Run `sudo bash /var/www/openeyes/protected/scripts/install-oe.sh`
    - (this installs OpenEyes itself. You will need to select option 1 to accept the terms and conditions, and then slightly later, say that Yes you want to run Composer as superuser in spite of the warning).

Assuming those scripts run successfully (you should get a message confirming successful installation or otherwise), you can then open your browser on the Ubuntu machine and navigate to localhost. The OpenEyes login screen will appear, and you can login with credentials username `admin` and password `admin`.
    
# Issues and support

Issues in the core should be logged through the [github issues system](https://github.com/AppertaFoundation/openeyes/issues/new).  

Please be aware that no service level agreement exists for the open source project and no support can be given via github. The team will do their best to fix any critical issues reported, but no guarantees are given. 

For official implementation and support, with Service Level Agreements on resolution times, please contact our commercial partner Toukan Labs at <sales@toukanlabs.com>
    
# Resources
---------

This is the main repository for development of the core OpenEyes framework.  Event type modules are being developed in other repositories both by ourselves and third party developers.  You may also be interested in our [EyeDraw repository](https://github.com/appertafoundation/EyeDraw) - this code is used by OpenEyes but may also be used independently.

The principal source of information on OpenEyes is [the OpenEyes website](http://openeyes.apperta.org)

If you're interested in the OpenEyes project, or for general enquiries, email: <openeyes@apperta.org>

You can find us on twitter at: http://twitter.com/openeyes_oef

# Contributing
-----------------------------------

If you are thinking of making a contribution to OpenEyes please contact our team at <openeyes@apperta.org>. 

If you need to share repositories with members of the core development team, you can find them listed as _organizational members_ at: <https://github.com/openeyes>

OpenEyes follows the [gitflow](http://nvie.com/posts/a-successful-git-branching-model/) model for git branches. As such, the stable release branch is always on master. For bleeding edge development, use the develop branch.

-----------------------------------
# Copyright and license    
- Code and documentation copyright 2019–2021 the [Apperta Foundation](https://apperta.org/) 
- Code released under the [GNU Affero General Public License v3.0](https://github.com/AppertaFoundation/openeyes/blob/master/LICENSE)
- Docs released under [Creative Commons](https://creativecommons.org/licenses/by/3.0/).  