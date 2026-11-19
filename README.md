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

## 📁 Struktura projektu

Projekt je rozdělený do vrstev podobně jako vzorový projekt UTB.Eshop v C#. Domain, Application a Infrastructure jsou samostatné balíčky v `src/` (každý má svůj `composer.json`), aplikace v `app/` je Presentation.

```
app/                       Presentation – controllery, Admin, FormRequesty, Rules, views
src/Domain/                entity, enumy, rozhraní repozitářů a managerů
  Entities/
  Enums/
  Interfaces/Repositories/
  Interfaces/Managers/
src/Application/           služby, DTO a ViewModely
  Abstraction/             rozhraní služeb (UserServiceInterface, ...)
  Implementation/          služby (UserService, ...)
  Dto/
  ViewModels/
src/Infrastructure/        Laravel/Eloquent
  Persistence/Eloquent/    modely, mappery, repozitáře
  Database/Migrations/
  Database/Seeders/
  Auth/, Weather/, System/
tests/                     feature testy, unit testy služeb
tests-controllers/         testy controllerů (samostatný phpunit.xml)
```

Závislosti:
- Domain nezávisí na ničem
- Application -> Domain
- Infrastructure -> Domain (+ Laravel)
- Presentation -> Application, Domain (+ Laravel)

Kontrola přes deptrac: `composer deptrac`.

Bindingy rozhraní na implementace jsou v `InfrastructureServiceProvider` (repozitáře, managery) a v `AppServiceProvider` (služby).

Poznámky:
- `config/auth.php` odkazuje na `UserModel`, protože Laravel přihlašování potřebuje Eloquent model.
- Factory zůstala v `database/factories`, používá se jen v testech.

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
