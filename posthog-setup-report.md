<wizard-report>
# PostHog post-wizard report

The wizard has completed a deep integration of PostHog analytics into this Laravel + Livewire LMS application. The `posthog/posthog-php` SDK (v4.2.0) was installed, a dedicated `PostHogService` was created in `app/Services/`, and PostHog is initialized in `AppServiceProvider` using environment variables. Ten events covering the full learner and admin journey are now captured server-side across 5 files.

| Event | Description | File |
|---|---|---|
| `user_registered` | Fired when a new user completes registration; also identifies the user | `app/Actions/Fortify/CreateNewUser.php` |
| `user_logged_out` | Fired when a user logs out | `app/Livewire/Actions/Logout.php` |
| `course_created` | Fired when an admin creates a new course | `app/Livewire/Admin/Courses/Create.php` |
| `course_updated` | Fired when an admin saves changes to a course | `app/Livewire/Admin/Courses/Edit.php` |
| `enrollment_batch_created` | Fired when an admin enrolls a batch of learners in a course | `app/Livewire/Admin/Enrollments/Create.php` |
| `lesson_completed` | Fired when a learner completes a lesson/content module | `app/Livewire/Faculty/CoursePlayer.php` |
| `course_completed` | Fired when a learner completes all lessons in a course | `app/Livewire/Faculty/CoursePlayer.php` |
| `quiz_submitted` | Fired when a learner submits a quiz, with score and pass/fail result | `app/Livewire/Faculty/CoursePlayer.php` |
| `comment_posted` | Fired when a learner posts a comment on a lesson | `app/Livewire/Faculty/CoursePlayer.php` |
| `comment_reply_posted` | Fired when a learner posts a reply to a comment | `app/Livewire/Faculty/CoursePlayer.php` |

## Next steps

We've built some insights and a dashboard for you to keep an eye on user behavior, based on the events we just instrumented:

- **Dashboard — Analytics basics**: https://us.posthog.com/project/378153/dashboard/1460731
- **New User Registrations** (daily trend): https://us.posthog.com/project/378153/insights/4EE5Jav6
- **Learner Journey Funnel** (registration → first lesson → course completion): https://us.posthog.com/project/378153/insights/2xk3oQsU
- **Course Completions per Week**: https://us.posthog.com/project/378153/insights/WXDDcxEz
- **Quiz Submissions vs Passes** (weekly, with pass filter): https://us.posthog.com/project/378153/insights/pEnJAN39
- **Enrollments Created per Week** (sum of enrolled_count): https://us.posthog.com/project/378153/insights/bgnzRCps

### Agent skill

We've left an agent skill folder in your project. You can use this context for further agent development when using Claude Code. This will help ensure the model provides the most up-to-date approaches for integrating PostHog.

</wizard-report>
