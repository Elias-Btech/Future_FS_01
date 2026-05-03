# How to Add Your Projects to the Portfolio

## Problem
Only 1 project is showing on your portfolio when you should have multiple projects displayed in a grid.

## Solution
You need to insert your projects into the database.

---

## Method 1: Using phpMyAdmin (Easiest)

1. **Open phpMyAdmin**
   - Go to `http://localhost/phpmyadmin`
   - Login with your MySQL credentials

2. **Select Your Database**
   - Click on your portfolio database (likely `portfolio_db`)

3. **Go to SQL Tab**
   - Click the "SQL" tab at the top

4. **Copy and Paste**
   - Open `sql/YOUR_PROJECTS_INSERT.sql`
   - Copy ALL the INSERT statements
   - Paste into the SQL query box
   - Click "Go"

5. **Verify**
   - Click on the `projects` table
   - You should see 4 projects listed

---

## Method 2: Using Command Line

### Windows (XAMPP):
```bash
cd C:\xampp\mysql\bin
mysql -u root -p portfolio_db < "C:\path\to\your\portfolio\sql\YOUR_PROJECTS_INSERT.sql"
```

### Mac/Linux:
```bash
mysql -u root -p portfolio_db < /path/to/your/portfolio/sql/YOUR_PROJECTS_INSERT.sql
```

---

## Method 3: Using the Dashboard

1. **Login to Dashboard**
   - Go to `http://localhost/your-portfolio/login.php`
   - Login with your credentials

2. **Add Projects Manually**
   - Click "Add Project"
   - Fill in the form for each project:
     - **Title**: Inventory Management System
     - **Description**: A comprehensive inventory management system...
     - **Tech Stack**: PHP, MySQL, JavaScript, Bootstrap, PDO, AJAX
     - **Category**: fullstack
     - **Emoji**: 📦
     - **GitHub URL**: https://github.com/Elias-Btech/inventory-management-system
     - **Live URL**: (leave empty if not deployed)
   - Click "Save"
   - Repeat for all 4 projects

---

## Your 4 Real Projects

1. **📦 Inventory Management System** (Full Stack)
   - PHP, MySQL, JavaScript, Bootstrap
   - GitHub: https://github.com/Elias-Btech/inventory-management-system

2. **📊 Population Growth Analysis** (Backend/Data)
   - Python, Pandas, Matplotlib, NumPy
   - GitHub: https://github.com/Elias-Btech/population_growth

3. **💰 Knapsack Investment Optimization** (Backend/Algorithms)
   - Python, Algorithms, Dynamic Programming
   - GitHub: https://github.com/Elias-Btech/knapsack-investment

4. **💡 ATmega32 FreeRTOS LED Control** (Embedded Systems)
   - C, FreeRTOS, ATmega32
   - GitHub: https://github.com/Elias-Btech/ATmega32_FreeRTOS_LED

---

## Expected Result

After adding projects, you should see:

### Desktop (1400px+):
```
┌─────────────────────────────────────────────────────────────┐
│           Featured Case Studies                             │
│                                                             │
│    ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│    │ 📦 Inv.  │    │ 📊 Pop.  │    │ 💰 Knap. │          │
│    │ Mgmt     │    │ Growth   │    │ Invest   │          │
│    └──────────┘    └──────────┘    └──────────┘          │
│                                                             │
│    ┌──────────┐                                            │
│    │ 💡 LED   │                                            │
│    │ Control  │                                            │
│    └──────────┘                                            │
└─────────────────────────────────────────────────────────────┘
```

### Desktop (1024-1399px):
```
┌─────────────────────────────────────────────────────────────┐
│           Featured Case Studies                             │
│                                                             │
│    ┌──────────────────┐    ┌──────────────────┐          │
│    │ 📦 Inventory     │    │ 📊 Population    │          │
│    │ Management       │    │ Growth Analysis  │          │
│    └──────────────────┘    └──────────────────┘          │
│                                                             │
│    ┌──────────────────┐    ┌──────────────────┐          │
│    │ 💰 Knapsack      │    │ 💡 ATmega32      │          │
│    │ Investment       │    │ LED Control      │          │
│    └──────────────────┘    └──────────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

---

## Troubleshooting

### "Table 'projects' doesn't exist"
Run the setup SQL first:
```bash
mysql -u root -p portfolio_db < config/setup_extended.sql
```

### "Duplicate entry" error
Your projects are already in the database. Check:
```sql
SELECT * FROM projects;
```

### Still showing only 1 project
1. Clear browser cache (Ctrl+Shift+Delete)
2. Check database connection in `config/db.php`
3. Verify projects exist:
   ```sql
   SELECT COUNT(*) FROM projects;
   ```

### Projects not displaying in correct order
Update sort_order:
```sql
UPDATE projects SET sort_order = 0 WHERE title LIKE '%Inventory%';
UPDATE projects SET sort_order = 1 WHERE title LIKE '%Population%';
UPDATE projects SET sort_order = 2 WHERE title LIKE '%Knapsack%';
UPDATE projects SET sort_order = 3 WHERE title LIKE '%ATmega%';
```

---

## Quick Test

After adding projects, refresh your portfolio page:
```
http://localhost/your-portfolio/index.php
```

You should now see **4 project cards** in a beautiful grid layout!

---

## Optional: Add Sample Projects

If you want to showcase more projects (for demonstration), you can also add the 10 sample projects:

```bash
mysql -u root -p portfolio_db < sql/SAMPLE_PROJECTS_INSERT.sql
```

This will give you **14 total projects** (4 real + 10 samples) for a fuller portfolio.

---

**Status**: Ready to add projects  
**Files**: `sql/YOUR_PROJECTS_INSERT.sql` (4 projects)  
**Alternative**: `sql/SAMPLE_PROJECTS_INSERT.sql` (10 sample projects)
