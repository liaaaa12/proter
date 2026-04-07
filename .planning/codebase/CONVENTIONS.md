# Code Conventions

## Architectural Patterns
- Follow standard MVC paradigm for PHP components.
- Rely on Inertia.js for state communication between Laravel backend and React UI layer.

## React Patterns
- Strongly favor Functional Components with hooks (`useState`, `useEffect`, `useRef`, `useCallback`).
- Minimize usage of `useEffect` for data-polling dependencies to avoid re-rendering race conditions, favor callbacks (like `onStop` triggers).
- Class components are not allowed.

## Python Patterns
- Microservices structured with FastAPI utilizing asynchronous endpoints (`async def`) when file I/O operations are occurring.
- ML models globally memoized in memory and explicitly loaded during the `lifespan` hook.
