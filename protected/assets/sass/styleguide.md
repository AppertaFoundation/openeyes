# Overview

Welcome to the OpenEyes living CSS Styleguide. This styleguide contains common components
that are used throughout the application.

The content of this styleguide is generated from the comments found in the Sass files.

## Introduction

Most elements used throughout the system will be defined in this core stylesheet.

## Sass and Compass

We use Sass to develop our CSS, and use compass to provide us with useful mixins. Please
use the compass mixins wherever possible.

### Compass mixins

Please be familiar with the following compass mixins:

* `@include background();`
* `@include box-shadow();`
* `@include image-url();`
* `@include border-radius();`
* `@include opacity();`
* `@include box-sizing();`

## Commenting

* Double-slash (Sass) comments will be removed in the compiled CSS.
* Slash-star (CSS) comments will not be removed in the compiled CSS.
* Use KSS style comments! The living styleguide is dependent on the correct
  format of comments. (See http://warpspire.com/kss/)
* Wrap comments after 80 chars.

## Conventions

Conventions allow for code readability and maintainability. You must follow
the following conventions if you want to make changes to the stylesheet.

* When in doubt, follow the conventions you see used in the foundation files.
* Tabs for indentation.
* Components should be decoupled from each other.
* Never use inline styles.
* Never use `!important`.
* Never use ID's for styling. This avoids specificity issues.
* Separate rules with new lines, eg:

        .grid {
            margin: 10em;
            padding: 1em;
        }

* Lower-case classnames, words separated by a hyphen. (eg `.button-dropdown`).
* Use an object orientated approach. Don't name your subclasses with
  a prefix of the class you're extending. For example, if you're adding a secondary
  button style, the class list will be: 'button secondary' and NOT 'button button-secondary'.
* Always use semantic and descriptive classnames that describe the content, NOT the style.
  (eg, `.button.primary`, not `.button.blue`)
* Use 3 character hexadecimal notation where possible (eg `#000`).
* Use lower-case characters in hexadecimal notation (eg `#3fa522`).
* Avoid qualifying class names with type selectors (eg, don't do this: `div.myclass`).
* Keep your selectors short! Try to keep your selectors one level deep. If you don't couple
  components, this should be easy to achieve. For example, this is bad because you've coupled
  the main-nav to the header, and you've nested your selector:

        .header {
            /* ... */
        }
        .header .main-nav {
            /* ... */
        }

  This is good:

        .header {
            /* ... */
        }
        .main-nav {
            /* ... */
        }

* Always use double-quotes, (eg: `font-family: "Helvetica Neue"`).
* Always quote attribute values in attribute selectors, (eg: [type="submit"])
* Never change the foundation component files.
* Use percentages instead of pixels or em units when adjusting the dimensions of
  layout containers. This allows the layout to be responsive and adapt to different
  screen sizes.
* If using Sublime Text text editor, you can use the 'SassBeautify'
  plugin to format your Sass.

## Variables

* To avoid conlicts with foundation variables, we have to namespace our variables,
  thus you must prefix the variable names with 'oe-'.
* Add default variables values in the components file, and copy those variables
  into the settings file (which will override the defaults).

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