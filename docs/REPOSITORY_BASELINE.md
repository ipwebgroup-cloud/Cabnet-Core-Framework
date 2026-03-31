# REPOSITORY_BASELINE.md

## Purpose

This file defines how the framework bundle should be treated in a repository.

## Recommended repository root contents

- framework code
- docs
- scripts
- tests
- database schema
- generated examples
- manifest

## Good practice

Treat the repository as:

- framework baseline
- reusable starter
- versioned source-of-truth

## Avoid

- mixing multiple unrelated project apps into the same framework repository
- keeping unstable project content in the baseline framework repo
- using the framework repo as a dumping ground for product-specific hacks

## Recommended branching approach

- `main` = stable framework baseline
- project forks or branches = project-specific customization
