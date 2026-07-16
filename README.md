# ResearchVault — Research Repository & Digital Library Portal

A secure, scalable academic research portal built with a custom PHP MVC architecture, service layer, MySQL, and Paystack payments.

Students can search, preview, purchase (premium), and download research papers uploaded by administrators.

## Technology Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP 8.2+ (custom MVC + Services) |
| Database | MySQL 8 / MariaDB |
| Payments | Paystack (initialize + server-side verify) |
| Server | Apache / Nginx / PHP built-in server |

**Fonts:** Fraunces + DM Sans (Google Fonts)

## System Architecture

```
Browser
  │
  ▼
public/index.php  (front controller)
  │
  ▼
Router → Middleware (Guest / Auth / Admin)
  │
  ▼
Controllers (thin)
  │
  ▼
Services (business logic)
  │
  ▼
Repositories → MySQL (PDO prepared statements)
  │
  ▼
storage/papers (secured document store)
```

### Layer responsibilities

- **Controllers** — HTTP input/output only
- **Services** — auth, papers, uploads, payments, downloads, dashboards
- **Repositories** — reusable SQL access
- **Views** — PHP templates with layouts/partials
- **Middleware** — role-based route protection

## Features Implemented

### Public / Student
- Responsive modern UI (mobile-friendly)
- Home catalog highlights and category browsing
- Search + filters (keyword, category, free/premium, year)
- Paper listing and detail pages
- Student registration and login
- Student dashboard (purchases + download history)
- Personal library of purchased papers
- Free paper downloads (authenticated)
- Premium purchase via Paystack
- Payment verification before access unlock

### Administrator
- Admin dashboard (students, papers, revenue, downloads)
- Upload / edit / delete research papers
- Free or premium pricing
- Draft / published / archived statuses
- Secure file upload (type, size, MIME checks)

### Security
- Password hashing (`password_hash` / `password_verify`)
- CSRF tokens on all mutating forms
- PDO prepared statements
- Session hardening (strict mode, HttpOnly, SameSite)
- Role-based access control (admin / student)
- Uploaded files stored outside the web root
- Output escaping helpers (`e()`)
- Download authorization checks (ownership / free access)

## Project Structure

```
app/
  Controllers/          # Thin HTTP controllers
  Core/                 # Router, DB, View, Validator, Session
  Middleware/
  Repositories/
  Services/
  Views/
config/                 # App, DB, Paystack, routes
database/
  schema.sql            # Full database export
  seed.php              # Demo users + sample papers
public/                 # Web root
  index.php
  assets/
storage/papers/         # Uploaded documents (not publicly served)
```

## Installation and Setup

### 1. Requirements
- PHP 8.2+ with extensions: `pdo_mysql`, `curl`, `fileinfo`, `mbstring`
- MySQL 8+ or MariaDB 10.4+
- Apache/Nginx **or** PHP built-in server
- Paystack test keys (for premium checkout)

### 2. Clone / copy the project
```bash
cd "Research Repository & Digital Library Portal"
cp .env.example .env
```

### 3. Configure `.env`
Update database credentials and Paystack keys:

```env
APP_URL=http://localhost:8000
DB_HOST=127.0.0.1
DB_DATABASE=research_vault
DB_USERNAME=root
DB_PASSWORD=your_password

PAYSTACK_PUBLIC_KEY=pk_test_...
PAYSTACK_SECRET_KEY=sk_test_...
```

### 4. Create database
```bash
mysql -u root -p < database/schema.sql
php database/seed.php
```

### 5. Run locally

**Option A — project root as document root (Laragon / shared hosting default)**
```bash
php -S localhost:8000 router.php
```
Or open the project folder URL in Laragon/Apache (document root = project folder).  
`index.php` at the project root is the front controller.

**Option B — `public/` as document root (optional)**
```bash
php -S localhost:8000 -t public public/router.php
```

### 6. Apache / Laragon
No document-root change required. Keep the site pointed at the project folder.  
Sensitive folders (`app`, `config`, `storage`, `database`) are blocked by `.htaccess`.

Set `APP_URL` in `.env` to your exact site URL (including subdirectory if any), for example:
```env
APP_URL=http://localhost/Research%20Repository%20%26%20Digital%20Library%20Portal
```
Or leave detection to work from the request host when possible.

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@researchvault.test` | `Admin@12345` |
| Student | `student@researchvault.test` | `Student@12345` |

> Run `php database/seed.php` after importing SQL so demo passwords are set correctly.

## Paystack Integration Flow

1. Student clicks **Purchase** on a premium paper
2. `PaymentService` creates/updates a pending purchase
3. Paystack transaction is initialized (server-side)
4. Student completes payment on Paystack checkout
5. Callback hits `/payment/callback`
6. App verifies the transaction with Paystack Secret Key
7. Amount is validated; purchase status becomes `completed`
8. Download access is granted

Use **Paystack test mode** cards during development.

## SQL Database Export

Full schema + seed categories: [`database/schema.sql`](database/schema.sql)

## Deployment Platform

Recommended production stack: **DigitalOcean App Platform / Droplet** or **Railway / Render** for PHP + managed MySQL.

See the hosting recommendation below.

---

## Hosting Recommendation Report

### Preferred hosting provider: **DigitalOcean** (Droplet or App Platform)

For this PHP + MySQL Paystack application, **DigitalOcean** is the best balance of control, cost, and scalability.

### Why DigitalOcean
- Native support for classic LAMP/LEMP stacks (PHP-FPM + Nginx + MySQL)
- Straightforward deployment of a custom MVC app without framework lock-in
- Managed MySQL option available when you outgrow a single VPS
- Predictable pricing and good regional coverage (including African routes relevant to Paystack/NGN)
- Easy TLS via Let’s Encrypt and simple object/storage expansion later

### Scalability considerations
- Start on a single Droplet (1–2 vCPU) behind Nginx
- Keep uploaded papers on disk initially; move `storage/papers` to **DigitalOcean Spaces** (S3-compatible) as volume grows
- Add Redis for sessions/cache when traffic increases
- Scale vertically first; then split web and database tiers
- Use Paystack webhooks (optional enhancement) for asynchronous payment confirmation under load
- Put Cloudflare (free tier) in front for CDN/caching of static assets

### Estimated monthly hosting costs

| Plan | Approx. monthly cost | Suitable for |
|---|---|---|
| Basic Droplet (1 vCPU / 1GB) | **$6–12** | Demo / campus pilot |
| Droplet + Managed MySQL | **$30–45** | Production department portal |
| App Platform + Managed DB | **$25–60** | Managed deploys with less ops work |
| + Spaces object storage | **$5+** | Growing document libraries |

*Estimates exclude domain (~$10–15/year) and Paystack transaction fees.*

### Advantages
- Full control over PHP version, upload limits, and security headers
- Affordable entry cost for academic projects
- Clean path from prototype → production
- Works well with Git-based deploy workflows

### Potential limitations
- More server administration than shared hosting (cPanel)
- You are responsible for OS patches, backups, and monitoring (unless using managed DB/App Platform)
- Shared hosting alternatives (Hostinger, Namecheap) are cheaper for tiny demos but weaker for secure file storage and scaling

### Alternatives
- **Railway / Render** — faster Git deploys, slightly higher cost at scale
- **AWS Lightsail** — similar to Droplets, deeper AWS ecosystem later
- **Shared hosting (Hostinger)** — fine for evaluation demos only; not ideal for secure document vaults

---

## Evaluation Mapping

| Criterion | How this project addresses it |
|---|---|
| Code quality | Custom MVC, thin controllers, reusable services/repositories |
| Database design | Normalized schema with FKs, indexes, purchase/payment audit trail |
| UI/UX | Responsive academic design, clear student/admin flows |
| Functionality | Search, upload, RBAC, purchase, verify, download |
| Security | CSRF, hashing, PDO, role checks, private storage |
| Payments | Paystack initialize + verify-before-access |
| Performance | Indexed queries, pagination, lean frontend assets |
| Documentation | This README + SQL export + hosting report |
| Architecture | Front controller, middleware, service layer, scalable storage path |

## License

Academic / assessment submission project.
