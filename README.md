The Backstage FOS User Bundle
=============================

The backstage fos user bundle provides a user provider for the backstage core bundle, based on the popular FOS user bundle.
If this is your first time installing the backstage please consider using the [standard distribution](https://github.com/bkstg/standard-distribution) instead.

[![Build Status](https://travis-ci.org/bkstg/fos-user-bundle.svg?branch=master)](https://travis-ci.org/bkstg/fos-user-bundle)
[![Latest Stable Version](https://poser.pugx.org/bkstg/fos-user-bundle/v/stable)](https://packagist.org/packages/bkstg/fos-user-bundle)
[![Total Downloads](https://poser.pugx.org/bkstg/fos-user-bundle/downloads)](https://packagist.org/packages/bkstg/fos-user-bundle)
[![License](https://poser.pugx.org/bkstg/fos-user-bundle/license)](https://packagist.org/packages/bkstg/fos-user-bundle)

Requirements
------------

This bundle relies on several contributed bundles to function, these are required in the composer.json for this bundle but will require you to configure them correctly (see the [standard distribution](https://github.com/bkstg/standard-distribution) for default configurations):

* [friendsofsymfony/user-bundle](https://packagist.org/packages/friendsofsymfony/user-bundle)
* [midnightluke/group-security-bundle](https://packagist.org/packages/midnightluke/group-security-bundle)
* [midnightluke/php-units-of-measure-bundle](https://packagist.org/packages/midnightluke/php-units-of-measure-bundle)

Additionally this bundle (and all backstage bundles) requires the doctrine ORM to function, providing entities and configuration to work with these bundles, as well as the twig templating engine.

Documentation
-------------

* [User documentation](https://github.com/bkstg/fos-user-bundle/wiki): managing users, roles, etc.
* [Developer documentation](https://github.com/bkstg/fos-user-bundle/tree/master/Resources/doc/index.md): installation, configuration, etc.
