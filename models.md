# Model ER Diagram

This document summarizes the relationships defined in `app/Models`.

## Core entities

- `User`: authentication, enrollment, activity, gamification
- `Course`: top-level learning container
- `Topic`: belongs to a course
- `Module`: belongs to a topic
- `Content`: belongs to a module
- `Quiz` / `Question`: assessment layer
- `Enrollment`, `Progress`, `BadgeAssignment`: pivot-style linking records

## Mermaid ER diagram

```mermaid
erDiagram
    USER ||--o| USER_META : has_one
    USER ||--o{ ENROLLMENT : has_many
    COURSE ||--o{ ENROLLMENT : has_many
    USER }o--o{ COURSE : enrolled_via_enrollments

    COURSE ||--o{ TOPIC : has_many
    TOPIC ||--o{ MODULE : has_many
    MODULE ||--o{ CONTENT : has_many
    COURSE ||--o| COURSE_META : has_one

    CONTENT ||--o{ COMMENT : has_many
    USER ||--o{ COMMENT : has_many
    COMMENT ||--o{ COMMENT : replies

    USER ||--o{ PROGRESS : has_many
    CONTENT ||--o{ PROGRESS : has_many

    USER ||--o{ FEEDBACK : has_many
    COURSE ||--o{ FEEDBACK : has_many

    CONTENT ||--o{ QUIZ : has_many
    QUIZ ||--o{ QUESTION : has_many
    QUESTION }o--|| QUIZ : belongs_to

    MODULE ||--o{ MODULE_QUIZ : has_many
    QUIZ ||--o{ MODULE_QUIZ : has_many

    CONTENT ||--o| END_QUIZ : has_one
    QUIZ ||--o{ END_QUIZ : has_many

    CONTENT ||--o{ TIMESTAMPED_QUIZ : has_many
    QUIZ ||--o{ TIMESTAMPED_QUIZ : has_many

    USER ||--o{ QUIZ_ATTEMPT : has_many
    QUIZ ||--o{ QUIZ_ATTEMPT : has_many

    USER ||--o{ SPEED_LOG : has_many
    CONTENT ||--o{ SPEED_LOG : has_many

    USER ||--o{ STREAK : has_many
    USER ||--o| XP : has_one
    USER ||--o{ XP_LOG : has_many
    USER ||--o{ NOTIFICATION : has_many

    USER }o--o{ BADGE : assigned_via_badge_assignments
    USER ||--o{ BADGE_ASSIGNMENT : has_many
    BADGE ||--o{ BADGE_ASSIGNMENT : has_many

    USER {
        bigint id PK
        string name
        string email
        string password
        enum college
        enum department
        enum role
        string image
    }

    USER_META {
        bigint id PK
        bigint user_id FK
        string phone_number
        string address
    }

    COURSE {
        bigint id PK
        string title
        string slug
        text description
    }

    COURSE_META {
        bigint id PK
        bigint course_id FK
        string category
        string thumbnail
        string difficulty
        string duration
        json data
    }

    TOPIC {
        bigint id PK
        bigint course_id FK
        string name
        text description
        int order
    }

    MODULE {
        bigint id PK
        bigint topic_id FK
        string title
        text description
        int order
    }

    CONTENT {
        bigint id PK
        bigint module_id FK
        int order
        string title
        text body
        enum type
        string content_url
        json content_meta
    }

    COMMENT {
        bigint id PK
        bigint content_id FK
        bigint parent_comment_id FK
        bigint user_id FK
        text comment_text
    }

    ENROLLMENT {
        bigint user_id PK,FK
        bigint course_id PK,FK
        bigint enrolled_by FK
        bigint batch_id
        datetime deadline
        datetime enrolled_at
    }

    PROGRESS {
        bigint user_id PK,FK
        bigint content_id PK,FK
        datetime completed_at
    }

    FEEDBACK {
        bigint id PK
        bigint user_id FK
        bigint course_id FK
        int rating
        text comments
        datetime created_at
    }

    QUIZ {
        bigint id PK
        bigint content_id FK
        bigint question_id FK
    }

    QUESTION {
        bigint id PK
        bigint quiz_id FK
        string type
        text question_text
        json options
        json correct_answer
    }

    MODULE_QUIZ {
        bigint id PK
        bigint module_id FK
        bigint quiz_id FK
    }

    END_QUIZ {
        bigint id PK
        bigint content_id FK
        bigint quiz_id FK
    }

    TIMESTAMPED_QUIZ {
        bigint id PK
        bigint content_id FK
        bigint quiz_id FK
        string timestamp
    }

    QUIZ_ATTEMPT {
        bigint id PK
        bigint user_id FK
        bigint quiz_id FK
        int score
        datetime attempted_at
    }

    SPEED_LOG {
        bigint id PK
        bigint user_id FK
        bigint content_id FK
        enum event
        float speed
        datetime logged_at
    }

    STREAK {
        bigint user_id PK,FK
        date date PK
        int count
    }

    XP {
        bigint user_id PK,FK
        int xp
        datetime updated_at
    }

    XP_LOG {
        bigint id PK
        bigint user_id FK
        int xp_change
        string reason
        datetime created_at
    }

    NOTIFICATION {
        bigint id PK
        bigint user_id FK
        string subject
        text description
        enum status
    }

    BADGE {
        bigint id PK
        string image
        string title
        text description
        json conditions
    }

    BADGE_ASSIGNMENT {
        bigint user_id PK,FK
        bigint badge_id PK,FK
        datetime assigned_at
    }
```

## Relationship summary

### Learning hierarchy

- `Course -> Topic -> Module -> Content` is the main instructional tree.
- `Course` also exposes `contents()` and `quizzes()` / `endQuizzes()` through nested relations.

### Enrollment and progress

- `Enrollment` is the user-course pivot and includes `enrolled_by`, `deadline`, and `enrolled_at`.
- `Progress` is the user-content pivot keyed by `user_id + content_id`.

### Assessments

- `Content` can own quizzes directly through `quizzes()` and also specialized quiz wrappers:
  - `endQuiz()`
  - `timestampedQuizzes()`
- `Quiz` has both:
  - `questions()` as a one-to-many relation
  - `question()` as a direct `belongsTo`
- `ModuleQuiz`, `EndQuiz`, and `TimestampedQuiz` all reference `Quiz`.

### Community and feedback

- `Comment` is self-referential through `parent()` and `replies()`.
- `Feedback` links users to courses.

### Gamification and activity

- `Xp`, `XpLog`, `Streak`, `Badge`, and `BadgeAssignment` form the gamification layer.
- `SpeedLog` records user interactions against content.
- `Notification` belongs to a user.

## Notes from the model code

- `Enrollment`, `Progress`, `Streak`, `Xp`, and `BadgeAssignment` use nonstandard primary key setups in the models.
- `EndQuiz::attempts()` points to `QuizAttempt`, but `QuizAttempt` only declares `quiz_id`, not `end_quiz_id`.
- `Quiz` defines both `questions()` and `question()`, which suggests mixed one-to-many and direct-current-question usage.
- `Content` defines both `quizzes()` and `quiz()`, which also implies mixed collection and single-record access patterns.
- `Notification` is a simple child of `User` with a status enum cast.
