# Flash Sale Checkout – Concurrency Safe API

Simple Laravel service demonstrating correct stock handling under high concurrency, atomic hold creation, expiry, and idempotent payment webhooks.

This project implements a minimal flash-sale checkout pipeline:

1. **Products** – finite stock, cached reads.
2. **Holds** – temporary stock reservations (2 minutes).
3. **Orders** – created only from valid & active holds.
4. **Payments Webhook** – idempotent, safe for retries and out-of-order delivery.

The system enforces correctness under concurrent access using:

-   `SELECT … FOR UPDATE` row-level locking.
-   Invariant-safe state transitions via Enums.
-   A scheduled job to auto-release expired holds.
-   Idempotent keys for webhooks to prevent double processing.

---

### **Product**

-   Stock must never go negative.
-   Reads are cached for performance, but writes always clear cache.
-   Only `stock` stored directly; all operations are recorded in `product_stocks` table.

### **Hold**

-   A hold represents a **temporary reservation**.
-   Hold lifecycle:

-   Only ACTIVE holds with future `expires_at` may be used.
-   Creating a hold **atomically deducts stock** using `lockForUpdate`.

### **Order**

-   Can only be created once per hold.
-   A hold cannot be reused or reactivated.
-   Order starts in PENDING state.

### **Payment Webhook**

-   Webhook payload: `{ idempotency_key, order_id, status }`
-   Fully **idempotent**:
-   Same idempotency_key processed once only.
-   Webhook may arrive **before** the client receives the order creation response.
-   Final order state must be correct:
-   `success` → PAID
-   `failed` → CANCELED + restore stock + mark hold EXPIRED

### **Scheduler**

-   Expired holds auto-release stock **without needing reads**.

---

## How to Run the Project

### Clone & Install Dependencies

```bash
git clone <your_repo_url>
cd project
composer install
urls
products

```

---

### **Application logs:**

storage/logs/laravel.log

---

### **database**

-   cp .env.example .env
-   DB_CONNECTION=mysql
-   DB_DATABASE=flash
-   DB_USERNAME=root
-   DB_PASSWORD=

---

### **artisan command**

-   php artisan migrate --seed
-   php artisan queue:work
-   php artisan schedule:work

---

### **Feature test**

Run all tests:

-   php artisan test

Run specific tests:

-   php artisan test --filter=HoldTest
-   php artisan test --filter=OrderTest
-   php artisan test --filter=PaymentWebhookTest

---

## **Postman Collection**

-   GetPayIn.postman_collection.json
