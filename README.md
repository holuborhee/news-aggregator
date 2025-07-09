# 📰 News Aggregator Platform

A full-stack news aggregator application built with **Laravel (API)**, **Next.js (Frontend - App Router)**, **MySQL**, and **Elasticsearch**. Dockerized for easy local development.

---

## ✨ Setup Instructions

### 🔑 Prerequisite: Get a NewsAPI.org API key

Generate a free API key from [https://newsapi.org](https://newsapi.org) and set it in `.env`.

---

### 🔧 Environment Setup

No native PHP/Node.js installation required — everything runs in **Docker**.

#### 1. Clone the repo

```bash
git clone https://github.com/your-username/news-aggregator.git
cd news-aggregator
```

#### 2. Create `.env` files

- `backend/.env` – Laravel environment file
- `frontend/.env.local` – Next.js environment file

✅ Some defaults are already injected via `docker-compose.yml`.

Sample for `backend/.env`:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
ELASTICSEARCH_HOST=http://elasticsearch:9200
NEWS_API_KEY=your_newsapi_key_here
QUEUE_CONNECTION=sync
```

---

### 🐳 Run the App with Docker

```bash
docker-compose up --build
```

Then run the following setup steps:

```bash
# Generate app key
docker exec -it laravel_app php artisan key:generate

# Run migrations and seed categories
docker exec -it laravel_app php artisan migrate --seed --force

# Setup Elasticsearch indexes, scrape initial news, and setup schedule
docker exec -it laravel_app php artisan app:setup-news-aggregator
```

Visit:

- Laravel API: [http://localhost:8000](http://localhost:8000)
- Next.js frontend: [http://localhost:3000](http://localhost:3000)

---

## 🧱 Architectural Decisions

### 🟧 Why Elasticsearch?

- Full-text search performance and scalability
- Powerful filtering, ranking, and boosting
- Ideal for paginated search and feed logic

### 🍜 Why Server Cookies in Next.js?

- Secure, HttpOnly tokens prevent XSS
- Works natively in server components
- Shared across routes without exposing sensitive data in JS

### 🧑‍💻 Why Keep Sources & Authors Only in Elasticsearch?

- Sources and authors are dynamic and depend on external APIs
- No need to normalize or manage their state relationally
- Avoids unnecessary writes or migrations in MySQL

### 🗺️ Why Articles & Feeds Pages Are Server Rendered?

- Better SEO and performance
- Allows SSR pagination and filtering with clean URLs
- Maintains secure cookie access on server components

### ⏱️ Why Run the Laravel Scheduler as a Service?

- Ensures scraping jobs run independently of web requests
- Cleaner DevOps model for background jobs
- Dockerized for separation of concerns

---

## 🚣 API Endpoints

| Method | Endpoint                  | Description                              |
| ------ | ------------------------- | ---------------------------------------- |
| POST   | `/api/v1/register`        | Register new user                        |
| POST   | `/api/v1/login`           | Login and receive token                  |
| POST   | `/api/v1/logout`          | Logout and revoke token                  |
| GET    | `/api/v1/preferences`     | Get current user preferences             |
| POST   | `/api/v1/preferences`     | Update user preferences                  |
| GET    | `/api/v1/feeds`           | Get personalized user feeds              |
| GET    | `/api/v1/articles/search` | Search/filter articles                   |
| GET    | `/api/v1/articles/{id}`   | Get single article details (if needed)   |
| GET    | `/api/v1/meta`            | Get all categories, sources, and authors |

---

## ✅ Improvements To Be Made

- [ ] ⏰ Improve placement of migration command for startup safety
- [ ] 🧰 Enhance Docker setup for separate **dev** and **prod** configs
- [ ] ↺ Consider switching NewsAPI endpoint (`/top-headlines` vs `/everything`)
- [ ] 🛠 Debug “no scheduled command is ready to run” issue
- [ ] 🥞 Make scraping frequency configurable
- [ ] 🔐 Switch from Laravel Sanctum to JWT auth for token interoperability
- [ ] ⚡️ Cache frequent Elasticsearch responses
- [ ] 🔍 Split authors into a dedicated query endpoint (if needed)
- [ ] 🧠 Improve relevance algorithm for feeds and article search
- [ ] 🎨 Remove Tailwind CDN and use compiled utility classes
- [ ] 🔒 Make logout use Laravel's `auth()` logout logic
- [ ] 🕵️ Add route protection with Laravel middleware
- [ ] 💨 Add loading/error states in the UI
- [ ] 📱 Improve mobile responsiveness
