# Admin Panel Implementation Plan

## Phase 1: Foundation (Auth, Routes, Layout)

### 1.1 Authorization
- Gate/Policy: `isAdmin()` - checks `Role::Admin` or `Role::SuperAdmin`
- Apply to all admin routes

### 1.2 Routes
```
/admin                       → redirect to /admin/dashboard
/admin/dashboard             → Admin dashboard
/admin/courses               → Course list
/admin/courses/create        → Create course
/admin/courses/{course}     → View course
/admin/courses/{course}/edit → Edit course
```

### 1.3 Layout
- Create `resources/views/layouts/admin.blade.php`
- Similar to faculty sidebar layout
- Sidebar navigation items:
  - Dashboard
  - Courses
  - Users (future)
  - Enrollments (future)
  - Badges (future)
  - Settings (future)
- Use Flux UI components

---

## Phase 2: Courses Module

### Routes
| Route | Component | Description |
|-------|-----------|-------------|
| `/admin/courses` | `Admin/Courses/Index` | List all courses |
| `/admin/courses/create` | `Admin/Courses/Create` | Create new course |
| `/admin/courses/{course}` | `Admin/Courses/Show` | View course details |
| `/admin/courses/{course}/edit` | `Admin/Courses/Edit` | Edit course |

**Future Routes (Phase 3+):**
- `/admin/courses/{course}/modules` → Manage modules
- `/admin/courses/{course}/enrollments` → View enrollments
- `/admin/courses/{course}/analytics` → View analytics

---

## Page: Course Index (`/admin/courses`)

### Layout
- Header: "Courses" title + "Create Course" button
- Filters: Search, Status filter
- Data table

### Table Columns
| Column | Type | Actions |
|--------|------|---------|
| ID | Number | - |
| Title | Text | Link to show |
| Slug | Text | - |
| Enrollments | Number | - |
| Status | Badge | Toggle publish |
| Created | Date | - |
| Actions | Buttons | Edit, View, Delete |

---

## Page: Course Create (`/admin/courses/create`)

### Form Fields
| Field | Type | Validation |
|-------|------|------------|
| Title | Text | Required, max:255 |
| Slug | Text | Optional, auto-generated, unique |
| Description | Textarea | Optional |
| Status | Select | draft/published, default: draft |

---

## Page: Course Show (`/admin/courses/{course}`)

### Sections
- Header: Title, Slug, Status badge, Edit button
- Overview: Description, dates
- Stats: Enrollments, completion rate, content count

---

## Page: Course Edit (`/admin/courses/{course}/edit`)

Same as Create, pre-filled.

---

## Components to Create

### Phase 1
```
app/Livewire/Admin/
├── Dashboard.php
```

### Phase 2
```
app/Livewire/Admin/
├── Courses/
│   ├── Index.php
│   ├── Create.php
│   ├── Show.php
│   └── Edit.php
```

### Views
```
resources/views/layouts/
├── admin.blade.php
```

---

## Tech Stack

- **Laravel 12** + **Livewire 4**
- **Flux UI** (free components)
- **Tailwind CSS v4**
- **Blade Components**

---

## Notes

- Use Flux UI components: `flux:table`, `flux:button`, `flux:input`, `flux:select`, `flux:modal`, etc.
- Reuse Course model relationships
- Add authorization for Admin/SuperAdmin roles only