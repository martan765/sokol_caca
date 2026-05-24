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
| Databáze | MySQL |
| Sestavení frontendu | Vite |

---

## 📁 Struktura projektu

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php   # Dashboardy hráče i trenéra
│   │   ├── TrainingController.php    # Přehled a mazání tréninků
│   │   ├── GameController.php        # Přehled, vytváření a mazání zápasů
│   │   ├── AttendanceController.php  # Ukládání hlasování o docházce
│   │   ├── ProfileController.php     # Správa profilu uživatele
│   │   └── Admin/
│   │       └── TrainingController.php  # Vytváření tréninků (pouze admin)
│   └── Middleware/
│       └── AdminMiddleware.php       # Ochrana admin tras
├── Models/
│   ├── User.php       # Uživatel s relacemi na tréninky a zápasy
│   ├── Training.php   # Model tréninku
│   └── Game.php       # Model zápasu
resources/views/
├── dashboard.blade.php          # Dashboard (hráč i trenér)
├── trainings/index.blade.php    # Přehled tréninků s hlasováním
├── games/index.blade.php        # Přehled zápasů s hlasováním
├── admin/trainings/create.blade.php
├── games/create.blade.php
├── profile/
└── layouts/
    ├── app.blade.php
    ├── guest.blade.php
    └── navigation.blade.php
routes/
├── web.php
└── auth.php
```

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

**Požadavky:** PHP 8.2+, Composer, Node.js 18+, MySQL

```bash
# 1. Stažení projektu
git clone https://github.com/...

# 2. Instalace PHP závislostí
composer install

# 3. Instalace JS závislostí
npm install

# 4. Konfigurace prostředí
cp .env.example .env
php artisan key:generate

# 5. Nastavení databáze v .env
DB_DATABASE=sokol_caca
DB_USERNAME=root
DB_PASSWORD=

# 6. Spuštění migrací
php artisan migrate

# 7. Sestavení frontendu
npm run dev

# nebo pro produkci
npm run build
```

---

## 👤 Autor

Developed by Martin Hábl.
