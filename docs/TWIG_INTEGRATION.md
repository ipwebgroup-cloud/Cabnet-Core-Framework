# TWIG_INTEGRATION.md

## Purpose

This phase brings the optional Twig layer into parity with the src-owned PHP presentation layer.

## What is included

- `config/app.php` renderer switch
- `TwigRenderer` using Twig classes when available
- logical template-name mapping from `.php` to `.twig`
- src-owned Twig layouts and partials
- src-owned built-in admin/public Twig pages
- src-owned starter CRUD Twig templates
- compatibility wrappers in `app/Views/twig`

## How to enable Twig

1. Install Twig through Composer:
   - `composer require twig/twig`

2. Switch the renderer in `config/app.php`:
   - `'renderer' => 'twig'`

3. Add Composer autoloading to the project bootstrap if not already present.

## Important behavior

Controllers and CRUD controllers can keep rendering logical template names such as:

- `admin/login.php`
- `public/home.php`
- `admin/services/index.php`

When the Twig renderer is active, those logical names are mapped automatically to:

- `admin/login.twig`
- `public/home.twig`
- `admin/services/index.twig`

This keeps controller code renderer-agnostic.

## Current default

The framework still defaults to:

- `PhpRenderer`

This keeps the starter cPanel-safe and dependency-light by default.

## Why this matters

Twig can now become the preferred rendering path without forcing controller rewrites or leaving the src-owned presentation migration incomplete.
