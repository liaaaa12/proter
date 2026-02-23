# Architecture

## Pattern

- **MVC (Laravel)**: Standard Model-View-Controller structure.
- **Frontend SPA**: React components served via Inertia.js (Single Page Application feel without the complexity of a separate API).

## Layers

- **App Layer**: `app/Http/Controllers`, `app/Models`.
- **Database Layer**: `database/migrations`, `database/seeders`, `database/factories`.
- **View Layer**: `resources/js/Pages`, `resources/js/Components`.
- **Script Layer**: `scripts/` contains specialized logic for voice processing (Python).

## Data Flow

1. **Request**: Handled by Laravel routes (`routes/web.php`).
2. **Controller**: Processes logic, potentially calling external scripts or models.
3. **Response**: Returned as an Inertia response with data passed to React props.
4. **Hydration**: React components in `resources/js/` render the UI.
