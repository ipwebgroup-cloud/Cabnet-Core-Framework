# TWIG_INTEGRATION.md

## Purpose

This phase adds the first real Twig-capable integration point.

## What is included

- `config/app.php` renderer switch
- `TwigRenderer` using Twig classes when available
- `renderer` service now supports `php` or `twig`

## How to enable Twig

1. Install Twig through Composer:
   - `composer require twig/twig`

2. Switch the renderer in `config/app.php`:
   - `'renderer' => 'twig'`

3. Add Composer autoloading to the project bootstrap if not already present.

## Current default

The framework still defaults to:
- `PhpRenderer`

This keeps the starter cPanel-safe and dependency-light by default.

## Why this matters

Twig can now become the preferred rendering path without changing controllers, services, or repositories.
