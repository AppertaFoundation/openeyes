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
2. Install sass: `sudo gem install sass`
3. Install compass: `sudo gem install compass`

## Compiling the front-end

* Run `compass compile` in the root of the project to compile the sass into css.
* Run `compass watch` in the root of the project to auto-compile the css whenever a sass file changes.

The watch command is generally more useful when developing Sass.