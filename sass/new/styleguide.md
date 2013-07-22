# Overview

Welcome to the OpenEyes living CSS Styleguide.

The content of this styleguide is generated from the comments found in the Sass files.
We use KSS to format our comments and to generate this styleguide. When adding new styles
or updating existing styles, you must keep the comments up-to-date.

This is a living styleguide, and will grow and evolve as the stylesheet grows and evolves.

Use this styleguide to get an understanding of the various UI components we use.

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