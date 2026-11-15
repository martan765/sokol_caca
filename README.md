# ⚽ Sokol Čača — Správa sportovního týmu

Webová aplikace pro správu tréninků, zápasů a docházky hráčů fotbalového oddílu. Postavena na Laravel 12, Blade šablonách a Tailwind CSS.

---

## ✨ Co aplikace umí

- **Tréninky** — plánování tréninků, přehled nadcházejících termínů, mazání
- **Zápasy** — správa zápasů včetně soupeře, místa a výsledku
- **Hlasování o docházce** — každý hráč potvrdí účast (Budu / Nebudu) u každého tréninku a zápasu
- **Dashboard hráče** — přehled vlastní docházky seskupený po měsících, oddělená statistika pro tréninky a zápasy (byl/celkem, %)
- **Dashboard trenéra** — globální tabulka všech hráčů po měsících, rozbalovací detail docházky každého hráče na konkrétní akce
- **Role** — hráč vidí svá data, trenér (admin) vidí vše a může přidávat/mazat akce
- **Registrace a přihlášení** — standardní autentizace s e-mailem a heslem

---

## 🎓 Kontext projektu

Aplikace vznikla jako nástroj pro reálné použití v místním fotbalovém oddílu. Cílem bylo nahradit chaotické sdílení informací přes zprávy a skupinové chaty něčím přehledným, kde trenér vidí kdo přijde a hráči vidí svou vlastní docházku.

Záměrně je to jednoduché — žádný zbytečně složitý systém, jen to co oddíl skutečně potřebuje. Do budoucna počítám s rozšiřováním podle toho, co bude v praxi chybět.

---

## 🛠 Použité technologie

| Vrstva | Technologie |
|--------|------------|
| Backend framework | Laravel 12 (PHP 8.2+) |
| Šablony | Blade |
| CSS framework | Tailwind CSS v3 |
| Interaktivita | Alpine.js |
| Databáze | SQLite (výchozí), případně MySQL |
| Sestavení frontendu | Vite |

---

## 📁 Struktura projektu (vrstvy)

Projekt je rozdělený na čtyři vrstvy. Domain, Application a Infrastructure jsou samostatné Composer balíčky ve složce `src/`, propojené přes `path` repositories v kořenovém `composer.json`.

```
app/                         Presentation (App\)
├── Http/Controllers/        controllery, Admin/ = area „Admin“
├── Http/Requests/           validace formulářů
├── Http/Middleware/         AdminMiddleware
├── Rules/                   vlastní validační pravidla (NoScheduleConflict, UniqueEmail…)
├── Policies/                UserPolicy
└── View/                    view composer, formátování datumů
resources/views/             Blade šablony (pracují jen s ViewModely)

src/Domain/                  sokol/domain (Sokol\Domain\) – čisté PHP
├── Entities/                User, Training, Game, GameType, AttendanceStatus, Attendance
├── Enums/                   Role, AttendanceStatusId, EventType
├── Repositories/            rozhraní repozitářů (+ generické RepositoryInterface)
├── Exceptions/              EntityNotFoundException, DomainRuleViolation
├── Pagination/Page.php
└── Schedule/ScheduleRules.php

src/Application/             sokol/application (Sokol\Application\) – závisí jen na Domain
├── Services/                UserService, TrainingService, GameService, AttendanceService…
├── Dto/                     vstupní DTO (TrainingData, GameData, RegisterUserData…)
├── ViewModels/              výstupy pro šablony (GameView, TrainingView, UserView…)
└── Ports/                   rozhraní pro infrastrukturu (PasswordHasher, UnitOfWork, Clock…)

src/Infrastructure/          sokol/infrastructure (Sokol\Infrastructure\) – Laravel
├── Persistence/Eloquent/    Eloquent modely (*Model), mappery, repozitáře
├── Persistence/EloquentUnitOfWork.php
├── Database/Migrations/     migrace (Code-First)
├── Auth/, Weather/, System/ implementace portů
└── InfrastructureServiceProvider.php   bindingy rozhraní → implementace

tests/                       feature testy + unit testy služeb (tests/Unit/Application)
tests-controllers/           samostatný testovací projekt – unit testy controllerů
```

Závislosti vedou jen směrem dolů:

```
Presentation ──► Application ──► Domain
      │                ▲
      ▼                │
  Framework ◄── Infrastructure
```

Hranice hlídá [deptrac](https://github.com/deptrac/deptrac) (`deptrac.yaml`): Domain a Application nesmí používat Laravel, Presentation nesmí používat Infrastructure.

Výjimky, které jsou konfigurace frameworku:
- `config/auth.php` odkazuje na `UserModel` – Laravel guard potřebuje Eloquent model.
- `LoginRequest` přihlašuje přes `Auth::attempt()` (guard frameworku). Registrace, změna a reset hesla i ověření e-mailu jdou přes `UserService`.
- Seedery a factory v `database/` používají Eloquent modely přímo – jsou to testovací a ukázková data, Laravel je hledá v namespace `Database\`.

---

## 🗄 Datový model

### `users` — uživatelé
| Pole | Typ | Popis |
|------|-----|-------|
| id | int | Identifikátor |
| name | string | Jméno |
| email | string | E-mail (unikátní) |
| password | string | Hashované heslo |
| role | string | `player` / `admin` |

### `trainings` — tréninky
| Pole | Typ | Popis |
|------|-----|-------|
| id | int | Identifikátor |
| training_date | datetime | Datum a čas tréninku |
| location | string | Místo konání |
| description | string | Volitelný popis |

### `games` — zápasy
| Pole | Typ | Popis |
|------|-----|-------|
| id | int | Identifikátor |
| match_type_id | int | Cizí klíč → `game_types` |
| home_team | string | Domácí tým |
| away_team | string | Hostující tým |
| game_date | datetime | Datum a čas zápasu |
| location | string | Místo konání |
| result | string | Výsledek (volitelný) |

### `training_attendance` — docházka na tréninky
| Pole | Typ | Popis |
|------|-----|-------|
| id | int | Identifikátor |
| user_id | int | Cizí klíč → `users` |
| training_id | int | Cizí klíč → `trainings` |
| status_id | int | `1` = budu, `2` = nebudu |
| note | string | Důvod absence (volitelný) |

### `game_attendance` — docházka na zápasy
Stejná struktura jako `training_attendance`, pouze s `game_id` místo `training_id`.

---

## 🚀 Instalace a spuštění

**Požadavky:** PHP 8.2+ (rozšíření pdo_sqlite, sqlite3, mbstring, openssl, fileinfo, curl, zip), Composer, Node.js

```bash
composer install
cp .env.example .env            # výchozí databáze je SQLite
php artisan key:generate
touch database/database.sqlite  # PowerShell: New-Item database\database.sqlite
php artisan migrate --seed
npm install
npm run build                   # nebo npm run dev
php artisan serve
```

Přihlášení: `admin@admin.cz` / `password` (trenér), hráči např. `novak@caca.cz` / `password`.

Pokud `composer install` hlásí, že lock file neodpovídá `composer.json`, spusťte jednou `composer update "sokol/*" deptrac/deptrac`.

Testy a kontrola vrstev:

```bash
php artisan test            # feature + unit testy
composer test:controllers   # unit testy controllerů (tests-controllers/)
composer deptrac            # kontrola závislostí mezi vrstvami
```

---

## 👤 Autor

Developed by idk.
