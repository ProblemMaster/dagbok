# Dagbok

En enkel träningsdagbok med en Lumen-backend och en Vue 3 / Vite-frontend.

**Projektstruktur**
- `Backend/`: Lumen API och PDF-export
- `Frontend/`: Vue 3-app (Vite)

**Krav**
- PHP 8.x
- Composer
- Node.js 16+ och npm
- SQLite/MySQL (konfigureras via .env)

Installation (backend)

1. Gå till backend-katalogen:

```
cd Backend
```

2. Installera beroenden:

```
composer install
```

3. Kopiera miljöfil och justera:

```
cp .env.example .env
```

4. Starta den inbyggda servern (utveckling):

```
php -S localhost:8080 -t public
```

Endpoints (viktigaste)
- `GET /activities` — lista aktivitetstyper
- `GET /workouts` — lista workouts
- `GET /statistics/timeline/{activity_id}` — data för diagram
- `GET /workouts/pdf` — exportera workouts som PDF

Installation (frontend)

1. Gå till frontend-katalogen:

```
cd Frontend
```

2. Installera beroenden och starta dev-server:

```
npm install
npm run dev
```

Konfiguration
- Frontend använder `Frontend/src/api/api.js` för att peka mot API; ändra `API_BASE_URL` om backend körs på annan port.
- Backend läser `.env` för databas och tidzon.

- Kör frontend-lint:

```
cd Frontend
npm run lint
```

Felsökning
- Kolla backend-loggar: `Backend/storage/logs/`.
- Vanliga problem: saknade routes, felaktig databas-URL i `.env`, eller att `routes/api_v1.php` saknas.


Kontakt
- Kontakta Melker eller Isac
