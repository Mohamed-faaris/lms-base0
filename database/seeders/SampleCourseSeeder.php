<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\ModuleItemType;
use App\Enums\ProgressStatus;
use App\Enums\QuestionType;
use App\Enums\ScopeType;
use App\Enums\StorageType;
use App\Enums\UserStatus;
use App\Enums\XPAction;
use App\Models\Announcement;
use App\Models\Badge;
use App\Models\Certificate;
use App\Models\ContentAsset;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseModule;
use App\Models\CourseVersion;
use App\Models\Department;
use App\Models\LearningProgress;
use App\Models\ModuleItem;
use App\Models\Organization;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserScope;
use App\Models\VideoSession;
use App\Models\XpTransaction;
use Illuminate\Database\Seeder;

class SampleCourseSeeder extends Seeder
{
    private array $playlist = [
        ['id' => 'zDNaUi2cjv4', 'title' => 'Binary Explained in 01100100 Seconds', 'duration' => 147],
        ['id' => 'B1t4Fjlomi8', 'title' => 'Why do computers use RGB for colors, and not RBY?', 'duration' => 43],
        ['id' => 'UwrZkg6JOOU', 'title' => 'r u even turing complete?', 'duration' => 38],
        ['id' => 'BThr1pb77Fo', 'title' => 'WTF is a Bézier Curve?', 'duration' => 62],
        ['id' => 'U3aXWizDbQ4', 'title' => 'C in 100 Seconds', 'duration' => 145],
        ['id' => 'I4EWvMFj37g', 'title' => 'Bash in 100 Seconds', 'duration' => 153],
        ['id' => 's9F8pu5KfyM', 'title' => 'Why do computers suck at math?', 'duration' => 54],
        ['id' => 'vqs_0W-MSB0', 'title' => 'How a CPU Works in 100 Seconds // Apple Silicon M1 vs Intel i9', 'duration' => 763],
        ['id' => '4Zc9ci9L5wY', 'title' => '7 Fancy words that make you sound like a 10x developer', 'duration' => 505],
        ['id' => 'W2Z7fbCLSTw', 'title' => '7 Database Paradigms', 'duration' => 315],
        ['id' => 'MFhxShGxHWc', 'title' => 'Binary Search Algorithm in 100 Seconds', 'duration' => 142],
        ['id' => '-uleG_Vecis', 'title' => '100+ Computer Science Concepts Explained', 'duration' => 787],
        ['id' => 'erEgovG9WBs', 'title' => '100+ Web Development Things you Should Know', 'duration' => 775],
        ['id' => 'lkIFF4maKMU', 'title' => '100+ JavaScript Concepts you Need to Know', 'duration' => 782],
        ['id' => 'r5NQecwZs1A', 'title' => 'CPU vs GPU vs TPU vs DPU vs QPU', 'duration' => 312],
        ['id' => 'rIrNIzy6U_g', 'title' => '100+ Docker Concepts you Need to Know', 'duration' => 508],
        ['id' => 'LKCVKw9CzFo', 'title' => '100+ Linux Things you Need to Know', 'duration' => 742],
    ];

    private array $modules = [
        [
            'title' => 'Digital Foundations',
            'description' => 'Core concepts that underpin all modern computing systems, from binary representation to fundamental theoretical models.',
            'items' => [0, 1, 2, 3],
        ],
        [
            'title' => 'Programming & Systems',
            'description' => 'Low-level programming languages and the shell environments that developers use to interact with operating systems.',
            'items' => [4, 5],
        ],
        [
            'title' => 'Computer Architecture',
            'description' => 'How hardware and software intersect—CPUs, floating-point arithmetic, and the vocabulary every engineer needs.',
            'items' => [6, 7, 8],
        ],
        [
            'title' => 'Data & Algorithms',
            'description' => 'Foundational data storage paradigms and algorithmic techniques that power modern software.',
            'items' => [9, 10, 11],
        ],
        [
            'title' => 'Modern Technology Landscape',
            'description' => 'A survey of contemporary stacks: web platforms, JavaScript ecosystems, processor diversity, containerization, and Linux.',
            'items' => [12, 13, 14, 15, 16],
        ],
    ];

    public function run(): void
    {
        $admin = $this->makeUser('admin@campus.edu', 'Dr. Alan Turing', 'admin');
        $learner = $this->makeUser('learner@campus.edu', 'Learner User', 'learner');

        $org = Organization::create([
            'name' => 'University of Computing',
            'code' => 'UOC',
            'status' => 'active',
        ]);

        $dept = Department::create([
            'organization_id' => $org->id,
            'name' => 'Department of Computer Science',
            'code' => 'CS',
            'status' => 'active',
        ]);

        foreach ([$admin, $learner] as $u) {
            UserScope::create([
                'user_id' => $u->id,
                'role_id' => 1,
                'scope_type' => ScopeType::ORGANIZATION,
                'organization_id' => $org->id,
                'department_id' => $dept->id,
                'created_by' => $admin->id,
            ]);
        }

        $course = Course::create([
            'title' => 'CS101 - Computer Science Fundamentals',
            'slug' => 'cs101-computer-science-fundamentals',
            'description' => 'A fast-paced survey of computer science covering binary, architecture, programming, algorithms, databases, and modern cloud-native tooling. Built from Fireship\'s CS101 playlist with supplementary reading and assessments.',
            'status' => CourseStatus::PUBLISHED,
            'created_by' => $admin->id,
        ]);

        $version = CourseVersion::create([
            'course_id' => $course->id,
            'version' => '1.0.0',
            'status' => CourseStatus::PUBLISHED,
            'published_at' => now(),
            'created_by' => $admin->id,
        ]);

        $assets = $this->createContentAssets($admin->id);

        $sort = 0;
        foreach ($this->modules as $modData) {
            $sort++;
            $module = CourseModule::create([
                'course_version_id' => $version->id,
                'title' => $modData['title'],
                'description' => $modData['description'],
                'sort_order' => $sort,
            ]);

            $itemSort = 0;
            foreach ($modData['items'] as $idx) {
                $itemSort++;
                ModuleItem::create([
                    'course_module_id' => $module->id,
                    'content_asset_id' => $assets[$idx]->id,
                    'type' => ModuleItemType::VIDEO,
                    'title' => $this->playlist[$idx]['title'],
                    'sort_order' => $itemSort,
                    'required' => true,
                ]);
            }
        }

        $this->addExtraModuleItems($version->id, $admin->id);
        $this->createDigitalFoundationsQuiz($version->id, $admin->id);
        $this->createArchitectureQuiz($version->id, $admin->id);
        $this->createDataAlgorithmsQuiz($version->id, $admin->id);

        $this->createEnrollment($version->id, $learner->id);
        $this->createProgressAndGamification($learner->id);
        $this->createAnnouncementsAndCertificates($org->id, $dept->id, $admin->id);
    }

    private function makeUser(string $email, string $name, string $role): User
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
            'phone' => fake()->phoneNumber(),
            'status' => UserStatus::ACTIVE,
        ]);
    }

    private function createContentAssets(int $userId): array
    {
        $assets = [];
        foreach ($this->playlist as $video) {
            $assets[] = ContentAsset::create([
                'type' => 'video',
                'title' => $video['title'],
                'storage' => StorageType::YOUTUBE,
                'path' => "https://www.youtube.com/watch?v={$video['id']}",
                'metadata' => [
                    'youtube_id' => $video['id'],
                    'duration' => $video['duration'],
                    'allow_seek' => true,
                ],
                'created_by' => $userId,
            ]);
        }
        return $assets;
    }

    private function addExtraModuleItems(int $versionId, int $userId): void
    {
        $extraModule = CourseModule::create([
            'course_version_id' => $versionId,
            'title' => 'Supplementary Materials',
            'description' => 'Additional reading, a self-assessment survey, and curated external resources to deepen your understanding.',
            'sort_order' => 99,
        ]);

        $pdfAsset = ContentAsset::create([
            'type' => 'pdf',
            'title' => 'Binary Number System Reference',
            'storage' => StorageType::LOCAL,
            'path' => 'assets/pdfs/binary-reference.pdf',
            'metadata' => ['pages' => 12, 'file_size' => 240_000],
            'created_by' => $userId,
        ]);

        ModuleItem::create([
            'course_module_id' => $extraModule->id,
            'content_asset_id' => $pdfAsset->id,
            'type' => ModuleItemType::PDF,
            'title' => 'Binary Number System Reference Guide',
            'sort_order' => 1,
            'required' => false,
            'settings' => null,
        ]);

        ModuleItem::create([
            'course_module_id' => $extraModule->id,
            'content_asset_id' => null,
            'type' => ModuleItemType::SURVEY,
            'title' => 'Course Experience Survey',
            'sort_order' => 2,
            'required' => false,
            'settings' => json_encode(['anonymous' => true, 'allow_skip' => true]),
        ]);

        ModuleItem::create([
            'course_module_id' => $extraModule->id,
            'content_asset_id' => null,
            'type' => ModuleItemType::EXTERNAL_LINK,
            'title' => 'Fireship YouTube Channel',
            'sort_order' => 3,
            'required' => false,
            'settings' => json_encode(['url' => 'https://youtube.com/@Fireship', 'open_in_new_tab' => true]),
        ]);
    }

    private function buildQuiz(int $moduleId, string $quizItemTitle, string $quizTitle, array $questions, int $passingMarks = 4, int $duration = 10, int $attemptLimit = 2, bool $shuffle = true): Quiz
    {
        $quizItem = ModuleItem::create([
            'course_module_id' => $moduleId,
            'content_asset_id' => null,
            'type' => ModuleItemType::QUIZ,
            'title' => $quizItemTitle,
            'sort_order' => 99,
            'required' => true,
            'settings' => null,
        ]);

        $quiz = Quiz::create([
            'module_item_id' => $quizItem->id,
            'title' => $quizTitle,
            'passing_marks' => $passingMarks,
            'duration' => $duration,
            'attempt_limit' => $attemptLimit,
            'shuffle_questions' => $shuffle,
        ]);

        $sortQ = 0;
        foreach ($questions as $qData) {
            $sortQ++;
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'type' => $qData['type'],
                'question' => $qData['question'],
                'marks' => $qData['marks'],
                'explanation' => $qData['explanation'],
                'sort_order' => $sortQ,
            ]);

            $optSort = 0;
            foreach ($qData['options'] as $opt) {
                $optSort++;
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['correct'],
                    'sort_order' => $optSort,
                ]);
            }
        }

        return $quiz;
    }

    private function createDigitalFoundationsQuiz(int $versionId, int $userId): void
    {
        $module = CourseModule::where('course_version_id', $versionId)->where('title', 'Digital Foundations')->first();

        $this->buildQuiz(
            $module->id,
            'Digital Foundations Quiz',
            'Binary & Representational Theory',
            [
                [
                    'question' => 'What is the decimal value of the binary number 1010?',
                    'type' => QuestionType::MCQ,
                    'marks' => 1,
                    'explanation' => '1010 in binary equals (1 × 2³) + (0 × 2²) + (1 × 2¹) + (0 × 2⁰) = 8 + 0 + 2 + 0 = 10 in decimal.',
                    'options' => [
                        ['text' => '10', 'correct' => true],
                        ['text' => '8', 'correct' => false],
                        ['text' => '12', 'correct' => false],
                        ['text' => '5', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'A computer monitor uses RGB color mixing because screens emit light rather than reflect it.',
                    'type' => QuestionType::TRUE_FALSE,
                    'marks' => 1,
                    'explanation' => 'RGB is additive color mixing used for emitted light (screens), while RBY is subtractive mixing used for reflected light (paint).',
                    'options' => [
                        ['text' => 'True', 'correct' => true],
                        ['text' => 'False', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Which of the following properties are required for a system to be Turing complete? (Select all that apply)',
                    'type' => QuestionType::MULTIPLE,
                    'marks' => 2,
                    'explanation' => 'Turing completeness requires conditional branching and infinite memory capacity (or equivalent recursion/iteration).',
                    'options' => [
                        ['text' => 'Conditional branching (if/else)', 'correct' => true],
                        ['text' => 'Ability to read and write memory', 'correct' => true],
                        ['text' => 'Graphical display output', 'correct' => false],
                        ['text' => 'Network connectivity', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'A ________ curve uses control points to define smooth paths in vector graphics.',
                    'type' => QuestionType::FILL_BLANK,
                    'marks' => 1,
                    'explanation' => 'Bézier curves are parametric curves defined by control points, widely used in computer graphics and font design.',
                    'options' => [
                        ['text' => 'Bézier', 'correct' => true],
                        ['text' => 'Bezier', 'correct' => true],
                        ['text' => 'bezier', 'correct' => true],
                    ],
                ],
            ],
            passingMarks: 3,
            duration: 8,
            attemptLimit: 3,
        );
    }

    private function createArchitectureQuiz(int $versionId, int $userId): void
    {
        $module = CourseModule::where('course_version_id', $versionId)->where('title', 'Computer Architecture')->first();

        $this->buildQuiz(
            $module->id,
            'Computer Architecture Quiz',
            'CPU Architecture & Performance',
            [
                [
                    'question' => 'What does a CPU primarily use to represent data?',
                    'type' => QuestionType::MCQ,
                    'marks' => 1,
                    'explanation' => 'CPUs operate on binary digits (bits) that represent 0 or 1, forming the foundation of all digital computation.',
                    'options' => [
                        ['text' => 'Binary digits (bits)', 'correct' => true],
                        ['text' => 'Decimal numbers', 'correct' => false],
                        ['text' => 'Hexadecimal characters', 'correct' => false],
                        ['text' => 'Analog signals', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'Floating-point arithmetic is always perfectly accurate.',
                    'type' => QuestionType::TRUE_FALSE,
                    'marks' => 1,
                    'explanation' => 'Floating-point representation has limited precision, leading to small rounding errors in calculations.',
                    'options' => [
                        ['text' => 'True', 'correct' => false],
                        ['text' => 'False', 'correct' => true],
                    ],
                ],
                [
                    'question' => 'Which of the following are types of processor architectures? (Select all that apply)',
                    'type' => QuestionType::MULTIPLE,
                    'marks' => 2,
                    'explanation' => 'CPU, GPU, TPU, and DPU are all specialized processors for different workloads.',
                    'options' => [
                        ['text' => 'CPU', 'correct' => true],
                        ['text' => 'GPU', 'correct' => true],
                        ['text' => 'TPU', 'correct' => true],
                        ['text' => 'PSU', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'A ________ is a curve defined by control points commonly used in computer graphics and animation.',
                    'type' => QuestionType::FILL_BLANK,
                    'marks' => 1,
                    'explanation' => 'Bézier curves use control points to define smooth paths and are fundamental to vector graphics.',
                    'options' => [
                        ['text' => 'Bézier curve', 'correct' => true],
                        ['text' => 'Bezier curve', 'correct' => true],
                        ['text' => 'bezier curve', 'correct' => true],
                    ],
                ],
                [
                    'question' => 'Explain in 2-3 sentences why modern CPUs use caching to improve performance.',
                    'type' => QuestionType::SUBJECTIVE,
                    'marks' => 3,
                    'explanation' => 'Caching stores frequently accessed data closer to the CPU core, reducing latency from main memory access and improving throughput.',
                    'options' => [],
                ],
            ],
            passingMarks: 4,
            duration: 10,
            attemptLimit: 2,
        );
    }

    private function createDataAlgorithmsQuiz(int $versionId, int $userId): void
    {
        $module = CourseModule::where('course_version_id', $versionId)->where('title', 'Data & Algorithms')->first();

        $this->buildQuiz(
            $module->id,
            'Data & Algorithms Quiz',
            'Databases, Search & Complexity',
            [
                [
                    'question' => 'Which data structure is most efficient for searching a sorted list?',
                    'type' => QuestionType::MCQ,
                    'marks' => 1,
                    'explanation' => 'Binary search on a sorted array runs in O(log n) time, making it significantly faster than linear search on large datasets.',
                    'options' => [
                        ['text' => 'Sorted array with binary search', 'correct' => true],
                        ['text' => 'Unsorted linked list', 'correct' => false],
                        ['text' => 'Hash table', 'correct' => false],
                        ['text' => 'Binary search tree (unbalanced)', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'A relational database stores data in documents rather than tables.',
                    'type' => QuestionType::TRUE_FALSE,
                    'marks' => 1,
                    'explanation' => 'Relational databases store data in structured tables with rows and columns. Document stores like MongoDB use documents.',
                    'options' => [
                        ['text' => 'True', 'correct' => false],
                        ['text' => 'False', 'correct' => true],
                    ],
                ],
                [
                    'question' => 'Which of the following are types of database paradigms? (Select all that apply)',
                    'type' => QuestionType::MULTIPLE,
                    'marks' => 2,
                    'explanation' => 'Relational, document, graph, key-value, and columnar are all established database paradigms with different data models.',
                    'options' => [
                        ['text' => 'Relational', 'correct' => true],
                        ['text' => 'Document', 'correct' => true],
                        ['text' => 'Graph', 'correct' => true],
                        ['text' => 'Spreadsheet', 'correct' => false],
                    ],
                ],
                [
                    'question' => 'The time complexity of binary search on a sorted array of n elements is O(log ________).',
                    'type' => QuestionType::FILL_BLANK,
                    'marks' => 1,
                    'explanation' => 'Binary search repeatedly divides the search space in half, giving a logarithmic time complexity of O(log n).',
                    'options' => [
                        ['text' => 'n', 'correct' => true],
                        ['text' => 'log n', 'correct' => true],
                        ['text' => 'N', 'correct' => true],
                    ],
                ],
            ],
            passingMarks: 3,
            duration: 8,
            attemptLimit: 3,
        );
    }

    private function createEnrollment(int $versionId, int $learnerId): void
    {
        $enrollment = CourseEnrollment::create([
            'course_version_id' => $versionId,
            'student_id' => $learnerId,
            'status' => EnrollmentStatus::IN_PROGRESS,
            'started_at' => now()->subDays(2),
        ]);

        $firstModule = CourseModule::where('course_version_id', $versionId)->orderBy('sort_order')->first();
        $firstItems = ModuleItem::where('course_module_id', $firstModule->id)->orderBy('sort_order')->get();

        foreach ($firstItems as $item) {
            $progress = LearningProgress::create([
                'enrollment_id' => $enrollment->id,
                'module_item_id' => $item->id,
                'status' => $item->sort_order === 1 ? ProgressStatus::COMPLETED : ProgressStatus::STARTED,
                'progress' => $item->sort_order === 1 ? 100.00 : 35.00,
                'started_at' => now()->subDays(2),
                'completed_at' => $item->sort_order === 1 ? now()->subDay() : null,
                'time_spent' => $item->sort_order === 1 ? 147 : 52,
                'score' => null,
            ]);

            if ($item->sort_order === 1) {
                VideoSession::create([
                    'progress_id' => $progress->id,
                    'last_second' => 147,
                    'watched_seconds' => 147,
                    'watch_percentage' => 100.00,
                    'seek_attempts' => 2,
                    'pause_count' => 3,
                    'playback_speed' => 1.25,
                    'focus_loss_count' => 1,
                ]);

                XpTransaction::create([
                    'user_id' => $learnerId,
                    'action' => XPAction::COURSE_COMPLETED,
                    'points' => 50,
                    'reference_type' => ModuleItem::class,
                    'reference_id' => $item->id,
                ]);
            }
        }
    }

    private function createProgressAndGamification(int $studentId): void
    {
        $badge = Badge::create([
            'name' => 'Early Adopter',
            'icon' => 'rocket',
            'description' => 'Awarded to students who complete their first module within 48 hours of enrollment.',
        ]);

        $badge->users()->attach($studentId, ['earned_at' => now()->subDay()]);
    }

    private function createAnnouncementsAndCertificates(int $orgId, int $deptId, int $userId): void
    {
        Announcement::create([
            'title' => 'Welcome to CS101 - Spring Semester',
            'content' => 'All enrolled students now have access to the CS101 course materials. Please complete the Digital Foundations module before the end of the first week. Office hours are available every Wednesday at 3 PM in room 4-201.',
            'scope_type' => ScopeType::DEPARTMENT,
            'organization_id' => $orgId,
            'department_id' => $deptId,
            'created_by' => $userId,
            'published_at' => now()->subDays(2),
        ]);

        Certificate::create([
            'name' => 'CS101 Completion Certificate',
            'template' => '<h1>Certificate of Completion</h1><p>This certifies that {{name}} has successfully completed CS101.</p>',
            'rules' => ['required_modules' => 5, 'min_progress' => 80, 'min_quiz_score' => 60],
            'created_by' => $userId,
        ]);
    }
}
