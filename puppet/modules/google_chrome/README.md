# puppet-google-chrome

## Overview

Puppet module to install the Google Chrome web browser module. Currently supports installation on:

* Fedora
* Debian / Ubuntu / Mint
* openSUSE

## Tested on:

* Fedora 20, 21, 22
* CentOS 7
* Debian Wheezy / Jessie
* openSUSE 13.1, 13.2

## Usage

To install the stable version of Google Chrome, include or declare the google_chrome class.

```puppet
include 'google_chrome'
```

```puppet
class { 'google_chrome':
}
```

To install unstable or beta versions, set the appropriate version attribute.

```puppet
class { 'google_chrome':
  version => 'unstable',
}
```

```puppet
class { 'google_chrome':
  version => 'beta',
}
```

#### Build

Branch | Status |
---------|---------
Master | [![Circle CI](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/master.svg?style=svg)](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/master)
CentOS | [![Circle CI](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/centos.svg?style=svg)](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/centos)
Debian | [![Circle CI](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/debian.svg?style=svg)](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/debian)
Fedora | [![Circle CI](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/fedora.svg?style=svg)](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/fedora)
SUSE | [![Circle CI](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/suse.svg?style=svg)](https://circleci.com/gh/jamesnetherton/puppet-google-chrome/tree/suse)
