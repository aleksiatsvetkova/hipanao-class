# HIPANAO SOLUTIONS

**Student and Alumni Records Management System**
**Version:** 3.0.5

| Item            | Details                                                                 |
| --------------- | ----------------------------------------------------------------------- |
| **Folder name** | `hipanao-class`                                                         |
| **Database**    | `hipanao_db`                                                            |
| **Stack**       | PHP 8 + PDO / MariaDB, AdminLTE 3, Bootstrap 4, DataTables, SweetAlert2 |

---

## 1. Installation

### Step 1 — Copy the folder

Extract the ZIP file and place the `hipanao-class` folder inside `htdocs`:

```text
C:\xampp\htdocs\hipanao-class\
```

You should end up with:

```text
C:\xampp\htdocs\hipanao-class\index.php
```

### Step 2 — Start XAMPP

Open the **XAMPP Control Panel** and start:

* Apache
* MySQL

### Step 3 — Import the database

Open:

```text
http://localhost/phpmyadmin
```

Click the **Import** tab.

> **Do not create the database first.** The SQL file creates `hipanao_db` automatically.

Choose:

```text
hipanao-class\database\hipanao_db.sql
```

Then click **Go**.

You should see `hipanao_db` appear in the left sidebar with **11 tables**.

### Step 4 — Set up the virtual host

Follow the instructions in:

```text
setup\httpd-vhosts.conf
```

Then:

```text
setup\hosts.txt
```

Restart Apache after making the changes.

### Step 5 — Open the site

With the virtual host configured:

```text
http://hipanao-class.local/
```

You can also skip the virtual host setup entirely and use:

```text
http://localhost/hipanao-class/
```

The application automatically detects which URL is being used and adjusts its links accordingly. **No code changes are required.**

---

## 2. Login

### Default account

```text
Username: hipanao
Password: hipanao
```

The original administrator account also still works:

```text
Username: admin
Password: admin
```

The other legacy accounts:

```text
jason
dfd
dfdf
```

carry over with their original password hashes.

Those passwords were not included in the database dump. If nobody remembers them, reset the accounts through:

**Account Settings → Manage User Accounts**

> **Important:** Change the default passwords before showing the system to anyone.

---

## 3. Enrollment Flow

Enrollment now follows a **two-stage process**.

```text
Student > Reg
    │
    │ Creates the record as RESERVED
    │
    ├── Academic Year
    ├── Semester
    ├── Course
    ├── Year Level
    ├── Curriculum Yr
    ├── Category
    ├── Date Reserved
    ├── NO section
    └── NO enrollment date yet
    │
    ▼
Enrollment > Sectioning
    │
    │ Completes the record as ENROLLED
    │
    ├── Assigns the Section
    └── Stamps Date Enrolled
    │
    ▼
Enrollment Details
    │
    └── Attach the subjects
    │
    ▼
Grades
    │
    └── Encode the grades
```

The **Enrollment** screen includes filter chips:

* **All**
* **Reserved**
* **Enrolled**

Each filter displays live counts so you can immediately see how many students are still waiting to be sectioned.

### Why Enrollment has its own module

Enrollment now uses its own module:

```text
module/enrollment
```

instead of the generic table builder.

The generic builder cannot provide the required **Sectioning** action, so `tblenrollment` was removed from `$GENERIC_TABLES`. This prevents having two competing interfaces for editing the same table.

### Enrollment column notes

`SECTION_ID` and `DATE_ENROLLED` are nullable.

A reserved student has neither value yet. This is the intended behavior of the two-stage enrollment process.

`ENCODED_BY` is a real foreign key to `tblusers` and records which user processed the enrollment.

A unique key on:

```text
(S_ID, SY_ID, SEMESTER)
```

prevents the same student from being reserved twice for the same term at the database level.

There is no `DEPT_ID`.

This database does not contain a department table, so the column would remain empty. The course already identifies the student's program.

---

## 4. Database Structure

All **11 tables** are connected. There are no orphan tables, so phpMyAdmin's **Designer** view displays one connected database diagram.

```text
tblusertype
    └── tblusers                (TYPEID)
          ├── tblstudent        (AddedBy)
          └── alumni_details    (AddedBy)

tblcourses
    ├── tblstudent              (COURSE_ID)
    ├── tblsections             (COURSE_ID)
    ├── tblsubjects             (COURSE_ID)
    └── tblenrollment           (COURSE_ID)

tblschoolyear
    ├── tblsections             (SY_ID)
    ├── tblenrollment           (SY_ID)
    └── tblgrades               (SY_ID)

tblstudent
    ├── alumni_details          (S_ID)
    ├── tblenrollment            (S_ID)
    └── tblgrades               (S_ID)

tblenrollment
    ├── tblenrollment_details   (ENROLLMENT_ID)
    └── tblgrades               (ENROLLMENT_ID)

tblsubjects
    ├── tblenrollment_details   (SUBJECT_ID)
    └── tblgrades               (SUBJECT_ID)

tblsections
    └── tblenrollment            (SECTION_ID)
```

**Total foreign keys:** 18

To view the diagram:

```text
phpMyAdmin
→ hipanao_db
→ Designer
```

---

## 5. Changes From the Original Build

### Student Module

The student edit modal now prefills correctly.

* `ajax.php` now returns `LNAME`, `SEX`, and `BDAY`, which it never returned before. This fixes the blank **Last Name**, **Gender**, and **Date** fields.
* The edit modal's gender and date fields previously reused the same HTML IDs as the Add modal. They were renamed to `SEX1` and `BDAY1`.
* The `UID` box is now hidden instead of appearing as a visible text field at the top of the form.
* `controller.php` now actually saves gender and date during editing. Previously, both values were silently discarded.
* The empty gender option previously had no `value`, causing a blank selection to submit the literal text `"Select Gender"`. This is how records ended up with values such as `SEX = "Select Gen"` in the old database.

### Database

* The database was renamed to `hipanao_db`, preventing conflicts with `alumni_db`.
* All legacy tables are now linked through foreign keys.
* `tblstudent.IDNO` was changed from `int` to `varchar(20)`. An integer cannot safely store a 12-digit LRN because it overflows at `2147483647`.
* `tblstudent` was changed from `latin1` to `utf8mb4` to match the other tables.
* `tblsubjects` was completely empty, leaving the Subject, Enrollment Details, and Grades modules with nothing to reference. It is now seeded with **10 subjects across the three courses**.
* Sample enrollment and grade rows were added so the modules are not blank on first run.
* Blank `tblusertype.STATUS` values were normalized to `Active` / `Inactive`.
* Both school years were initially marked Active. Only **2025-2026** is now active.

### Generic Module

* Empty date fields no longer write `0000-00-00`. Blank values now become `NULL`, or fall back to the column `DEFAULT`.
* The `STATUS` dropdown is no longer hardcoded to `Active` / `Inactive` everywhere. Enrollment correctly supports:

  * `Enrolled`
  * `Dropped`
  * `Completed`
* `AddedBy` and `TYPEID` columns now render as proper dropdowns.

### PHP 8 Compatibility

* `strftime()` was replaced with `date()` in seven locations. `strftime()` was deprecated in PHP 8.1 and removed in PHP 8.4.
* `module/error/index.php` used `<?` instead of `<?php`. When `short_open_tag` was disabled, the 404 page displayed its own source code.

### Login

Previously, only these user types were allowed through:

* Administrator
* Doctor
* Staff

Other authenticated user types were left on the login page.

Now, **any active account can reach the dashboard**.

A failed login previously redirected to `index.php`, which then redirected back to `login.php`. Failed logins now remain on the login page and display a clear error message.

### School Logo

`csr-scc.png` is the **Tanon College, San Carlos City seal**.

The filename was intentionally kept unchanged so the three existing references do not need modification:

```text
theme/template.php
login.php
module/about/list.php
```

Replace that one file whenever you want to use a different school logo. All three locations will automatically use the replacement.

The original image had a solid black background. The background was removed by flood-filling inward from the image borders so that only the background was removed while dark pixels inside the artwork were preserved.

The scalloped outer edge was also feathered to prevent a black halo from appearing around the logo on the dark sidebar.

### Size Optimization

The untouched AdminLTE demo content was removed:

```text
pages/
build/
index11.html
```

A total of **34 unused plugin libraries** were also removed.

The original `csr-scc.png` was a **3.1 MB, 2048 px** image being displayed at approximately 20% width. It was resized to **300 px** and is now approximately **143 KB**.

Approximate total size:

```text
Before: ~70 MB
After:  ~25 MB
```

---

## 6. Still Outstanding

The following issues are known and were **not changed** because fixing them properly requires reworking how the application communicates with the database.

### SQL Injection

`User::AuthenticateUser` and older `*_ajax.php` files construct SQL queries through string concatenation without parameter binding.

The login form is therefore exploitable.

Properly fixing this requires moving database operations to **prepared statements with bound parameters**.

### Password Security

Passwords currently use **unsalted SHA1**.

They should be migrated to:

```php
password_hash()
```

and verified using:

```php
password_verify()
```

### `escape_value()`

`escape_value()` calls:

```php
mysql_real_escape_string()
```

This function does not exist in PHP 8, so the current implementation silently falls back to `addslashes()`.

### Dynamic Properties

The `User`, `Student`, and `Details` classes do not declare their properties, causing every save operation to create dynamic properties.

Dynamic properties are deprecated in PHP 8.2.

`Course` and `Subject` were written correctly and do not have this issue.

### Student Date Label

The Student module label currently says:

```text
Date Started
```

but the database column is:

```text
BDAY
```

and the list header also says:

```text
BDAY
```

Decide which field this is supposed to represent, then make the label consistent.

---

# HIPANAO SOLUTIONS
