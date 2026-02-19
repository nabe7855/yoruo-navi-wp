# Handover Implementation Plan: Next.js to WordPress Migration

## 1. Objective

Port the remaining core functionalities of the "yoru-otoko-navi" (Next.js) application to the "yoruo-navi" (WordPress) theme.
Specifically:

1.  **Store Registration & Approval**: Stores sign up, Admin approves.
2.  **Store Job Management**: Stores log in, post/edit jobs.
3.  **User Job Application**: Users (Job Seekers) log in, apply to jobs.

## 2. Architecture & Data Mapping

### 2.1 Users & Roles

We will use standard WordPress Users with custom roles.

| entity Type    | Next.js Role | WordPress Role  | Notes                                            |
| :------------- | :----------- | :-------------- | :----------------------------------------------- |
| **Store**      | `employer`   | `store_owner`   | Custom Role. Can manage own 'job' posts.         |
| **Job Seeker** | `jobseeker`  | `job_seeker`    | Custom Role. Can manage own 'application' posts. |
| **Admin**      | `admin`      | `administrator` | Standard WP Admin.                               |

### 2.2 Custom Post Types (CPT)

| Entity          | WordPress CPT | Existing? | Fields (Meta/Taxonomy)                                                                                              |
| :-------------- | :------------ | :-------- | :------------------------------------------------------------------------------------------------------------------ |
| **Job**         | `job`         | **Yes**   | `job_category` (tax), `job_area` (tax), `_salary_min`, `_salary_max`, `_employment_type`, `_job_detailed_fields`... |
| **Application** | `application` | **No**    | `_job_id` (target job), `_applicant_id` (user), `_application_status`, `_message`                                   |

### 2.3 Store Profile Data

Stored in **User Meta** for simplicity (one user = one store).

- `_store_name` (Company Name)
- `_store_business_type`
- `_store_area_pref`
- `_store_contact_email`
- `_account_status` ('pending', 'approved', 'rejected')

## 3. Implementation Steps

### Phase 1: Core Setup (Roles & CPTs)

**File:** `inc/core-setup.php` (Create and require in `functions.php`)

- Register Roles: `store_owner`, `job_seeker`.
- Grant Capabilities:
  - `store_owner`: `edit_jobs`, `publish_jobs`, `upload_files` (limit to own).
- Register CPT `application`:
  - `hierarchical` => false
  - `public` => false (internal use mostly, or private dashboard)
  - `supports` => `['title', 'editor', 'custom-fields']`

### Phase 2: User Registration & Login (Unified)

**Files:** `page-templates/page-login.php`, `page-templates/page-signup.php`

- **Signup Form**:
  - Fields: Email, Password, Display Name, Account Type (Radio: Store/Seeker).
  - Action: `wp_create_user()`.
  - Logic:
    - If 'Store' -> Set role `store_owner`, set meta `_account_status` = 'pending'. Redirect to Store Setup.
    - If 'Seeker' -> Set role `job_seeker`. Redirect to Home/Profile Setup.
- **Login Form**:
  - Standard `wp_login_form()` with custom styling.
  - Redirect based on Role.

### Phase 3: Store Workflow

**Files:** `page-templates/page-store-dashboard.php`, `page-templates/page-job-post.php`

1.  **Store Setup (Onboarding)**
    - Check if `_store_name` exists. If not, show form.
    - Form: Business Type, Area, Phone, Vibe Tags.
    - Save to `update_user_meta()`.

2.  **Dashboard**
    - Query `post_type=job`, `author=current_user`.
    - List jobs with Status (Draft, Terminates, Published).
    - "Create New Job" button.

3.  **Job Posting Form**
    - Replicate fields from Next.js `PostJobPage.tsx`:
      - **Basic**: Title, Category (Tax), Type (Select), Area (Tax).
      - **Salary**: Type (Hourly/Monthly), Min, Max.
      - **Details**: Qualifications, Access, Benefits, Insurance, Hours, Holidays, PR (Textareas).
    - Action: `wp_insert_post()` + `update_post_meta()`.
    - **Status Logic**:
      - If Store Verified -> `post_status` = 'publish' (or 'pending' if moderation needed).
      - If Store Pending -> `post_status` = 'pending'.

### Phase 4: Admin Approval Interface

**Files:** `inc/admin-customizations.php`

- **User List Column**: Add "Account Status" column to Users list.
- **Action**: "Approve Store" link (sets `_account_status` = 'approved').
- **Job List Column**: Show "Store Name".

### Phase 5: Job Application Flow

**Files:** `single-job.php` (update), `page-templates/page-apply.php` (optional helper)

1.  **Job Detail Page**:
    - "Apply Now" button.
    - Condition:
      - Guest -> Modal "Login to Apply".
      - Store -> Hide or "Login as Seeker".
      - Seeker -> Show Application Form (Modal or Inline).

2.  **Application Form**:
    - Pre-fill User Name/Email.
    - Fields: Message, Contact Preference.
    - Submit -> Create `application` post.
      - Title: "Apply: [Job] - [User]"
      - Meta: `_job_id`, `_applicant_id`, `_status`='submitted'.
    - Notification: `wp_mail` to Store Owner email.

## 4. Reference: Field Mappings

**(From `yoru-otoko-navi/pages/PostJobPage.tsx`)**

| Field             | WordPress                 | Type     |
| :---------------- | :------------------------ | :------- |
| `title`           | `post_title`              | Text     |
| `category`        | `job_category` (Tax)      | Select   |
| `employment_type` | `_employment_type` (Meta) | Select   |
| `area_pref/city`  | `job_area` (Tax)          | Select   |
| `salary_type`     | `_salary_type`            | Select   |
| `salary_min`      | `_salary_min`             | Number   |
| `salary_max`      | `_salary_max`             | Number   |
| `qualifications`  | `_qualifications`         | Textarea |
| `access_info`     | `_access_info`            | Textarea |
| `salary_details`  | `_salary_details`         | Textarea |
| `benefits`        | `_benefits`               | Textarea |
| `insurance`       | `_insurance`              | Textarea |
| `working_hours`   | `_working_hours`          | Textarea |
| `holidays`        | `_holidays`               | Textarea |
| `workplace_info`  | `_workplace_info`         | Textarea |

## 5. Next Steps for AI Developers

1.  **Execute Phase 1**: Create `inc/core-setup.php` and register the `application` CPT and Roles.
2.  **Execute Phase 2**: Build the Signup Page Template.
3.  **Execute Phase 3**: Build the Store Dashboard and Job Post Form.
4.  **Execute Phase 5**: Connect the frontend "Apply" button.
