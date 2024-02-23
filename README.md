[![Latest Stable Version](http://poser.pugx.org/idetik/coretik-page-builder/v)](https://packagist.org/packages/idetik/coretik-page-builder) [![License](http://poser.pugx.org/idetik/coretik-page-builder/license)](https://github.com/idetik/coretik-page-builder/blob/master/LICENSE.md)
# Pagebuilder with ACF for Coretik

Coretik page builder provides a modern way for developers to build blocks for page builder (and it is WP-CLI friendly !).
It uses framework logic containing reusable components and composite blocks and it manages as many components levels as necessary.

## Overview
Block instance defined all its features :
- rendering method ;
- admin fields and admin preview ;

Three blocks types exists :
- Components : the lower block level, used to build other blocks ;
- Block : a free way to build a complete block instance ;
- Composite : a fast way to build blocks based on many components or others blocks ;

Components doesn't appear in the user library. There are only used to build others blocks.

## Requirements
- PHP >= 8.0
- Composer
- `idetik/coretik` : https://github.com/idetik/coretik
- Plugin ACF : https://www.advancedcustomfields.com/
- Plugin ACF Extended : https://acf-extended.com/

## Installation

`composer require idetik/coretik-page-builder`

## Get started

This builder comes with some components ready to use.

## Features
- Manage blocks with POO & DI
- Generate blocks thumbnails with WP Cli
- Create your own blocks

... Wiki in progress
