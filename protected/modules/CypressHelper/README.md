# Cypress Helper Module

This module MUST NEVER be enabled in a production setting. It provides HTTP accessible routes to create data in the application to support the frontend cypress testing coverage.

## Creating users

The createUser endpoint provides the ability to create a user with the provided set of authitems. This enables users with specific privileges to be created and used in the application.

Auth Items are defined in the `authitem` table. There is a hierarchy with these items that is defined in the `authitemchild` table. Typically only roles should be assigned to users for testing purposes.