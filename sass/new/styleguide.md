# Overview

Welcome to the OpenEyes living CSS Styleguide.

The content of this styleguide is generated from the comments found in the Sass files.
We use KSS to format our comments and to generate this styleguide. When adding new styles
or updating existing styles, you **must** keep the comments up-to-date.

This is a living styleguide, and will grow and evolve as the stylesheet grows and evolves.

Use this styleguide to get an understanding of the various UI components we use, and how to add new or change existing styles.

## Introduction

Most elements used throughout the system will be defined in this core stylesheet.
There should be few reasons to override these styles, and if we find we are doing so,
then we should try make the core styles more generic and adapatable rather than
overriding them in the module stylesheets.

## Sass and Compass

We use Sass to develop our CSS, and use compass to provide us with useful mixins. Please
use the compass mixins wherever possible.

### Compass mixins

Compass mixins allow us, for one example, to create cross-browser compatible CSS3,
and thus it's important that we use these mixins wherever possible.

Please be familiar with the following compass mixins:

* @include background();
* @include box-shadow();
* @include image-url();
* @include border-radius();
* @include opacity();
* @include box-sizing();

## Commenting

* Double-slash (Sass) comments will be removed in the compiled CSS.
* Slash-star (CSS) comments will not be removed in the compiled CSS.
* Use KSS style comments! The living styleguide is dependent on the correct
  format of comments. (See http://warpspire.com/kss/)
* Wrap comments after 80 chars.

## Conventions

* When in doubt, follow the conventions you see used in the foundation files.
* 4 spaces for indentation.
* Components should be decoupled from each other.
* Never use ID's for styling.
* Separate rules with new lines.
* Lower-case classnames, words separated by a hyphen. (eg .button-dropdown).
* Use 3 character hexadecimal notation where possible (eg #000).
* Use upper-case characters in hexadecimal notation (eg #3FA522).
* Avoid qualifying class names with type selectors (eg, don't do this: div.myclass).
* Use double-quotes, (eg: font-family: "Helvetica Neue").
* Never change the foundation component files.
* If using Sublime Text text editor, you can use the 'SassBeautify'
  plugin to format your Sass.

## Variables

* Use variables for all values.
* To avoid conlicts with foundation variables, we have to namespace our variables,
  thus you must prefix the variable names with 'oe-'.
* Add default variables values in the component file, and copy those variables
  into the _variables file. This allows use to create different themes in the future
  by simply using a different _variables file.

## Framework

We use the zurb foundation framework as a base for our stylesheet. The foundation framework
is a responsive (mobile first) framework written in Sass, and give us many useful mixins to
use as a base for our UI components.

Most of the zurb classes are not rendered by default, instead we define our own classes and
use the foundation mixins to generate the rules. This allows for a more finely tuned stylesheet
with no bloat.

## The grid

If the interface is to ever be responsive, we *must* use the foundation grid system to layout
the interface. There should be very few reason to position elements without using the grid
system. The foundation grid system is a 12 column grid and allows us to control wrapping of
columns at 3 different screen widths.

## Variables and themeing

All values used in the stylesheet are stored in variables. We use a global '_variables.scss'
file to store the values. This allows us to easily theme the interface by simply changing the values
of these variables.

The following rules have to use ems, and thus you need to use the emCalc() function to convert pixels to ems:

* margin
* padding (unless you're offsetting a background image, then just use pixels)
* font-size