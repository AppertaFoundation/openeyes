# wkhtmltox

#### Table of Contents

1. [Overview](#overview)
2. [Module Description - What the module does and why it is useful](#module-description)
3. [Setup - The basics of getting started with wkhtmltox](#setup)
    * [What wkhtmltox affects](#what-wkhtmltox-affects)
4. [Usage - Configuration options and additional functionality](#usage)
5. [Limitations - OS compatibility, etc.](#limitations)
6. [Development - Guide for contributing to the module](#development)

## Overview

This module can install the wkhtmltox toolkit to your machine. It supports:
 * Debian Wheezy
 * Ubuntu Precise/Trusty
 * CentOS/RHEL 5 or 6

For more information about wkhtmltopdf: http://wkhtmltopdf.org/

## Module Description

The installation of wkhtmltox/pdf is automated by this module.
It can download the latest version from sourceforge binaries and install it.
Uses wget to do the initial download.

## Setup

### What wkhtmltox affects

* Installs a version specific installation of wkhtmltox/pdf to your system
* Shouldn't break anything, but do make sure you don't have a system repo install of 'whtmltopdf'
  as they might conflict.

## Usage

puppet module install jlondon-wkhtmltox

Also supports inclusion in librarian-puppet

Usage of the module is pretty basic and shouldn't need much other than a default run:

    class { 'wkhtmltox':
      ensure => present,
    }

## Limitations

Only tested to work with Debian wheezy, Ubuntu precise/trusty or centos 5/6.

## Development

Feel free to fork and modify this module.
Please make sure that if you make a useful change to submit a pull request!
Additionally if you do fully fork the project, please do not remove attribution (I only ask this because it has happened before).
