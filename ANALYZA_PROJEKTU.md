# SPHERE — Analýza e-shopu na prodej obuvi

## Obsah

1. Popis projektu
2. Architektura — návrhový vzor MVC
3. Databázový model
4. Technologický stack
5. Struktura webu
6. SCRUM — harmonogram prací
7. Product Backlog
8. Bezpečnost
9. Nasazení
10. Diagram toku aplikace

---

## 1. Popis projektu

**SPHERE** je prémiový e-shop zaměřený na prodej sportovní a lifestylové obuvi. Zákazník si může prohlédnout produkty rozdělené do kolekcí (Muži / Ženy), filtrovat je podle ceny a dostupnosti, přidat zboží do košíku ve vybrané velikosti a dokončit objednávku s automatickým předvyplněním doručovacích údajů z uživatelského profilu.

### Klíčové funkce


| Oblast    | Funkce                                                            |
| --------- | ----------------------------------------------------------------- |
| Katalog   | Výpis produktů, filtrování (cena, dostupnost), řazení       |
| Produkt   | Výběr velikosti (dostupné / vyprodané), více fotografií     |
| Košík   | Přidání položek (session), pokladna, shrnutí objednávky     |
| Uživatel | Registrace, přihlášení, správa profilu, přehled objednávek |
| Admin     | CRUD produktů, správa skladu dle velikosti                      |

---

## 2. Architektura — návrhový vzor MVC

Projekt striktně dodržuje vzor **MVC** prostřednictvím frameworku **Nette**:


| Vrstva         | Adresář         | Popis                                  |
| -------------- | ----------------- | -------------------------------------- |
| **Model**      | `app/Model/`      | Datová vrstva, business logika        |
| **View**       | `app/templates/`  | Latte šablony — prezentační vrstva |
| **Controller** | `app/Presenters/` | Nette Presenter (obdoba Controller)    |

### 2.1 Model — datová vrstva


| Soubor                | Odpovědnost                                                   |
| --------------------- | -------------------------------------------------------------- |
| `ShoeService.php`     | CRUD produktů, kolekce muži/ženy, správa skladu velikostí |
| `CartService.php`     | Session košík, uložení objednávky do DB                   |
| `UserService.php`     | Čtení a aktualizace profilu uživatele                       |
| `CustomerService.php` | Zákazník a objednávka v tabulce`zakaznik`                   |
| `Authenticator.php`   | Autentizace — bcrypt hash, role admin/user                    |

### 2.2 Controller — Presentery


| Presenter           | URL                          | Funkce                                      |
| ------------------- | ---------------------------- | ------------------------------------------- |
| `HomepagePresenter` | `/`                          | Úvodní strana — hero, CTA sekce, výpisy |
| `MenPresenter`      | `/men`                       | Kolekce Muži s filtrováním               |
| `WomenPresenter`    | `/women`                     | Kolekce Ženy s filtrováním               |
| `ProductPresenter`  | `/product/<id>`              | Detail produktu, přidání do košíku     |
| `CartPresenter`     | `/cart`                      | Košík, pokladna, odeslání objednávky   |
| `SignPresenter`     | `/sign/in`, `/sign/register` | Přihlášení a registrace                 |
| `AccountPresenter`  | `/account`                   | Přehled objednávek, nastavení profilu    |
| `AdminPresenter`    | `/admin`                     | Správa produktů (role: admin)             |
| `ErrorPresenter`    | —                           | Obsluha chyb 4xx / 500                      |

### 2.3 View — šablony (Latte)

```
app/templates/
├── @layout.latte              ← Sdílený layout (nav, footer, search)
├── Homepage/default.latte
├── Men/default.latte
├── Women/default.latte
├── Product/detail.latte
├── Cart/default.latte
├── Sign/in.latte
├── Sign/register.latte
├── Account/default.latte
├── Admin/default.latte
├── Admin/edit.latte
├── Admin/add.latte
└── Error/4xx.latte
    Error/500.latte
```

> [!tip] Latte — výhody šablonovacího systému
>
> - **Automatické escapování** všech výstupů (ochrana před XSS)
> - **Dědičnost bloků** — `{block content}`, `{block footer}`
> - **Integrace s Nette formuláři** přes atributy `n:name`
> - Podmínky, cykly a proměnné syntaxí `{if}`, `{foreach}`, `{var}`

---

## 3. Databázový model

### Schéma tabulek

#### `boty` — produkty


| Sloupec                | Typ           | Popis                      |
| ---------------------- | ------------- | -------------------------- |
| `IDB`                  | INT PK AUTO   | Primární klíč          |
| `shoeName`             | VARCHAR(2000) | Název produktu            |
| `popisek`              | MEDIUMTEXT    | Popis                      |
| `price`                | INT           | Cena v EUR                 |
| `img1`, `img2`, `img3` | VARCHAR(200)  | Cesty k obrázkům         |
| `available`            | TINYINT(1)    | Dostupnost (1 = dostupný) |

#### `shoe_sizes` — sklad podle velikostí


| Sloupec   | Typ         | Popis                   |
| --------- | ----------- | ----------------------- |
| `id`      | INT PK AUTO | Primární klíč       |
| `shoe_id` | INT FK      | Odkaz na`boty.IDB`      |
| `size`    | FLOAT       | Velikost (38.0 – 47.0) |
| `stock`   | INT         | Počet kusů na skladě |

#### `users` — zákaznické účty


| Sloupec                  | Typ          | Popis               |
| ------------------------ | ------------ | ------------------- |
| `id`                     | INT PK AUTO  | Primární klíč   |
| `name`, `email`          | VARCHAR      | Jméno a email      |
| `password`               | VARCHAR(255) | Bcrypt hash hesla   |
| `phone`, `address1/2`    | VARCHAR      | Doručovací údaje |
| `city`, `country`, `zip` | VARCHAR      | Doručovací údaje |
| `created_at`             | DATETIME     | Datum registrace    |

#### `zakaznik` — objednávky


| Sloupec                                         | Typ         | Popis                         |
| ----------------------------------------------- | ----------- | ----------------------------- |
| `idZ`                                           | INT PK AUTO | Primární klíč             |
| `Cname`, `surname`, `email`                     | VARCHAR     | Kontaktní údaje             |
| `phone`, `address1/2`, `city`, `country`, `zip` | VARCHAR     | Doručovací adresa           |
| `payment_method`                                | VARCHAR(50) | Způsob platby                |
| `user_id`                                       | INT FK      | Odkaz na`users.id` (nullable) |
| `created_at`                                    | DATETIME    | Datum objednávky             |

#### `order_items` — položky objednávky


| Sloupec                | Typ           | Popis                    |
| ---------------------- | ------------- | ------------------------ |
| `id`                   | INT PK AUTO   | Primární klíč        |
| `order_id`             | INT FK        | Odkaz na`zakaznik.idZ`   |
| `shoe_id`, `shoe_name` | INT / VARCHAR | Produkt                  |
| `size`                 | FLOAT         | Objednaná velikost      |
| `price`                | INT           | Cena v době objednávky |
| `img`                  | VARCHAR(200)  | Obrázek produktu        |

### ER diagram

```
users ──────────────────────── zakaznik
  id ──────────── user_id FK       idZ
                                    │
                               order_items
                                 order_id FK
                                 shoe_id  FK ──── boty
                                                   IDB
                                                    │
                                              shoe_sizes
                                               shoe_id FK
```

> [!note] Rozlišení velikosti a barvy
>
> - **Velikost** je řešena tabulkou `shoe_sizes` — každá kombinace produkt × velikost má vlastní záznam s počtem kusů na skladě.
> - **Barva** je součástí názvu produktu (např. *"Air Max DN 'Black Dark Grey'"*). Pro budoucí rozšíření by stačilo přidat tabulku `shoe_colors` se stejným vzorem jako `shoe_sizes`.

---

## 4. Technologický stack


| Vrstva         | Technologie                   | Důvod volby                                             |
| -------------- | ----------------------------- | -------------------------------------------------------- |
| Backend        | PHP 8.2 + Nette Framework 3.x | MVC, DI container, bezpečné formuláře (CSRF), router |
| Šablony       | Latte                         | Automatické escapování, bloky, integrace s Nette      |
| Databáze      | MariaDB 10                    | Relační DB, plná kompatibilita s MySQL                |
| Frontend       | Tailwind CSS (CDN)            | Utility-first CSS, bez build kroku                       |
| Kontejnerizace | Docker + Docker Compose       | Reprodukovatelné prostředí                            |
| Webový server | Apache (v Docker image)       | Standardní pro PHP projekty                             |

---

## 5. Struktura webu

```
/                    Úvodní stránka (hero, CTA sekce, výpisy produktů)
/men                 Kolekce Muži (filtr ceny, řazení, dostupnost)
/women               Kolekce Ženy (filtr ceny, řazení, dostupnost)
/product/<id>        Detail produktu (galerie, výběr velikosti, košík)
/cart                Košík + pokladna
/sign/in             Přihlášení
/sign/register       Registrace
/account             Moje objednávky + nastavení profilu
/admin               Administrace (pouze role admin)
/admin/edit/<id>     Úprava produktu
/admin/add           Přidání produktu
```

---

## 6. SCRUM — harmonogram prací

### Složení týmu


| Člen   | Role               | Zaměření                           |
| ------- | ------------------ | ------------------------------------- |
| Člen 1 | Frontend / Design  | Šablony (Latte), Tailwind CSS, UX/UI |
| Člen 2 | Backend / PHP      | Presentery, Model vrstva, autentizace |
| Člen 3 | Databáze / DevOps | Návrh schématu, migrace, Docker     |

---

### Sprint 0 — Příprava *(1 týden)*

**Cíl:** Nastavení prostředí, definice architektury, databázový návrh


| ID   | Úkol                                              | Role | Priorita     |
| ---- | -------------------------------------------------- | ---- | ------------ |
| S0-1 | Nastavení Docker prostředí (app + db + adminer) | DB   | 🔴 Vysoká   |
| S0-2 | Inicializace Nette projektu, DI konfigurace        | BE   | 🔴 Vysoká   |
| S0-3 | Návrh ER diagramu a SQL schématu                 | DB   | 🔴 Vysoká   |
| S0-4 | Seedovací data (produkty, velikosti)              | DB   | 🟡 Střední |
| S0-5 | Definice barevné palety a typografie              | FE   | 🟡 Střední |
| S0-6 | Wireframe hlavních stránek                       | FE   | 🟡 Střední |

---

### Sprint 1 — Základ katalogu *(2 týdny)*

**Cíl:** Zobrazení produktů, navigace, sdílený layout


| ID   | Úkol                                               | Role                        | Stav      |
| ---- | --------------------------------------------------- | --------------------------- | --------- |
| S1-1 | Shared layout (`@layout.latte`) — navigace, footer | FE                          | ✅ Hotovo |
| S1-2 | Homepage — hero sekce, CTA sekce Muži/Ženy       | FE                          | ✅ Hotovo |
| S1-3 | Stránky Muži / Ženy — grid produktů            | FE                          | ✅ Hotovo |
| S1-4 | `ShoeService` — getMen(), getWomen(), getById()    | BE                          | ✅ Hotovo |
| S1-5 | Routing (RouterFactory)                             | BE                          | ✅ Hotovo |
| S1-6 | Latte filtr`                                        | img` pro cesty k obrázkům | BE        |

---

### Sprint 2 — Detail produktu a košík *(2 týdny)*

**Cíl:** Výběr velikosti, přidání do košíku, session košík


| ID   | Úkol                                                   | Role  | Stav      |
| ---- | ------------------------------------------------------- | ----- | --------- |
| S2-1 | Detail produktu — layout, galerie obrázků            | FE    | ✅ Hotovo |
| S2-2 | Výběr velikosti — dlaždice (dostupné / vyprodané) | FE    | ✅ Hotovo |
| S2-3 | Formulář "Přidat do košíku" (Nette Form + CSRF)    | BE    | ✅ Hotovo |
| S2-4 | Tabulka`shoe_sizes`, getSizes(), getAvailableSizes()    | DB/BE | ✅ Hotovo |
| S2-5 | Session košík — přidat, odebrat, výpočet ceny     | BE    | ✅ Hotovo |
| S2-6 | Stránka košíku — výpis položek, shrnutí          | FE    | ✅ Hotovo |

---

### Sprint 3 — Uživatelé a objednávky *(2 týdny)*

**Cíl:** Registrace, přihlášení, dokončení objednávky


| ID   | Úkol                                                   | Role  | Stav      |
| ---- | ------------------------------------------------------- | ----- | --------- |
| S3-1 | Registrace a přihlašovací formuláře                | FE/BE | ✅ Hotovo |
| S3-2 | Autentizace (bcrypt, role admin/user)                   | BE    | ✅ Hotovo |
| S3-3 | Pokladna — formulář s doručovacími údaji          | FE    | ✅ Hotovo |
| S3-4 | Uložení objednávky (`zakaznik` + `order_items`)      | BE    | ✅ Hotovo |
| S3-5 | Auto-předvyplnění košíku z uživatelského profilu | BE    | ✅ Hotovo |
| S3-6 | Uložení adresy po dokončení objednávky             | BE    | ✅ Hotovo |
| S3-7 | Stránka Můj účet — přehled objednávek            | FE/BE | ✅ Hotovo |

---

### Sprint 4 — Admin panel a rozšíření *(2 týdny)*

**Cíl:** Správa produktů, filtrování, finální polish


| ID   | Úkol                                                | Role  | Stav      |
| ---- | ---------------------------------------------------- | ----- | --------- |
| S4-1 | Admin dashboard — přehled produktů                | FE/BE | ✅ Hotovo |
| S4-2 | Přidat / upravit produkt (sklad dle velikostí)     | FE/BE | ✅ Hotovo |
| S4-3 | Ochrana admin sekce (kontrola role v Presenteru)     | BE    | ✅ Hotovo |
| S4-4 | Filtrování produktů (cena, řazení, dostupnost)  | FE    | ✅ Hotovo |
| S4-5 | Vyhledávání — search overlay (klientská strana) | FE    | ✅ Hotovo |
| S4-6 | Error stránky (4xx, 500) s SPHERE brandingem        | FE    | ✅ Hotovo |
| S4-7 | Flash zprávy (success / error notifikace)           | FE/BE | ✅ Hotovo |

---

## 7. Product Backlog

### Epic 1 — Katalog produktů

- Jako zákazník chci vidět produkty rozdělené podle pohlaví (Muži / Ženy)
- Jako zákazník chci filtrovat produkty podle cenového rozsahu
- Jako zákazník chci seřadit produkty (výchozí, cena ↑, cena ↓)
- Jako zákazník chci vidět, které velikosti jsou dostupné a které vyprodané
- Jako zákazník chci vyhledat produkt podle názvu přes search overlay

### Epic 2 — Nákupní košík

- Jako zákazník chci přidat produkt v konkrétní velikosti do košíku
- Jako zákazník chci vidět obsah košíku s jednotkovými i celkovou cenou
- Jako zákazník chci odebrat položku z košíku
- Jako zákazník chci dokončit objednávku zadáním doručovacích údajů

### Epic 3 — Uživatelský účet

- Jako zákazník chci se zaregistrovat pomocí emailu a hesla
- Jako zákazník chci se přihlásit a zůstat přihlášen (session)
- Jako přihlášený zákazník chci mít automaticky předvyplněné doručovací údaje
- Jako přihlášený zákazník chci vidět historii svých objednávek s detailem

### Epic 4 — Administrace

- Jako administrátor chci přidat nový produkt s obrázky a popisem
- Jako administrátor chci upravit název, cenu a dostupnost produktu
- Jako administrátor chci spravovat skladové zásoby pro každou velikost
- Jako administrátor chci označit produkt jako nedostupný (bez smazání)

---

## 8. Bezpečnost


| Hrozba                   | Implementované řešení                                         |
| ------------------------ | ----------------------------------------------------------------- |
| SQL Injection            | Nette Database Explorer — parametrické dotazy, žádné raw SQL |
| XSS                      | Latte — automatické escapování všech výstupů`{$variable}`  |
| CSRF                     | Nette Forms — skrytý token v každém formuláři               |
| Neoprávněný přístup | Kontrola`$this->user->isInRole('admin')` + redirect               |
| Hashování hesel        | `password_hash()` s algoritmem bcrypt                             |
| Session fixation         | Nette Security — regenerace session ID po přihlášení         |

---

## 9. Nasazení

### Docker Compose konfigurace

```yaml
services:
  app:      # PHP 8 + Apache, zdrojový kód baked do image
  db:       # MariaDB 10, init.sql spuštěn při prvním startu
  adminer:  # Webové rozhraní pro správu databáze (port 8080)
```

### Spuštění projektu

```bash
# První spuštění / po změně kódu
docker compose up -d --build

# Web dostupný na
http://localhost

# Adminer (správa DB) na
http://localhost:8080
```

> [!warning] Důležité
> Nette kompiluje šablony a DI container do adresáře `temp/cache/`. Po každém nasazení je nutné sestavit nový Docker image (`--build`), nebo vymazat cache příkazem:
>
> ```bash
> docker exec sphere-app-1 sh -c "rm -rf /var/www/html/temp/cache/*"
> ```

---

## 10. Diagram toku aplikace

```
Zákazník
│
├─ Prohlíží katalog ───→ MenPresenter / WomenPresenter
│                              └─→ ShoeService.getMen() / getWomen()
│
├─ Otevře detail ──────→ ProductPresenter.renderDetail()
│                              ├─→ ShoeService.getById()
│                              └─→ ShoeService.getSizes()
│
├─ Přidá do košíku ────→ ProductPresenter.handleAddToCartForm()
│                              └─→ CartService.add() → session
│
├─ Dokončí objednávku ─→ CartPresenter.handleCheckoutForm()
│                              ├─→ CustomerService.createOrder()
│                              └─→ UserService.updateProfile()
│
└─ Přihlásí se ────────→ SignPresenter.handleLoginForm()
                               └─→ Authenticator.authenticate()
```
