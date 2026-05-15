# 🤖 AGENTS.md — AccessAgadir

This file describes the project structure, conventions, and instructions for AI coding agents (Claude, Copilot, Cursor, etc.) working on this codebase.

---

## 📌 Project Overview

**AccessAgadir** is a Laravel web application that helps people with reduced mobility navigate the city of Agadir (Morocco). It features an interactive accessibility map, detailed place sheets, route planning, community obstacle reporting, and an AI assistant.

---

## 🧰 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2 · Laravel 11 |
| Frontend | Blade templates · Alpine.js · TailwindCSS |
| Map | Leaflet.js · OpenStreetMap · OSRM (routing) |
| Database | MySQL |
| AI | OpenAI API (GPT-4o) — called server-side only |
| Build | Vite (`npm run dev` / `npm run build`) |

---

## 📁 Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── HomeController.php
│       ├── MapController.php
│       ├── PlaceController.php
│       ├── ReviewController.php
│       ├── ObstacleController.php
│       ├── AiController.php
│       └── Admin/
│           └── ObstacleController.php
├── Models/
│   ├── Place.php
│   ├── Category.php
│   ├── Review.php
│   └── Obstacle.php
database/
├── migrations/
└── seeders/
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php       # Main layout (navbar, footer, Vite assets)
│   ├── home/
│   │   └── index.blade.php     # Landing page
│   ├── map/
│   │   └── index.blade.php     # Interactive map (Leaflet + Alpine.js)
│   ├── places/
│   │   └── show.blade.php      # Place detail sheet
│   └── admin/
│       └── obstacles.blade.php # Obstacle moderation
├── css/
│   └── app.css                 # Tailwind directives
└── js/
    └── app.js                  # Alpine.js + Vite entry
routes/
└── web.php                     # All application routes
```

---

## 🗺️ Routes Reference

```php
// Public
GET  /                          HomeController@index
GET  /map                       MapController@index
GET  /places/{id}               PlaceController@show

// API (JSON)
GET  /api/places                PlaceController@index
POST /places/{id}/reviews       ReviewController@store
POST /obstacles                 ObstacleController@store
POST /ai/recommend              AiController@recommend

// Admin
GET   /admin/obstacles          Admin\ObstacleController@index
PATCH /admin/obstacles/{id}     Admin\ObstacleController@updateStatus
```

---

## 🗃️ Database Schema

```sql
categories
  id, name, icon, created_at, updated_at

places
  id, category_id, name, address, lat, lng,
  rating, description,
  wheelchair BOOLEAN,
  ramp BOOLEAN,
  elevator BOOLEAN,
  adapted_toilet BOOLEAN,
  pmr_parking BOOLEAN,
  created_at, updated_at

reviews
  id, place_id, rating (1-5), comment, created_at, updated_at

obstacles
  id, lat, lng, type, description,
  severity ENUM(low, medium, high),
  status ENUM(pending, approved, rejected),
  created_at, updated_at
```

---

## ⚙️ Key Conventions

### PHP / Laravel
- Controllers are thin — business logic goes in the Model or a Service class if needed
- API endpoints return JSON via `response()->json()`
- All OpenAI calls are made **server-side only** inside `AiController` — never expose the API key to the client
- Use Eloquent relationships: `Place belongsTo Category`, `Place hasMany Review`, `Place hasMany Obstacle` (via proximity, not FK)
- Validation is done inside controllers using `$request->validate()`

### Blade Templates
- All views extend `layouts.app`
- Use `@section('content')` / `@yield('content')` pattern
- Alpine.js state is declared with `x-data` directly on the relevant element
- Leaflet.js is initialized inside a `<script>` block at the bottom of map views, after the map div
- Never use inline JavaScript for API calls — use `fetch()` inside Alpine.js `x-init` or methods

### Alpine.js
- Use `x-data`, `x-show`, `x-on:click`, `x-bind`, `x-init` — avoid `$refs` unless necessary
- Filter state lives in the Alpine.js component on the map sidebar
- Modals (obstacle report, AI panel) use `x-show` with `x-transition`
- Loading states use a boolean flag (e.g., `loading: false`) toggled around `fetch()` calls

### TailwindCSS
- Use utility classes directly in Blade — no custom CSS unless absolutely necessary
- Color palette: `violet-600` (primary), `green-500` (accessible), `red-500` (obstacle/blocked), `gray-100` (backgrounds)
- Mobile-first: always write `sm:` / `md:` breakpoints for layout shifts

---

## 🤖 AI Assistant (AiController)

The AI feature calls OpenAI GPT-4o server-side. The prompt includes:

- Place name, category, and accessibility indicators
- Nearby approved obstacles (within ~500m)
- User's start point (if available)

**Expected AI output:** a short paragraph (3–5 sentences) in French recommending whether the place/route is accessible and what to watch out for.

The endpoint `POST /ai/recommend` accepts:
```json
{
  "place_id": 3,
  "origin_lat": 30.4278,
  "origin_lng": -9.5981
}
```
And returns:
```json
{
  "recommendation": "..."
}
```

---

## 🚨 Obstacle Reporting Flow

1. User clicks "Signaler un obstacle" on the map
2. Alpine.js modal opens — user clicks the map to set GPS position
3. User fills: type, description, severity
4. `POST /obstacles` stores with `status = pending`
5. Admin reviews at `/admin/obstacles` and approves or rejects
6. Approved obstacles appear on the map with a ⚠️ marker

**Obstacle types:** `escalier_bloquant`, `trottoir_casse`, `pente_forte`, `travaux`, `absence_rampe`, `route_dangereuse`

---

## ✅ Definition of Done (Demo Checklist)

- [ ] Map loads with all place markers and obstacle markers
- [ ] Category/accessibility filters work via Alpine.js
- [ ] Place detail page is readable and shows all indicators
- [ ] Route is drawn on the map from origin to destination
- [ ] AI panel shows a relevant recommendation in French
- [ ] Obstacle report modal works end-to-end
- [ ] No console errors during the Sara demo flow
- [ ] App is responsive on mobile

---

## 🚫 Out of Scope (Hackathon MVP)

- User authentication / registration
- Native mobile app
- Real-time public transport integration
- Payment or reservation features
- Full admin dashboard with statistics
- Email notifications

---

## 🛠️ Local Dev Setup

```bash
git clone https://github.com/your-org/access-agadir.git
cd access-agadir

cp .env.example .env
# Fill in: DB_DATABASE, DB_USERNAME, DB_PASSWORD, OPENAI_API_KEY

composer install
php artisan key:generate
php artisan migrate --seed

npm install
npm run dev

php artisan serve
# → http://localhost:8000
```