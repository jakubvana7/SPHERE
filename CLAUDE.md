# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SPHERE is a PHP/HTML shoe e-commerce website. It uses plain HTML/CSS/JS for the frontend and PHP with a MariaDB/MySQL database for the backend. There is no build system or package manager.

## Running the Project

Requires a local PHP + MySQL/MariaDB server (e.g. XAMPP or MAMP):
- Place the project in the web server's document root (e.g. `htdocs/`)
- Import `exct/vanaj.sql` into a MySQL database named `vanaj`
- Access via `http://localhost/SPHERE/`

## Architecture

- **`index.html`** — Homepage with hero video, product listings for men/women
- **`men.html` / `woman.html`** — Category pages
- **`dynamic.php`** — Product detail page; reads shoe data from DB via `$_GET` params and PHP functions (`shoeName()`, `image1/2/3()`, `price()`)
- **`cart.php`** — Shopping cart and checkout; uses `$_SESSION` to store cart items and mysqli to write orders to DB
- **`about.php`** — About page with contact form that writes to DB
- **`style.css`** — Single shared stylesheet for all pages
- **`sricpt.js`** — Shared JS (note: intentional typo in filename)
- **`exct/vanaj.sql`** — Database schema and seed data (MariaDB dump)

## Database

Database name: `vanaj`. Main table: `boty` (shoes). DB credentials are hardcoded inline in each PHP file (no shared config file). When modifying DB connections, update each PHP file individually.
