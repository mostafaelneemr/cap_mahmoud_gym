# AI Agent Directives
> [!IMPORTANT]
> ANY AI agent must read this document before executing tasks to understand the system's boundaries and design patterns.

# System Overview
This project is a large-scale **Gym Management System** designed to handle gym administration, content, and daily operations. Primary entities revolve around Gym Administration, including features for:
- Trainees
- Workout Plans & Exercises
- Zones, Cities, Countries
- Content Management (Blogs, Sliders, Testimonials, Messages)
- Settings & Configurations

# Database Architecture (CRITICAL)
> [!IMPORTANT]
> The complete database schema, table relations, and structural definitions are located in the DB-structure.sql file at the project root. Always use DB-structure.sql as the absolute source of truth for database architecture before writing queries, migrations, or models.

# Directory Mapping
The project is built on the Laravel framework and structured as follows:
- `app/`
  - `Models/` - Contains Eloquent models (e.g., `Trainee`, `WorkoutPlan`, `Exercise`).
  - `Modules/` - Core domain logic organized by interfaces:
    - `System/` - Backend / Admin controllers and business logic (e.g., `TraineeController`, `WorkoutController`).
    - `Web/` - Frontend website controllers.
  - `Repositories/` - Implements the Repository Design Pattern for data access.
  - `Services/` - Service layer containing reusable business logic and abstractions.
  - `Http/Controllers/` - Core routing controllers.
  - `Enums/` - Type-safe enumerations.
- `routes/` - Application routing.
- `config/` - Application configurations.
- `database/` - Migrations, seeders, and factories.
- `DB-structure.sql` - Absolute source of truth for the database schema.

# Tech Stack & Patterns
- **Framework:** PHP (Laravel ^13.0)
- **Database:** MySQL
- **Frontend/Views:** Blade/HTML (`laravellux/html`), Vite.
- **Other Packages:** Sanctum (Authentication), Bugsnag (Error tracking), Excel (`maatwebsite/excel`), Redis (`predis`), ActivityLog.
- **Architectural Patterns:**
  - **Repository Pattern:** Heavily utilized (`app/Repositories/`) to decouple data access from the business logic. All interactions with Eloquent should go through the repositories.
  - **Modular Architecture:** Grouping controllers by context (`app/Modules/System` and `app/Modules/Web`).
  - **Service Layer:** Used alongside repositories to keep controllers thin (`app/Services/`).

# Core Workflows
Currently, the codebase handles the following Core Workflows:
- **Trainee Management:** Registration, profiles, and associated fitness metrics.
- **Workout & Exercise Processing (Programs):** Creating fitness plans, workout programs, and mapping exercises for trainees.
- **Content & Portal Management:** Handling settings, blogs, sliders, and site configurations.
- **Authentication & Roles:** Session handling and system permission groups.
