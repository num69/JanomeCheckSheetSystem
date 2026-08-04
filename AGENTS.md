# AGENTS

Guidance for AI coding agents working in this repository.

## Quick Start

- Start local stack: `docker-compose up -d`
- Build frontend bundles: `npm run build`
- Watch JS bundles during development: `npm run watch`
- Lint PHP files quickly: `php -l path/to/file.php`
- Do not run `npm test` (placeholder script that always fails).

## Vendor Library Versions (assets/vendor)

Versions below are read from bundled vendor file headers in `assets/vendor`.

| Library | Version | Source in vendor |
|---|---|---|
| jQuery | 3.6.0 | [assets/vendor/jquery/jquery.min.js](assets/vendor/jquery/jquery.min.js) |
| Bootstrap | 4.6.2 | [assets/vendor/bootstrap/css/bootstrap.min.css](assets/vendor/bootstrap/css/bootstrap.min.css), [assets/vendor/bootstrap/js/bootstrap.bundle.min.js](assets/vendor/bootstrap/js/bootstrap.bundle.min.js) |
| Chart.js | 2.9.4 | [assets/vendor/chartjs/Chart.min.js](assets/vendor/chartjs/Chart.min.js) |
| Select2 | 4.0.13 | [assets/vendor/select2/js/select2.min.js](assets/vendor/select2/js/select2.min.js) |
| SweetAlert2 | 11.26.25 | [assets/vendor/sweetalert2/sweetalert2.all.min.js](assets/vendor/sweetalert2/sweetalert2.all.min.js) |
| Font Awesome Free | 5.15.4 | [assets/vendor/fontawesome/css/all.min.css](assets/vendor/fontawesome/css/all.min.css) |
| DataTables (core) | 1.13.7 | [assets/vendor/datatables/js/jquery.dataTables.min.js](assets/vendor/datatables/js/jquery.dataTables.min.js) |
| DataTables Buttons | 2.4.2 | [assets/vendor/datatables/js/dataTables.buttons.min.js](assets/vendor/datatables/js/dataTables.buttons.min.js) |
| DataTables Responsive | 2.5.0 | [assets/vendor/datatables/js/dataTables.responsive.min.js](assets/vendor/datatables/js/dataTables.responsive.min.js) |
| JSZip | 3.10.1 | [assets/vendor/datatables/js/jszip.min.js](assets/vendor/datatables/js/jszip.min.js) |

Notes:
- `air-datepicker` CSS file in [assets/vendor/air-datepicker/css/air-datepicker.css](assets/vendor/air-datepicker/css/air-datepicker.css) does not expose a version header.
- Some integration/minified CSS files in `assets/vendor/datatables/css/` and `assets/vendor/select2/css/` do not expose version headers.

## Architecture Map

- Server-rendered pages:
  - Root entrypoints in [index.php](index.php), [home.php](home.php), [login.php](login.php), [logout.php](logout.php)
  - Feature pages in [pages/](pages/) (for example [pages/warehouse/warehouseFgStock.php](pages/warehouse/warehouseFgStock.php))
- JSON API endpoints:
  - Auth endpoints in [api/auth/](api/auth/)
  - Warehouse endpoints in [api/warehouse/](api/warehouse/)
- Shared PHP includes:
  - Auth/session/layout in [include/](include/)
  - API helpers in [api/include/](api/include/)
- Frontend assets:
  - Source JS in [assets/js/src/](assets/js/src/)
  - Built JS in [assets/js/](assets/js/)
  - CSS in [assets/css/](assets/css/)

## Project Conventions

- Use `require_once` with `__DIR__`-based paths for includes.
  - Example pattern: [include/auth.php](include/auth.php)
- Page protection pattern:
  - Set `$baseUrl`, then include [include/auth.php](include/auth.php) at the top of protected pages.
- API protection pattern:
  - Include [api/include/authorized.php](api/include/authorized.php) and call `authorized()` early.
- API response pattern:
  - Return JSON through helpers in [api/include/response.php](api/include/response.php) (`jsonResponseOk`, `jsonResponseBadRequest`, etc.).
- Session behavior:
  - Session name and cookie path are configured in [include/session.php](include/session.php).

## Pitfalls

- Linux path case-sensitivity applies in dev/runtime containers; keep include/import casing exact.
- Docker uses mounted volumes, but runtime behavior depends on service config in [docker-compose.yml](docker-compose.yml).
- PHP timezone is configured in [.docker/php/php.ini](.docker/php/php.ini) (`Asia/Bangkok`); keep date handling consistent with API expectations.
- In page files, keep asset registration style consistent (`$pageScripts`, `$pageStyles`), see [pages/warehouse/warehouseFgStock.php](pages/warehouse/warehouseFgStock.php).

## Reference Examples

- API validation + response flow: [api/warehouse/getStockFgSummary.php](api/warehouse/getStockFgSummary.php)
- API pagination/query flow: [api/warehouse/getStockFgHistory.php](api/warehouse/getStockFgHistory.php)
- Protected page structure: [pages/warehouse/warehouseFgStock.php](pages/warehouse/warehouseFgStock.php)
