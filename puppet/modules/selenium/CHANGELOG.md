
#### [Current]
 * [4f74a38](../../commit/4f74a38) - __(Joshua Hoblitt)__ Merge pull request [#52](../../issues/52) from jhoblitt/maint/plumbing

Maint/plumbing
 * [6e27f73](../../commit/6e27f73) - __(Joshua Hoblitt)__ Merge pull request [#51](../../issues/51) from jhoblitt/i11-user-managehome

I11 user managehome
 * [659cb9c](../../commit/659cb9c) - __(Joshua Hoblitt)__ update rspec-puppet gem version to ~> 2.2

For bugfixes and to allow the 2.x version to float as the API stability has
been better than with the 1.x series.

 * [9380db4](../../commit/9380db4) - __(Joshua Hoblitt)__ remove default nodset symlink

To resolve this PMT error:
    Puppet::ModuleTool::Errors::ModuleToolError: Found symlinks. Symlinks in modules are not allowed, please remove them.

 * [b8ba280](../../commit/b8ba280) - __(Joshua Hoblitt)__ add puppet-blacksmith gem
 * [80638ae](../../commit/80638ae) - __(Joshua Hoblitt)__ set selenium user home to /var/lib/selenium
 * [ad7e240](../../commit/ad7e240) - __(Joshua Hoblitt)__ make selenium user/group "system" accounts
 * [c569f5b](../../commit/c569f5b) - __(Joshua Hoblitt)__ Merge pull request [#50](../../issues/50) from jhoblitt/bugfix/hub-and-node-on-same-host

acceptance test coverage of hub+node on the same host
 * [97f1fe2](../../commit/97f1fe2) - __(Joshua Hoblitt)__ add acceptance test coverage of combined hub+node
 * [9b69415](../../commit/9b69415) - __(Joshua Hoblitt)__ add $::osfamily == debian unit test coverage for selenium::config
 * [44d05e7](../../commit/44d05e7) - __(Ilja Bobkevic)__ Manage users home
 * [afe1d8c](../../commit/afe1d8c) - __(Joshua Hoblitt)__ Merge pull request [#46](../../issues/46) from anho/bugfix/node-and-hub-on-same-server

make file for init-script unique
 * [56db553](../../commit/56db553) - __(anho)__ make file for init-script unique

#### v1.0.0
 * [e2ed805](../../commit/e2ed805) - __(Joshua Hoblitt)__ Merge pull request [#44](../../issues/44) from jhoblitt/feature/v1.0.0

Feature/v1.0.0
 * [cc7128f](../../commit/cc7128f) - __(Joshua Hoblitt)__ bump version to v1.0.0
 * [3e5db68](../../commit/3e5db68) - __(Joshua Hoblitt)__ add travis_lint rake target
 * [d162ebe](../../commit/d162ebe) - __(Joshua Hoblitt)__ update operatingsystem support list include Debian
 * [f6d8406](../../commit/f6d8406) - __(Joshua Hoblitt)__ add Puppet Version Compatibility subsection to README
 * [c17c100](../../commit/c17c100) - __(Joshua Hoblitt)__ refresh selenium class API description in README
 * [415b08b](../../commit/415b08b) - __(Joshua Hoblitt)__ fix typos in README
 * [ba7bf9d](../../commit/ba7bf9d) - __(Joshua Hoblitt)__ add Contributing section to README
 * [54bccb7](../../commit/54bccb7) - __(Joshua Hoblitt)__ Merge pull request [#43](../../issues/43) from jhoblitt/feature/jar_2.45.0

update default selenium jar version to 2.45.0
 * [47ed216](../../commit/47ed216) - __(Joshua Hoblitt)__ Merge pull request [#42](../../issues/42) from jhoblitt/jshholland-debian-support

Jshholland debian support
 * [f1784fc](../../commit/f1784fc) - __(Joshua Hoblitt)__ update default selenium jar version to 2.45.0
 * [3c1962b](../../commit/3c1962b) - __(Joshua Hoblitt)__ fix linter errors
 * [4e11d86](../../commit/4e11d86) - __(Joshua Hoblitt)__ fix whitespace errors
 * [c08252d](../../commit/c08252d) - __(Joshua Hoblitt)__ add debian 7.8 nodeset
 * [bc82b72](../../commit/bc82b72) - __(Josh Holland)__ Fix acceptance tests for Ubuntu
 * [8288b64](../../commit/8288b64) - __(Josh Holland)__ Fix test failures
 * [eec17bf](../../commit/eec17bf) - __(Doug Fitzmaurice)__ Resource ordering and lint fixes

Added ensure_package call, and explicit ordering to ensure it runs
before the init script is generated.

Added a default block to the switch.

 * [a6c688e](../../commit/a6c688e) - __(Markus Rekkenbeil)__ Fix debian init.d template

To avoid errors with update-rc.d / insserv and a grid + node setup
 * [4456cf6](../../commit/4456cf6) - __(Doug Fitzmaurice)__ Test fixes for OS support
 * [b0b97a0](../../commit/b0b97a0) - __(Doug Fitzmaurice)__ Preliminary Debian support

Added a new init.d file for Debian, which requires the "daemon" package.T
This will be automatically required on Debian systems.

 * [ed0e4dc](../../commit/ed0e4dc) - __(Joshua Hoblitt)__ Merge pull request [#40](../../issues/40) from tphoney/certificate_readme

Add a small note to highlight certificate errors
 * [bc1e3ba](../../commit/bc1e3ba) - __(tphoney)__ Add a small note to highlight certificate errors

small ammendment

typo

 * [c467278](../../commit/c467278) - __(Joshua Hoblitt)__ add ruby 2.2 to travis matrix
 * [6521084](../../commit/6521084) - __(Joshua Hoblitt)__ Merge pull request [#39](../../issues/39) from jhoblitt/feature/puppet-4.0

Feature/puppet 4.0
 * [4c04106](../../commit/4c04106) - __(Joshua Hoblitt)__ pull fixtures repos via https
 * [18c08a5](../../commit/18c08a5) - __(Joshua Hoblitt)__ add :validate to default rake target list
 * [773e16b](../../commit/773e16b) - __(Joshua Hoblitt)__ add metadata-json-lint gem

Needed by rake metadata.json validation target.

 * [349df92](../../commit/349df92) - __(Joshua Hoblitt)__ set stdlib requirement to 4.6.0
 * [8422351](../../commit/8422351) - __(Joshua Hoblitt)__ Merge pull request [#38](../../issues/38) from jhoblitt/feature/puppet-4.0

Feature/puppet 4.0
 * [e9a3823](../../commit/e9a3823) - __(Joshua Hoblitt)__ update travis matrix puppet 3.x minimum version to 3.7
 * [e8c9607](../../commit/e8c9607) - __(Joshua Hoblitt)__ update rspec-puppet gem version to ~> 2.1.0

For compatibility with puppet 4.0.0

 * [d351157](../../commit/d351157) - __(Joshua Hoblitt)__ add puppet 4.0 to travis matrix
 * [d4a870f](../../commit/d4a870f) - __(Joshua Hoblitt)__ remove puppet 2.7 from travis matrix

4.0.0 has been released; support major release -1

 * [456cf28](../../commit/456cf28) - __(Joshua Hoblitt)__ Merge pull request [#37](../../issues/37) from jhoblitt/maint/test_infra

Maint/test infra
 * [f7638c3](../../commit/f7638c3) - __(Joshua Hoblitt)__ update logrotate fixture ref to fix future parser compatibility

The current forge release (1.1.1) of logrotate is not compatible with the
future parser. See: https://github.com/rodjek/puppet-logrotate/pull/39

 * [b347154](../../commit/b347154) - __(Joshua Hoblitt)__ add junit/ to .gitiginore

Generated by beaker 5

 * [9b43c9c](../../commit/9b43c9c) - __(Joshua Hoblitt)__ update LICENSE -<year> to 2015
 * [9376db4](../../commit/9376db4) - __(Joshua Hoblitt)__ update Rakefile to latest boilerplate
 * [5e0b5ba](../../commit/5e0b5ba) - __(Joshua Hoblitt)__ update spec_helper_acceptance.rb to latest boilerplate
 * [9e307e1](../../commit/9e307e1) - __(Joshua Hoblitt)__ update beaker nodesets to use current chef/bento boxes
 * [0293a55](../../commit/0293a55) - __(Joshua Hoblitt)__ update .travis.yml to latest boilerplate
 * [c519431](../../commit/c519431) - __(Joshua Hoblitt)__ update .gitignore to latest boilerplate
 * [9879dc0](../../commit/9879dc0) - __(Joshua Hoblitt)__ pin rspec on Ruby 1.8.7 (rspec/rspec-core[#1864](../../issues/1864))
 * [43f085a](../../commit/43f085a) - __(Joshua Hoblitt)__ use rspec-puppet 2.0.0 from gems instead of git
 * [153115f](../../commit/153115f) - __(Joshua Hoblitt)__ add FACTER_GEM_VERSION to Gemfile
 * [1dee44e](../../commit/1dee44e) - __(Joshua Hoblitt)__ Merge pull request [#34](../../issues/34) from Super-Visions/feature/optional_user_management

Make user management optional if user is already managed elsewhere
 * [183368e](../../commit/183368e) - __(Thomas Casteleyn)__ Added new options documentation to README file
 * [ba8450a](../../commit/ba8450a) - __(Joshua Hoblitt)__ Merge pull request [#35](../../issues/35) from Super-Visions/feature/fix_java_path

Use SLNM_JAVA variable
 * [2911fa6](../../commit/2911fa6) - __(Thomas Casteleyn)__ Make user management optional if user is already managed elsewhere
 * [d423106](../../commit/d423106) - __(Thomas Casteleyn)__ Use SLNM_JAVA variable
 * [1c6a1b0](../../commit/1c6a1b0) - __(Joshua Hoblitt)__ Merge pull request [#27](../../issues/27) from zivan/master

fixed issue [#19](../../issues/19) SSL error with wget
 * [c5b1dd2](../../commit/c5b1dd2) - __(zivan)__ add unit test for the param nocheckcertificate
 * [de88fd8](../../commit/de88fd8) - __(zivan)__ update order params
 * [f75685c](../../commit/f75685c) - __(zverev)__ fixed issue [#19](../../issues/19) SSL error with wget

#### v0.3.0
 * [0426075](../../commit/0426075) - __(Joshua Hoblitt)__ Merge pull request [#32](../../issues/32) from jhoblitt/feature/v0.3.0

Feature/v0.3.0
 * [6215f01](../../commit/6215f01) - __(Joshua Hoblitt)__ bump version to v0.3.0
 * [6090f31](../../commit/6090f31) - __(Joshua Hoblitt)__ update fixtures stdlib to 4.2.0
 * [1260914](../../commit/1260914) - __(Joshua Hoblitt)__ replace Modulefile vs metadata.json
 * [eb0f399](../../commit/eb0f399) - __(Joshua Hoblitt)__ update rspec-puppet to v2.0.0
 * [4d50d4a](../../commit/4d50d4a) - __(Joshua Hoblitt)__ Merge pull request [#31](../../issues/31) from jhoblitt/feature/jar_2.44.0

Feature/jar 2.44.0
 * [1b74629](../../commit/1b74629) - __(Joshua Hoblitt)__ update beaker nodesets from puppet-module_skel
 * [85a9295](../../commit/85a9295) - __(Joshua Hoblitt)__ update default selenium jar version to 2.44.0
 * [e28b863](../../commit/e28b863) - __(Joshua Hoblitt)__ Merge pull request [#30](../../issues/30) from jhoblitt/feature/travis_1.8.7

fix travis ruby 1.8.7 runs
 * [b5a9110](../../commit/b5a9110) - __(Joshua Hoblitt)__ enable travis container based builds
 * [6b8949f](../../commit/6b8949f) - __(Joshua Hoblitt)__ fix travis ruby 1.8.7 runs

By excluding beaker related gems that pull in deps now incompatible with
1.8.7.

 * [6310705](../../commit/6310705) - __(Joshua Hoblitt)__ Merge pull request [#28](../../issues/28) from tayzlor/future-parser-fixes

Fixes required for compatibility with 3.7.x puppet and future parser
 * [b0b38f1](../../commit/b0b38f1) - __(Graham Taylor)__ Fixes required for compatibility with 3.7.x puppet and future parser
 * [62f74d6](../../commit/62f74d6) - __(Joshua Hoblitt)__ Merge pull request [#22](../../issues/22) from jhoblitt/feature/future_parser

add future parser to travis matrix
 * [37d72d4](../../commit/37d72d4) - __(Joshua Hoblitt)__ add future parser to travis matrix
 * [aaa9adc](../../commit/aaa9adc) - __(Joshua Hoblitt)__ Merge pull request [#18](../../issues/18) from jhoblitt/feature/beaker_update

Feature/beaker update
 * [0e51725](../../commit/0e51725) - __(Joshua Hoblitt)__ ignore puppet warnings during acceptance tests
 * [cd44a66](../../commit/cd44a66) - __(Joshua Hoblitt)__ ignore beaker log dir

#### v0.2.3
 * [889e77c](../../commit/889e77c) - __(Joshua Hoblitt)__ Merge pull request [#16](../../issues/16) from jhoblitt/feature/v0.2.3

Feature/v0.2.3
 * [3be4bcc](../../commit/3be4bcc) - __(Joshua Hoblitt)__ bump version to v0.2.3
 * [e12054a](../../commit/e12054a) - __(Joshua Hoblitt)__ remove all in class/define parameter docs

Replace with URLs to section anchors in README

 * [b81f8fa](../../commit/b81f8fa) - __(Joshua Hoblitt)__ fix linter warnings
 * [d9efd4d](../../commit/d9efd4d) - __(Joshua Hoblitt)__ Merge pull request [#15](../../issues/15) from jhoblitt/feature/jar_2.42.1

update default selenium jar version to 2.42.1
 * [3adad1c](../../commit/3adad1c) - __(Joshua Hoblitt)__ update default selenium jar version to 2.42.1
 * [c46f6eb](../../commit/c46f6eb) - __(Joshua Hoblitt)__ Merge pull request [#14](../../issues/14) from jhoblitt/feature/beaker

convert from rspec-systerm -> beaker
 * [c62e058](../../commit/c62e058) - __(Joshua Hoblitt)__ convert from rspec-systerm -> beaker

+ update acceptance tests

 * [c500ad7](../../commit/c500ad7) - __(Joshua Hoblitt)__ Merge pull request [#13](../../issues/13) from enxebre/defaul-url
 * [51e71cb](../../commit/51e71cb) - __(Joshua Hoblitt)__ fix new download URL path version component

The new download URL has the major.minor version as a path component but
excludes the .patch. Eg.

    https://selenium-release.storage.googleapis.com/<major>.<minor>/selenium-server-standalone-<major>.<minor>.<patch>.jar

 * [931a531](../../commit/931a531) - __(alamela)__ fixing specs
 * [b9aa097](../../commit/b9aa097) - __(Enxebre)__ default url on https
 * [39ac34b](../../commit/39ac34b) - __(Enxebre)__ changing borken default url

#### v0.2.2
 * [d43f99c](../../commit/d43f99c) - __(Joshua Hoblitt)__ Merge pull request [#12](../../issues/12) from jhoblitt/feature/v0.2.2

Feature/v0.2.2
 * [36ecd00](../../commit/36ecd00) - __(Joshua Hoblitt)__ bump version to v0.2.2
 * [5f10d31](../../commit/5f10d31) - __(Joshua Hoblitt)__ minor README grammar tweak
 * [48bc2ce](../../commit/48bc2ce) - __(Joshua Hoblitt)__ change README MD so class parameters are a subsection

Changing the class parameters markup from a list to a
sub-sub-sub-sub-subsection creates a direct HTML anchor that is useful
to cut'n'paste as a URL.

 * [8a40c58](../../commit/8a40c58) - __(Joshua Hoblitt)__ Merge pull request [#11](../../issues/11) from jhoblitt/feature/jar-2.41.0

update default selenium jar version to 2.41.0
 * [c6daae6](../../commit/c6daae6) - __(Joshua Hoblitt)__ update default selenium jar version to 2.41.0

#### v0.2.1
 * [fa644ee](../../commit/fa644ee) - __(Joshua Hoblitt)__ Merge pull request [#10](../../issues/10) from jhoblitt/feature/v0.2.1

bump version to v0.2.1
 * [a19c6ca](../../commit/a19c6ca) - __(Joshua Hoblitt)__ bump version to v0.2.1
 * [a93d574](../../commit/a93d574) - __(Joshua Hoblitt)__ update rspec to cover PR [#9](../../issues/9)
 * [e070184](../../commit/e070184) - __(Joshua Hoblitt)__ Merge pull request [#9](../../issues/9) from KlavsKlavsen/master
 * [eb5c321](../../commit/eb5c321) - __(Joshua Hoblitt)__ update .gitignore
 * [57625be](../../commit/57625be) - __(Klavs Klavsen)__ ensure selenium log folder is writable by its owner
 * [3d59e1d](../../commit/3d59e1d) - __(Joshua Hoblitt)__ fix a few README typos
 * [4c04708](../../commit/4c04708) - __(Joshua Hoblitt)__ add a standalone vs grid blurb to the README

Based on question raised in https://github.com/jhoblitt/puppet-selenium/issues/7

 * [a917ef6](../../commit/a917ef6) - __(Joshua Hoblitt)__ update copyright notice year (-2014)

#### v0.2.0
 * [392f32f](../../commit/392f32f) - __(Joshua Hoblitt)__ bump version to v0.2.0
 * [8ac1df0](../../commit/8ac1df0) - __(Joshua Hoblitt)__ add validation + rspec of selenium class download_timeout param
 * [4c446af](../../commit/4c446af) - __(Joshua Hoblitt)__ update boilerplate .gitignore
 * [e881e3e](../../commit/e881e3e) - __(Joshua Hoblitt)__ add selenium class download_timeout param to README
 * [f46c358](../../commit/f46c358) - __(Rogério Prado Colferai)__ Corrected lint erros
 * [5d475f2](../../commit/5d475f2) - __(Rogério Prado Colferai)__ Passing the timeout as parameter for Selenium class
 * [7739a50](../../commit/7739a50) - __(Rogério Prado Colferai)__ Passing the timeout as parameter for Selenium class
 * [cdc6f18](../../commit/cdc6f18) - __(Joshua Hoblitt)__ trim travis test matrix

#### v0.1.6
 * [cade4b2](../../commit/cade4b2) - __(Joshua Hoblitt)__ bump version to v0.1.6
 * [db19171](../../commit/db19171) - __(Joshua Hoblitt)__ update README Files section
 * [89cb5d8](../../commit/89cb5d8) - __(Joshua Hoblitt)__ Merge pull request [#4](../../issues/4) from jhoblitt/system_tests

System tests
 * [169cfcd](../../commit/169cfcd) - __(Joshua Hoblitt)__ fix selenium group not being created

As of puppet 3.4.0, the group type will not create/manage a group without
`ensure => present` being set.

 * [83d4c56](../../commit/83d4c56) - __(Joshua Hoblitt)__ add basic rspec-system tests
 * [9f55770](../../commit/9f55770) - __(Joshua Hoblitt)__ Merge pull request [#3](../../issues/3) from jhoblitt/jar_2.39.0

Jar 2.39.0
 * [0b42040](../../commit/0b42040) - __(Joshua Hoblitt)__ update default selenium jar version to 2.39.0
 * [b1afa01](../../commit/b1afa01) - __(Joshua Hoblitt)__ fix rspec-puppet 1.0.1 deprecation warning

DEPRECATION: include_class is deprecated. Use contain_class instead.

#### v0.1.5
 * [083d2c8](../../commit/083d2c8) - __(Joshua Hoblitt)__ bump version to v0.1.5
 * [e01802f](../../commit/e01802f) - __(Joshua Hoblitt)__ README grammar tweak
 * [5635de0](../../commit/5635de0) - __(Joshua Hoblitt)__ README typo
 * [1eb2f7e](../../commit/1eb2f7e) - __(Joshua Hoblitt)__ add module deps to see also section of README
 * [cb9e84d](../../commit/cb9e84d) - __(Joshua Hoblitt)__ minor README tweaks

#### v0.1.4
 * [b22e8d2](../../commit/b22e8d2) - __(Joshua Hoblitt)__ bump version to v0.1.4
 * [469d78a](../../commit/469d78a) - __(Joshua Hoblitt)__ fix README markdown formatting
 * [c691874](../../commit/c691874) - __(Joshua Hoblitt)__ add example profiles/roles to README

#### v0.1.3
 * [2a2b481](../../commit/2a2b481) - __(Joshua Hoblitt)__ bump version to v0.1.3
 * [53d8cfe](../../commit/53d8cfe) - __(Joshua Hoblitt)__ update default selenium jar file version to 2.37.0 in README

#### v0.1.2
 * [bc57dfe](../../commit/bc57dfe) - __(Joshua Hoblitt)__ bump version to v0.1.2
 * [f12845f](../../commit/f12845f) - __(Joshua Hoblitt)__ update default selenium server version to 2.37.0

#### v0.1.1
 * [d9677b1](../../commit/d9677b1) - __(Joshua Hoblitt)__ bump version to v0.1.1
 * [4ca0a1e](../../commit/4ca0a1e) - __(Joshua Hoblitt)__ Merge pull request [#2](../../issues/2) from xiankai/refresh_service

Refresh service if the options are changed
 * [ed4f9d5](../../commit/ed4f9d5) - __(KJ)__ Refresh service if the options are changed
 * [e0476a3](../../commit/e0476a3) - __(Joshua Hoblitt)__ remove experimental github based puppet forge publishing
 * [7e3c232](../../commit/7e3c232) - __(Joshua Hoblitt)__ enable experimental github based puppet forge publishing

#### v0.1.0
 * [52a25ca](../../commit/52a25ca) - __(Joshua Hoblitt)__ bump version to v0.1.0
 * [ceacf8f](../../commit/ceacf8f) - __(Joshua Hoblitt)__ add a logrotate::rule for $log_path
 * [0fc5e6c](../../commit/0fc5e6c) - __(Joshua Hoblitt)__ remove ruby-head / puppet ~> 3.2.1 & ~> 3.3.0 from travis ci matrix
 * [ec20f7e](../../commit/ec20f7e) - __(Joshua Hoblitt)__ update doc formatting
 * [ddf0f14](../../commit/ddf0f14) - __(Joshua Hoblitt)__ fix selenium::hub doc examples

#### v0.0.1
 * [3a69948](../../commit/3a69948) - __(Joshua Hoblitt)__ fix README ToC anchors
 * [37d806b](../../commit/37d806b) - __(Joshua Hoblitt)__ fix README typos
 * [4170146](../../commit/4170146) - __(Joshua Hoblitt)__ doc/README overhaul
 * [3be7688](../../commit/3be7688) - __(Joshua Hoblitt)__ be consistent about Rakefile quoting style
 * [803c0f0](../../commit/803c0f0) - __(Joshua Hoblitt)__ fix anchor(s)
 * [cc6075d](../../commit/cc6075d) - __(Joshua Hoblitt)__ reuse the default options from servers on nodes
 * [eedad0c](../../commit/eedad0c) - __(Joshua Hoblitt)__ whitespace
 * [a4d77e7](../../commit/a4d77e7) - __(Joshua Hoblitt)__ add class selenium::node
 * [df6cc77](../../commit/df6cc77) - __(Joshua Hoblitt)__ add class selenium::hub
 * [0904127](../../commit/0904127) - __(Joshua Hoblitt)__ admit defeat and use the anchor pattern in selenium::server
 * [c96870b](../../commit/c96870b) - __(Joshua Hoblitt)__ mv service resource into selenium::config to prevent circular deps
 * [ba59bb7](../../commit/ba59bb7) - __(Joshua Hoblitt)__ merge class selenium::service into selenium::server
 * [558c335](../../commit/558c335) - __(Joshua Hoblitt)__ rename selenium::server init.d from seleniumstandalone -> seleniumserver
 * [8523e66](../../commit/8523e66) - __(Joshua Hoblitt)__ rename class selenium::install -> selenium and refactor it as common base
 * [50e63e6](../../commit/50e63e6) - __(Joshua Hoblitt)__ convert class selenium::config into a define

This is to allow selenium::config to create multiple init.d scripts in
the future for selenium hub and grid nodes.

 * [d742263](../../commit/d742263) - __(Joshua Hoblitt)__ interpolate $prog in init.d script
 * [80373a6](../../commit/80373a6) - __(Joshua Hoblitt)__ minor rspec cleanups
 * [2aeebe7](../../commit/2aeebe7) - __(Joshua Hoblitt)__ cleanup init.d template variable interpolation
 * [45e77c2](../../commit/45e77c2) - __(Joshua Hoblitt)__ add $display param to class selenium::server

Selects the X display to use

 * [68d1861](../../commit/68d1861) - __(Joshua Hoblitt)__ increase wget timeout 30s -> 90s

To avoid failures seen due to lag spikes

 * [9148bbf](../../commit/9148bbf) - __(Joshua Hoblitt)__ fix wget download destination
 * [2164d59](../../commit/2164d59) - __(Joshua Hoblitt)__ mv user setup from selenium::server -> selenium::install

To break a dependency cycle between wget::fetch and user.

 * [93670c4](../../commit/93670c4) - __(Joshua Hoblitt)__ fix whitespace
 * [45c06af](../../commit/45c06af) - __(Joshua Hoblitt)__ tie selenium::{install,config,service} together in selenium::server
 * [e0bc408](../../commit/e0bc408) - __(Joshua Hoblitt)__ add class selenium::service
 * [dcf5ebf](../../commit/dcf5ebf) - __(Joshua Hoblitt)__ add class selenium::config
 * [69e8010](../../commit/69e8010) - __(Joshua Hoblitt)__ whitespace fix
 * [5071e9c](../../commit/5071e9c) - __(Joshua Hoblitt)__ add class selenium::install
 * [f4f887b](../../commit/f4f887b) - __(Joshua Hoblitt)__ download selenium jars as the selenium user
 * [b08a9b9](../../commit/b08a9b9) - __(Joshua Hoblitt)__ add selenium::install class with simple download support
 * [eb724de](../../commit/eb724de) - __(Joshua Hoblitt)__ stub out selenium::{server,params} classes
 * [edc5da0](../../commit/edc5da0) - __(Joshua Hoblitt)__ Merge puppet-module_skel
 * [0c59736](../../commit/0c59736) - __(Joshua Hoblitt)__ first commit
