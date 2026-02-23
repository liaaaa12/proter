# Coding Conventions

## PHP / Laravel

- **PSR-12**: Following standard PHP PSR-12 coding styles.
- **Naming**:
    - Controllers: `CamelCaseController.php`
    - Models: `CamelCase.php` (Singular)
    - Migrations: `yyyy_mm_dd_id_description.php`
- **Patterns**:
    - Use Eloquent models for database interaction.
    - Keep controllers slim, using Services or Actions for complex logic (though not extensively seen yet).

## JavaScript / React

- **Component structure**: Functional components with custom hooks in `resources/js/`.
- **Styling**: Utility-first CSS using Tailwind.
- **State Management**: React state and custom hooks (Inertia handles server state).

## Python

- **Scripts**: Procedural and class-based scripts for specific processing tasks.
- **Naming**: `snake_case.py`.
