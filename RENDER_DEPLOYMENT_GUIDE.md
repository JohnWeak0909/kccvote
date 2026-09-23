# Render.com Deployment Guide for KEVS (CodeIgniter 4)

This guide walks you through deploying your **KEVS E-Voting System** to **[Render.com](https://render.com)** for **permanent 24/7 hosting** with a permanent HTTPS domain (`https://kevs-voting.onrender.com`).

---

## Architecture Overview

```
                      ┌────────────────────────────────────────┐
                      │            Render.com (24/7)           │
                      │  Docker Web Service (PHP 8.2 + Apache) │
                      │   Domain: https://your-app.onrender.com│
                      └──────────────────┬─────────────────────┘
                                         │ (Connects over SSL)
                                         ▼
                      ┌────────────────────────────────────────┐
                      │      Free Cloud MySQL (24/7)           │
                      │  TiDB Cloud Serverless / Aiven MySQL   │
                      │   Tables: admins, students, votes, etc.│
                      └────────────────────────────────────────┘
```

---

## Step 1: Set Up Free 24/7 Cloud MySQL (60 Seconds)

Render's free tier provides web compute, but not native MySQL. The best free cloud MySQL is **TiDB Cloud Serverless** (100% MySQL compatible, 5 GB free forever, no credit card required).

1. Go to **[https://tidbcloud.com](https://tidbcloud.com)** and sign in with Google or GitHub.
2. Click **Create Cluster** and choose **Serverless** (Free $0/mo).
3. Select your preferred region and click **Create**.
4. Once created, click **Connect**:
   - Choose **General connection** or **PHP**.
   - Note your credentials:
     - **Host**: (e.g. `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`)
     - **Port**: `4000` (or `3306` if standard MySQL)
     - **User**: (e.g. `xxxxxx.root`)
     - **Password**: (Your cluster password)
     - **Database**: `test` or create `voting_system`
5. In the TiDB Cloud dashboard, click **SQL Editor** (or connect via HeidiSQL / DBeaver / MySQL Workbench) and run the contents of:
   `c:\xampp\htdocs\KEVS\db\render_voting_system.sql`
   All tables, elections, positions, and admin data will be imported immediately!

> **Alternative Free MySQL Providers**: You can also use [Aiven.io](https://aiven.io) (free MySQL) or [Clever Cloud](https://www.clever-cloud.com) (free MySQL).

---

## Step 2: Push Your Code to GitHub

1. Create a new empty repository on **[github.com](https://github.com)** (e.g., named `KEVS` or `kcc-voting`).
2. Open your terminal in `c:\xampp\htdocs\KEVS` and run:
   ```bash
   git add .
   git commit -m "Configure KEVS for Render deployment"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
   git push -u origin main
   ```

---

## Step 3: Deploy on Render.com

1. Go to **[https://render.com](https://render.com)** and sign up / log in with your GitHub account.
2. In the Render Dashboard, click **New +** and select **Web Service**.
3. Choose **Build and deploy from a Git repository**.
4. Select your `KEVS` repository.
5. Render will automatically read the `Dockerfile` and configure:
   - **Runtime**: `Docker`
   - **Instance Type**: `Free`
6. Scroll down to **Environment Variables** and add your Cloud MySQL details:
   | Key | Value (from Step 1) |
   | :--- | :--- |
   | `CI_ENVIRONMENT` | `production` |
   | `database.default.hostname` | *Your cloud MySQL host* |
   | `database.default.database` | *Your cloud database name* |
   | `database.default.username` | *Your cloud MySQL user* |
   | `database.default.password` | *Your cloud MySQL password* |
   | `database.default.DBDriver` | `MySQLi` |
   | `database.default.port` | `4000` *(or `3306`)* |
7. Click **Create Web Service**.

---

## Step 4: Your Live Permanent Website!

Render will build your Docker image and deploy your container.
Once finished (usually 2-3 minutes), you will receive your permanent 24/7 HTTPS domain:

🌐 **`https://kevs-voting.onrender.com`**

- It runs **24 hours a day, 7 days a week**.
- It does **not** require your computer or laptop to stay on.
- No visitor password prompts, no temporary tunnels.
- Every time you push updates to GitHub, Render automatically redeploys your site!
