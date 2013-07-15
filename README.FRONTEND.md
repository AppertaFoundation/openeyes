# Front-end rewrite

(This is an evolving document, and will probably be moved somewhere else in the future.)

## Overview
The front-end rewrite is an effort to rewrite all of the markup and CSS, which will allow 
for better maintainability and flexibility. 

## Conventions

### CSS

* Follow this styleguide: http://google-styleguide.googlecode.com/svn/trunk/htmlcssguide.xml
* Tabs for indentation.
* DO NOT use id's for styling - always use classes.

### Javascript

TODO

## Software dependencies

1. Ensure ruby (and rubygems) is installed on your system
2. Ensure nodejs is installed on your system. 
3. Install sass: `sudo gem install sass`
4. Install compass: `sudo gem install compass`
5. Install grunt-cli: `sudo npm install -g grunt-cli`
6. Install nodejs modules: (ensure you are in the root of the project) `npm install`

## Tasks

### Overview

Grunt is used for running various tasks as part of the front-end development work flow. Please refer to
the Gruntfile.js for a list of tasks.

### Building the front-end

The default grunt task will build the front-end. This involves linting and compiling the code.

Run `grunt` in the root of the project to build the front-end.

### Developing Sass

Run `grunt watch` in the root of the project to auto-compile the css when a sass file changes. 