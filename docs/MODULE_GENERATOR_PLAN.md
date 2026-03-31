# MODULE_GENERATOR_PLAN.md

## Purpose

This document defines the next-level module generator direction for Cabnet Core.

## Current phase

The framework currently includes:
- generic CRUD rendering
- entity definitions
- named routes
- a naming/blueprint helper generator

## Next generator goals

A future generator should be able to create:

- definition class
- repository class
- service class
- controller class
- PHP CRUD wrapper views
- route snippets
- service registration snippets
- schema starter

## Generator modes

### 1. Dry-run mode
Outputs the planned file names and content previews.

### 2. File-write mode
Actually writes the generated files to the project.

### 3. Docs mode
Also updates:
- roadmap
- changelog
- project docs

## Safety rules

- never overwrite files silently
- generate predictable names
- preserve manual edits
- keep generator output simple and auditable

## Recommendation

Start with a file-preview generator before building automatic file-writing behavior.
