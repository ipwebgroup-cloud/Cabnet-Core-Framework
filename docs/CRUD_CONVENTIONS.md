# CRUD_CONVENTIONS.md

## Purpose

This document defines the starter CRUD pattern for Cabnet Core.

## Standard CRUD routes

- `GET /entity`
- `GET /entity/create`
- `POST /entity`
- `GET /entity/{id}/edit`
- `POST /entity/{id}/update`
- `POST /entity/{id}/delete`

## Standard layers

### Controller
Handles:
- route entry
- redirect/flash flow
- CSRF checks
- view rendering

### Service
Handles:
- business rules
- validation orchestration
- repository coordination

### Repository
Handles:
- SQL
- data persistence
- entity lookups

### Views
Should include:
- list
- create
- edit

## Standard temporary form state

Use session-backed keys:
- `_old_input`
- `_validation_errors`

Managed through:
- `ViewState`

## Starter conventions

- CSRF token on all mutating forms
- redirect after create/update/delete
- flash messages for outcomes
- repository methods keep SQL centralized
- route params use `{id}` placeholders
- views should be layout-based, not standalone pages

## Next evolution

Later phases can convert this into:
- reusable CRUD base controller
- entity metadata definitions
- auto-generated form field rendering
- route naming and URL helpers
