OphInVisualfields
=================

This description requires extra information for the Import standard fields section.
Using the Openeyes Vagrant vm might help run the legacy import below faster.

Importing Legacy Events: OpenEyes
=================================

Requirements:

- Latest version of the OphInVisualfields module and an OpenEyes instance to support it.
- OpenEyes has already been set up, and all necessary modules imported and necessary database migrations ran.

The legacy import script in the OphInVisualfields/commands directory is used to import .fmes files.
For help on the command, simply run (from protected/):

	./yiic importlegacyvf help

The arguments are very much similar to the import performed on the client, only with differently named options:

	--importDir: specifies which directory to import legacy files from;
	--archiveDir: move successfully imported measurements to specified directory;
	--errorDir: where to move files that were not successfully imported;
	--dupDir: where to place files that have already been imported to OpenEyes
	--interval: an acceptable time span that exists between tests for a given patient; specified as a PHP time interval (e.g. PT1H30M);

Create folders required by import process

	1 - ssh the VM - vagrant ssh
	2 - Create directories
		cd /var/www/protected; \
		mkdir runtime/fields;  \
		mkdir runtime/fields/legacy; \
		mkdir runtime/fields/out; \
		mkdir runtime/fields/err; \
		mkdir runtime/fields/dups;
	3 - copy test legacy fmes files  - cp  modules/OphInVisualfields/tests/fields/legacy/* runtime/fields/legacy/
	4 - These files have sample numbers that might not match your patient ID, change the number part in patient ids in the xml to map a user in your system


Run the command below to import the legacy events:

        ./yiic importlegacyvf import --importDir=runtime/fields/legacy --archiveDir=runtime/fields/out --errorDir=runtime/fields/err --dupDir=runtime/fields/dups

Note that if import is successful a list of legacy events will appear under "Legacy events" on the patien's Episodes and events page.
Also the command informs of the result for each attempted import

Importing Standard Fields
=========================

(This is not tested and left only for future reference)

Once all legacy field events have been imported, standard (that is, non-legacy) fields can be imported. The import process for standard imports is very different to the legacy import method - standard fields are sent to OpenEyes using HTTP to access the OpenEyes API.

In order to import the fields via the API, a new user must be set up, via the admin (localhost/admin) page. You can use a current user but it's best to set up a fields user specifically for this task. This can be done via the admin interface. The user will need 3 roles when created, API access, Admin and User. "Yes" should be chosen for "doctor". The config/local/common.php needs to be updated, using  'local_users' => array('admin','fields_user').

Again, like the legacy import, there are several folders that are used to marshal files into: -a (archive, where successfully transferred files are moved to), -d (directory to watch for newly arrived files from the fields machine), -e (error directory) and -u (duplicate directory), -t (outgoing - when a file is not sent due to a network error it is moved here to be re-scheduled for sending). There are also some extra arguments to take into account:

 -c,--credentials <arg>: Supply (necessary) username/password (comma separated); if no password is given after the comma it is prompted for when the process runs;
 -g,--global-search-path <arg>: Specify the location of the imagemagick installation; necessary only on Windows installations.
 -i,--interval <arg>: Time in seconds to sleep between checks for the in-directory.
 -o,--image-options <arg>: Set up by default but can be parameterised. Specify location and segment of humphrey test to extract, along with scaling parameters. Format: x,y,w,h,x1,y1 where x,y is the the location to cut image with wxh size, scaled to x1,y1. Scaling parameters (x1,y1) are optional and can be omitted.
 -p,--port <arg>: Port to connect to on server. Default 80. Local 8888.
 -r,--regex <arg>: Regular expression to determine valid patient identifiers. Defaults to ^([0-9]{1,9})$
 -s,--host <arg>: Specify server to send messages to.
 -t,--outgoing <arg>: Directory to place measurement files that were not successfully sent.
 -u,--duplicates <arg>: Duplicate files (successfully transferred) are moved to this directory.
 -x,--xml-source: Include XML source file information with captured data. Default is false.

For example, to send fields to the server 'openeyes' with username fields_user and password admin, with ImageMagick being installed in
ImageMagick-6.8.8-Q16, the following would import all file from fields/in and post them to the server:


sh target/appassembler/bin/fields -d fields/in -e fields/err -u fields/dups -a fields/out -t fields/resend -c fields_user,admin -s localhost -p 8888
Note that the credentials could have been specified as -c fields, and the password will be prompted for at the command line.