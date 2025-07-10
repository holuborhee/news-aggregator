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
git clone https://github.com/holuborhee/news-aggregator.git
cd news-aggregator
```

#### 2. Environment variables

✅ Defaults are already injected via `docker-compose.yml` for all services except for the backend service.

#### Why is the Backend service handled differently?

Managing the environment variables from `.env` file is best for the backend service, partly because of how Laravel handles environment config. It prioritizes the `.env` file and will only look elsewhere if the file doesn't exist. Also the `key:generate` command writes the generated key to `.env` file automatically. If `.env` is missing, it won’t write it anywhere, and Laravel will fail on encryption functions (e.g., auth, cookies).

##### Setting `.env` for backend

a. Run the command below to create your `.env` file

```
cd backend
cp .env.example .env
```

b. Ensure to locate each of the key below in your `backend/.env` file and ensure their value matches the value here:

```env
APP_NAME=NewsAggregator
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
QUEUE_CONNECTION=sync
```

`Make use of the exact values above unless you are sure of what you are doing`

c. Finally, add these two variables to the file. This is where you use the api key you generated initially.

```env
ELASTICSEARCH_HOST=http://elasticsearch:9200
NEWSAPI_KEY={your_newsapi_key_here}
```

> NOTE: Unless you make any change to the docker settings and ports, you do not need to bother about envionment variables for all the services except the `laravel_app`. The defaults should work for all the services.

---

### 🐳 Run the App with Docker

Navigate to the root directory of the app to run the following commands:

```bash
docker-compose up --build
```

Then run the following setup steps:

1. Generate app key

```bash
docker exec -it laravel_app php artisan key:generate
```

2. Run migrations and seed categories

```bash
docker exec -it laravel_app php artisan migrate --seed --force
```

3. Setup Elasticsearch indexes, scrape initial news, and setup schedule

```bash
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

| Method | Endpoint              | Description                              |
| ------ | --------------------- | ---------------------------------------- |
| POST   | `/api/v1/register`    | Register new user                        |
| POST   | `/api/v1/login`       | Login and receive token                  |
| GET    | `/api/v1/preferences` | Get current user preferences             |
| PUT    | `/api/v1/preferences` | Update user preferences                  |
| GET    | `/api/v1/feed`        | Get personalized user feeds              |
| GET    | `/api/v1/articles`    | Search/filter articles                   |
| GET    | `/api/v1/metadata`    | Get all categories, sources, and authors |

---

## ✅ Improvements To Be Made

- [x] ⏰ Improve placement of migration command for startup safety
- [ ] 🧰 Enhance Docker setup for separate **dev** and **prod** configs
- [ ] ↺ Consider switching NewsAPI endpoint (`/top-headlines` vs `/everything`)
- [ ] 🛠 Debug and fix scheduler not running
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
