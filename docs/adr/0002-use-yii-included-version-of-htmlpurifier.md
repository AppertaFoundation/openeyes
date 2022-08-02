# 2. Use Yii included version of HTMLPurifier

Date: 2022-07-15

## Status

Accepted

## Context

The [ezyang/htmlpurifier](https://github.com/ezyang/htmlpurifier) library is included within the Yii framework, rather than as a composer dependency. The `phpoffice/phpspreadsheet` includes the library as a composer dependency. This leads to a class name collision when both are used in the same application request context.

## Decision

We will use the composer replace spec to indicate that OpenEyes provides the `ezyang/htmlpurifier` library directly, preventing it from being downloaded as a vendor package.

## Consequences

### Code implications

To ensure that the `HtmlPurifier` classes are available across the project, we must update the `index.php` to import the Yii standalone version as though autoloading from the vendor package.

This must also be done in the phpunit bootstrap.

This ensures that we use a version of the code that Yii is compatible with.

### Risks

As time goes by, the version of `HtmlPurifier` that `phpoffice/phpspreadsheet` requires may no longer be compatible with that of the version included with Yii. 

If this arises, we would need to raise a PR with Yii to bump the version. However, if the package has significantly changed, this PR may be difficult to get into the official release.

Given the maturity of `HtmlPurifier` this risk should be relatively low.