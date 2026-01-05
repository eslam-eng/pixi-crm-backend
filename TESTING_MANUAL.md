# Mazal CRM (Pixi) - System User Manual & Testing Guide

This document provides an overview of the Mazal CRM system architecture, intended for testers and new developers to understand the available features, APIs, and workflows.

## System Architecture

The system is built on a **Multi-Tenant** architecture using Laravel.

-   **Central (Landlord)**: Manages tenants, subscriptions, plans, billing, and global settings.
-   **Tenant**: The actual CRM application isolated for each customer (Contact management, Leads, Pipelines, etc.).

---

## 1. Landlord / Central APIs

These APIs operate on the central domain and manage the SaaS aspect of the platform.

### Authentication & Registration

-   **Verify Registration**: `POST /api/auth/register/verify`
    -   Sends a verification code to the user's email.
    -   **New**: Checks `activation_codes` table if an activation code is provided.
    -   **New**: Stores verification code in `password_reset_tokens`.
-   **Register Tenant**: `POST /api/auth/register`
    -   Requires the verification code from the previous step.
    -   Creates User, Tenant, and Domain.
    -   Sets up the chosen Plan (or Free Trial).
    -   Sends "Welcome" and "Subscription Activated" emails.
-   **Admin Login**: `POST /api/auth/admin/login`

### Subscription Management

-   **Plans**: Manage pricing tiers (Monthly, Annual, Lifetime).
-   **Subscriptions**:
    -   `POST /central/api/subscriptions` - Create subscription manually.
    -   `POST /central/api/subscriptions/{id}/renew` - Manual renewal.
    -   Handles Invoicing automatically.
-   **Activation Codes**: Generate and validate codes for prepaid access.
-   **Discount Codes**: Manage promotional codes for plans.

### Global Settings

-   **Locations**: Countries, Cities, Currencies, Timezones.
-   **CRM Defaults**: Industries, Departments, Sources (used to seed new tenants).

---

## 2. Tenant APIs (The CRM)

These APIs operate on the tenant's subdomain (e.g., `acme.pixi.test/api/...`).

### Core CRM Features

-   **Contacts & Leads**: Full CRUD, Import/Export, Merging logic.
-   **Opportunities**: Kanban board management, Stages, Drag & Drop status updates.
-   **Items/Products**: Inventory management, Variants, Attributes.
-   **Deals**: Manage closed business and payments.

### Sales Pipeline

-   **Pipelines**: Custom sales pipelines.
-   **Stages**: Customizable stages for each pipeline.
-   **Loss Reasons**: Track why opportunities were lost.

### Activities & Tasks

-   **Tasks**: Todo list, Priorities, Reminders.
-   **Calendar**: View tasks and events.
-   **Activities**: Log calls, meetings, notes (for Opportunities/Contacts).

### Reporting & Analytics

Extensive reporting modules:

-   **Sales Performance**: Deals performance, Revenue analysis, Sales Rep ranking.
-   **Win/Loss Analysis**: Trend analysis for won vs lost deals.
-   **Forecasting**: Opportunity forecasting vs actual revenue.
-   **Conversion Rates**: Funnel analysis from Contact -> Opportunity -> Deal.

### Integrations

-   **Zapier**:
    -   Incoming Webhooks (`/api/zapier/create-contact`).
    -   Triggers & Actions authentication.
-   **Facebook**:
    -   Lead Ads integration (Webhooks).
    -   Post to Page, Read Page Insights.
-   **Internal Forms**: Drag-and-drop form builder for lead capture.

### Automation

-   **Workflows**: Define Triggers (e.g., "Deal Created") -> Conditions -> Actions (e.g., "Send Email", "Create Task").

---

## 3. Key Testing Workflows

### A. New User Registration (End-to-End)

1.  **Request Verification**:
    -   Send `POST` to `/api/auth/register/verify` with `email`, `first_name`, `last_name`, `domain`, `plan_id`, and optional `activation_code`.
    -   **Check Email**: User receives a "Verify Your Email" email with a 4-digit code.
    -   _(Dev Note: In Local env, valid code is usually `9999` or check response/DB)_.
2.  **Complete Registration**:
    -   Send `POST` to `/api/auth/register` with the same details + `code` (the 4-digit one) and `password`.
3.  **Result**:
    -   Tenant is created.
    -   Subscription is created (Active or Trial).
    -   User receives "Welcome Aboard" and "Subscription Activated" emails.
    -   User can now login at `http://{domain}.{app_url}/login`.

### B. Manual Subscription Renewal

1.  **As Admin**: Call `POST /central/api/subscriptions/{id}/renew`.
2.  **Result**:
    -   Subscription end date is extended based on plan cycle.
    -   New Invoice is generated.
    -   Tenant Owner receives "Subscription Renewed" email.

### C. Facebook Integration

1.  Connect Facebook Account via OAuth.
2.  Map Facebook Lead Form fields to CRM Contact fields.
3.  Submit a test lead on Facebook Developer Tools.
4.  Verify a new Contact appears in the CRM.

---

## 4. Environment Notes

-   **Mail**: Uses SMTP (SendGrid in `.env`). Local dev may use `log` driver or Mailtrap.
-   **Queue**: Uses `database`. Ensure queue worker is running (`php artisan queue:work`) for emails and heavy background tasks.
-   **Frontend**: The backend serves APIs. Frontend is likely separate or Blade-based for admin panels.

---

_Created automatically by Antigravity Agent._
