<?php

namespace Database\Seeders;

use App\Enums\ContentType;
use App\Enums\QuizKind;
use App\Models\Content;
use App\Models\Course;
use App\Models\CourseMeta;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class FireShortsCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::firstOrCreate(
            ['slug' => 'fire-shorts'],
            [
                'title' => 'Fire Shorts',
                'description' => 'A curated collection of high-impact programming videos covering web development, tools, and computer science concepts.',
            ]
        );

        CourseMeta::updateOrCreate(
            ['course_id' => $course->id],
            [
                'category' => 'Programming',
                'thumbnail' => 'https://i.ytimg.com/vi/pL7h1tUzrBs/maxresdefault.jpg',
                'difficulty' => 'Intermediate',
                'duration' => '2 hours',
            ]
        );

        $topics = [
            [
                'name' => 'Quick Tips & Tricks',
                'description' => 'Short programming tips and tricks',
                'contents' => [
                    ['title' => '7 Linux Things You Say WRONG', 'duration' => 31, 'id' => 'pL7h1tUzrBs'],
                    ['title' => 'VS Code Path Trick w/ JavaScript', 'duration' => 25, 'id' => 'WpgZKBtW_t8'],
                    ['title' => '4 Steps to Become a Developer', 'duration' => 51, 'id' => 'nvlizC6koSc'],
                    ['title' => 'Reverse Engineer CSS Animations', 'duration' => 39, 'id' => 'ecl-eCbYFPM'],
                    ['title' => 'Delete node_modules like a Pro', 'duration' => 40, 'id' => 'qOSH2pYg6m8'],
                    ['title' => 'Top 3 Ways to Center a DIV with CSS', 'duration' => 37, 'id' => 'njdJeu95p6s'],
                    ['title' => 'Easy Hand-Drawn SVG Animation', 'duration' => 42, 'id' => 'LuWdeuPMHps'],
                    ['title' => 'TODO: Write Good Code Comments', 'duration' => 49, 'id' => 'kt0bfw4YkFk'],
                    ['title' => "I've become my own Arch Enemy", 'duration' => 31, 'id' => 'epH4QvLUXlY'],
                    ['title' => 'Fullstack Development Iceberg', 'duration' => 40, 'id' => 'JMWNYfPIF2U'],
                ],
            ],
            [
                'name' => 'JavaScript Deep Dives',
                'description' => 'In-depth JavaScript concepts and patterns',
                'contents' => [
                    ['title' => 'Async Await try-catch hell', 'duration' => 46, 'id' => 'ITogH7lJTyE'],
                    ['title' => 'Why do computers suck at math?', 'duration' => 54, 'id' => 's9F8pu5KfyM'],
                    ['title' => 'God Tier HTML Programming', 'duration' => 50, 'id' => 'ZtyMdRzvi0w'],
                    ['title' => 'TypeScript is Literal Magic', 'duration' => 26, 'id' => '5JqzCjg4YRU'],
                    ['title' => 'Is your memory leaking?', 'duration' => 40, 'id' => 'XQMVOoPZLYs'],
                    ['title' => 'Why do computers use RGB for colors, and not RBY?', 'duration' => 44, 'id' => 'B1t4Fjlomi8'],
                    ['title' => 'Quick overview of CSS', 'duration' => 33, 'id' => 'OAuZGexox5s'],
                    ['title' => 'This weird skill helps you learn to code faster', 'duration' => 40, 'id' => 'BQBMbwfC6TY'],
                    ['title' => 'Awesome User Avatars Made Easy', 'duration' => 42, 'id' => 'TqnC96-nXAA'],
                    ['title' => 'It works on localhost', 'duration' => 39, 'id' => 'SlBOpNLFUC0'],
                    ['title' => 'WTF is an Abstract Syntax Tree?', 'duration' => 36, 'id' => 'mi6DoxNEN6w'],
                    ['title' => 'i quit using console.log in prod', 'duration' => 39, 'id' => 'o0v41bhlpi0'],
                    ['title' => 'WTF is a Bézier Curve?', 'duration' => 41, 'id' => 'BThr1pb77Fo'],
                    ['title' => 'Web 1.0-beta', 'duration' => 25, 'id' => 'ZIv4hb_Swug'],
                    ['title' => 'r u even turing complete?', 'duration' => 39, 'id' => 'UwrZkg6JOOU'],
                    ['title' => 'my code works… why?', 'duration' => 30, 'id' => '9BvBRXkJA58'],
                ],
            ],
            [
                'name' => 'Web & Development',
                'description' => 'Web development concepts and tools',
                'contents' => [
                    ['title' => 'client got faded, I got paid', 'duration' => 35, 'id' => 'UA7NSpzG98s'],
                    ['title' => '7 killer website features', 'duration' => 48, 'id' => '8B20fRB78nA'],
                    ['title' => 'Database Sharting Explained', 'duration' => 32, 'id' => 'K7iRfOs-aUw'],
                    ['title' => 'JPG vs PNG vs WEBP vs GIF vs SVG', 'duration' => 52, 'id' => 'U_QNznf2FZA'],
                    ['title' => 'get a grip on grep', 'duration' => 44, 'id' => '5_t_I_4OuwQ'],
                    ['title' => 'my code will never stop never stopping', 'duration' => 23, 'id' => '1CDfTpD8dxo'],
                    ['title' => '5 impressive command line tricks', 'duration' => 39, 'id' => 'wYN3KC9lLS0'],
                    ['title' => 'real eyes realize AI lies', 'duration' => 44, 'id' => 'Z8omJ59hNfY'],
                    ['title' => 'how big is a yottabyte?', 'duration' => 33, 'id' => '6V-eRPPLSy0'],
                    ['title' => '10 Strange Mysteries of the Life of the Universe', 'duration' => 1063, 'id' => '9b4acmdG84I'],
                ],
            ],
            [
                'name' => 'Systems & Tools',
                'description' => 'System administration and development tools',
                'contents' => [
                    ['title' => 'the PATH var of righteousness', 'duration' => 33, 'id' => '-J7EWBYipqI'],
                    ['title' => 'Meet SAM… Meta\'s latest AI model', 'duration' => 43, 'id' => 'WrxnpxKarxU'],
                    ['title' => 'Uh oh… AI-search engine for developers has emerged', 'duration' => 27, 'id' => '91IPJ6LFmto'],
                    ['title' => 'A Day in the Life of a Proompt Engineer', 'duration' => 56, 'id' => 'H1sXIUbpRCU'],
                    ['title' => 'Yo mama so FAT32...', 'duration' => 43, 'id' => '0xnuhmqRvVQ'],
                    ['title' => 'my browser, my paste', 'duration' => 26, 'id' => '7bmsDg4BaKw'],
                    ['title' => 'the untold history of web development', 'duration' => 54, 'id' => 'aXcuz6fn8_w'],
                    ['title' => 'real HTML programmers debug in 3D', 'duration' => 50, 'id' => 'gGWQfV1FCis'],
                    ['title' => 'how god programmed birds probably', 'duration' => 40, 'id' => 'X8LglXSG53A'],
                    ['title' => "Let's play… Does your code suck? JavaScript Variables Edition", 'duration' => 38, 'id' => 'ZRjmGq1gAEQ'],
                    ['title' => 'this is just sad...  CrowdStrike attacks a clown website', 'duration' => 49, 'id' => 'tUjjsqRp3mg'],
                    ['title' => '5 life-changing Linux tips', 'duration' => 46, 'id' => 'fwBIZRq-vzY'],
                ],
            ],
        ];

        $topicOrder = 1;
        foreach ($topics as $topicData) {
            $topic = Topic::firstOrCreate(
                ['course_id' => $course->id, 'order' => $topicOrder],
                [
                    'name' => $topicData['name'],
                    'description' => $topicData['description'],
                ]
            );
            $topicOrder++;

            $module = Module::firstOrCreate(
                ['topic_id' => $topic->id, 'order' => 1],
                [
                    'title' => $topicData['name'],
                    'description' => $topicData['description'],
                ]
            );

            $contentOrder = 1;
            foreach ($topicData['contents'] as $video) {
                if ($video['id'] === 'nKwIDjiyXnA' || $video['id'] === 'IUED2ipa_j4') {
                    continue;
                }

                $content = Content::firstOrCreate(
                    ['module_id' => $module->id, 'order' => $contentOrder],
                    [
                        'title' => $video['title'],
                        'body' => 'Learn about '.str_replace([' #Shorts', ' #shorts'], '', $video['title']),
                        'type' => ContentType::Video,
                        'content_url' => 'https://youtu.be/'.$video['id'],
                        'content_meta' => [
                            'youtube_id' => $video['id'],
                            'duration' => $video['duration'],
                            'thumbnail' => 'https://i.ytimg.com/vi/'.$video['id'].'/maxresdefault.jpg',
                        ],
                    ]
                );

                $contentOrder++;
            }
        }

        $contents = Content::whereHas('module.topic', fn ($q) => $q->where('course_id', $course->id))->get();

        foreach ($contents as $content) {
            $this->createEndVideoQuiz($content, $content->title);
        }

        $admin = User::query()->whereIn('role', ['admin', 'super_admin'])->first();

        if ($admin) {
            $users = User::all();
            foreach ($users as $user) {
                Enrollment::firstOrCreate(
                    ['user_id' => $user->id, 'course_id' => $course->id],
                    [
                        'enrolled_by' => $admin->id,
                        'batch_id' => 1,
                        'deadline' => now()->addYear()->timestamp,
                        'enrolled_at' => now(),
                    ]
                );
            }
        }
    }

    private function createModuleQuiz(Module $module, string $moduleName): void
    {
        $quizContent = Content::firstOrCreate(
            [
                'module_id' => $module->id,
                'title' => $moduleName.' Assessment',
            ],
            [
                'order' => ((int) $module->contents()->max('order')) + 1,
                'body' => 'Test your knowledge of '.$moduleName,
                'type' => ContentType::Quiz,
                'content_url' => null,
            ]
        );

        $quiz = Quiz::firstOrCreate(
            ['content_id' => $quizContent->id],
            ['kind' => QuizKind::Content]
        );

        $questions = $this->generateTopicQuestions($moduleName);
        foreach ($questions as $questionData) {
            Question::firstOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['text'],
                ],
                [
                    'type' => 'multiple_choice',
                    'options' => array_values($questionData['options']),
                    'correct_answer' => array_map(
                        static fn (string $letter): int => ord($letter) - 65,
                        $questionData['correct']
                    ),
                ]
            );
        }
    }

    private function generateTopicQuestions(string $topicName): array
    {
        return [
            ['text' => 'Did you find this video helpful?', 'options' => ['A' => 'Yes, very helpful', 'B' => 'Somewhat helpful', 'C' => 'Not helpful', 'D' => 'Already knew this'], 'correct' => ['A']],
            ['text' => 'Would you recommend this video to others?', 'options' => ['A' => 'Yes, definitely', 'B' => 'Maybe', 'C' => 'No', 'D' => 'Not sure'], 'correct' => ['A']],
        ];
    }

    private function createEndCourseQuiz(Course $course): void
    {
        $lastTopic = Topic::where('course_id', $course->id)->orderBy('order', 'desc')->first();

        if (! $lastTopic) {
            return;
        }

        $module = Module::firstOrCreate(
            ['topic_id' => $lastTopic->id, 'order' => 99],
            [
                'title' => 'Course Completion',
                'description' => 'Final assessment for Fire Shorts',
            ]
        );

        $quizContent = Content::firstOrCreate(
            [
                'module_id' => $module->id,
                'title' => 'Course End Quiz',
            ],
            [
                'order' => ((int) $module->contents()->max('order') ?? 0) + 1,
                'body' => 'Test your understanding of all the Fire Shorts content',
                'type' => ContentType::Quiz,
                'content_url' => null,
            ]
        );

        $quiz = Quiz::firstOrCreate(
            ['content_id' => $quizContent->id],
            ['kind' => QuizKind::Timestamped]
        );

        $timestampedQuestions = [
            ['text' => 'What is the best way to handle async errors in JavaScript?', 'options' => ['A' => 'Use try-catch with async/await', 'B' => 'Use .catch() method', 'C' => 'Use callbacks', 'D' => 'Ignore errors'], 'correct' => ['A']],
            ['text' => 'Which CSS property is best for centering elements?', 'options' => ['A' => 'text-align: center', 'B' => 'display: flex with justify-content and align-items', 'C' => 'margin: auto', 'D' => 'float: center'], 'correct' => ['B']],
            ['text' => 'What is an Abstract Syntax Tree (AST)?', 'options' => ['A' => 'A JavaScript framework', 'B' => 'A tree representation of code structure', 'C' => 'A database structure', 'D' => 'A CSS selector'], 'correct' => ['B']],
            ['text' => 'What is database sharding?', 'options' => ['A' => 'Breaking code into shards', 'B' => 'Splitting data across multiple databases', 'C' => 'A CSS technique', 'D' => 'A JavaScript pattern'], 'correct' => ['B']],
            ['text' => 'What is the PATH environment variable used for?', 'options' => ['A' => 'A file path', 'B' => 'Environment variable for executable locations', 'C' => 'A URL', 'D' => 'A database'], 'correct' => ['B']],
        ];

        foreach ($timestampedQuestions as $questionData) {
            Question::firstOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['text'],
                ],
                [
                    'type' => 'multiple_choice',
                    'options' => array_values($questionData['options']),
                    'correct_answer' => array_map(
                        static fn (string $letter): int => ord($letter) - 65,
                        $questionData['correct']
                    ),
                ]
            );
        }

        $contentQuiz = Content::firstOrCreate(
            [
                'module_id' => $module->id,
                'title' => 'Final Assessment',
            ],
            [
                'order' => ((int) $module->contents()->max('order')) + 1,
                'body' => 'Test your overall understanding of Fire Shorts',
                'type' => ContentType::Quiz,
                'content_url' => null,
            ]
        );

        $contentQuizDb = Quiz::firstOrCreate(
            ['content_id' => $contentQuiz->id],
            ['kind' => QuizKind::Content]
        );

        $contentQuestions = [
            ['text' => 'Which CSS property is best for centering elements?', 'options' => ['A' => 'text-align: center', 'B' => 'display: flex with justify-content and align-items', 'C' => 'margin: auto', 'D' => 'float: center'], 'correct' => ['B']],
            ['text' => 'What is database sharding?', 'options' => ['A' => 'Breaking code into shards', 'B' => 'Splitting data across multiple databases', 'C' => 'A CSS technique', 'D' => 'A JavaScript pattern'], 'correct' => ['B']],
            ['text' => 'What is the PATH environment variable used for?', 'options' => ['A' => 'A file path', 'B' => 'Environment variable for executable locations', 'C' => 'A URL', 'D' => 'A database'], 'correct' => ['B']],
            ['text' => 'Which image format supports transparency?', 'options' => ['A' => 'JPEG', 'B' => 'PNG', 'C' => 'BMP', 'D' => 'TIFF'], 'correct' => ['B']],
            ['text' => 'What is grep used for?', 'options' => ['A' => 'Image editing', 'B' => 'Pattern matching and searching', 'C' => 'Database queries', 'D' => 'CSS compilation'], 'correct' => ['B']],
        ];

        foreach ($contentQuestions as $questionData) {
            Question::firstOrCreate(
                [
                    'quiz_id' => $contentQuizDb->id,
                    'question_text' => $questionData['text'],
                ],
                [
                    'type' => 'multiple_choice',
                    'options' => array_values($questionData['options']),
                    'correct_answer' => array_map(
                        static fn (string $letter): int => ord($letter) - 65,
                        $questionData['correct']
                    ),
                ]
            );
        }
    }

    private function createEndVideoQuiz(Content $content, string $videoTitle): void
    {
        $quiz = Quiz::firstOrCreate(
            ['content_id' => $content->id, 'kind' => QuizKind::End]
        );

        $questions = [
            ['text' => 'Did you find this video helpful?', 'options' => ['A' => 'Yes, very helpful', 'B' => 'Somewhat helpful', 'C' => 'Not helpful', 'D' => 'Already knew this'], 'correct' => ['A']],
            ['text' => 'Would you recommend this video to others?', 'options' => ['A' => 'Yes, definitely', 'B' => 'Maybe', 'C' => 'No', 'D' => 'Not sure'], 'correct' => ['A']],
        ];

        foreach ($questions as $questionData) {
            Question::firstOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionData['text'],
                ],
                [
                    'type' => 'multiple_choice',
                    'options' => array_values($questionData['options']),
                    'correct_answer' => array_map(
                        static fn (string $letter): int => ord($letter) - 65,
                        $questionData['correct']
                    ),
                ]
            );
        }
    }
}
