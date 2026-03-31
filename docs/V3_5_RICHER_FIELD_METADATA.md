# V3_5_RICHER_FIELD_METADATA.md

## Phase summary

This phase extends canonical CRUD field metadata so the framework can express richer form and validation behavior without forcing controller rewrites or immediate schema overhauls.

## Added capabilities

- upload fields with file/image validation and managed storage persistence
- relation-driven select fields whose options can be hydrated at the service layer
- translatable field payloads with locale-aware validation and rendering
- multipart form rendering derived from entity definitions
- request payload merging for uploaded files

## Key runtime changes

- `Cabnet\Http\Request` now merges normalized `$_FILES` into `input()` payloads
- `Cabnet\Support\UploadManager` persists configured uploads to the public uploads path
- `DefinitionCrudService` can hydrate relation options for form definitions and persist upload/translatable data
- shared PHP and Twig CRUD form partials render upload, relation, and multilingual inputs from metadata

## Generator impact

`CrudScaffoldWriter` now preserves metadata for:
- `translatable`
- `locales`
- `upload`
- `image`
- `accept`
- `max_size_kb`
- `upload_dir`
- `relation`

## Validation coverage

The smoke suite now includes coverage for:
- merged upload input payloads
- multipart form rendering
- rich field rendering for upload/relation/translatable metadata
- definition-driven service persistence for uploads and multilingual payloads
