<?php

namespace Database\Seeders;

use App\Enums\ContentType;
use App\Enums\QuizKind;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Course;
use App\Models\CourseMeta;
use App\Models\Enrollment;
use App\Models\Feedback;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\SpeedLog;
use App\Models\Topic;
use App\Models\User;
use App\Models\Xp;
use App\Models\XpLog;
use Illuminate\Database\Seeder;

class JavaCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::firstOrCreate(
            ['slug' => 'java-tutorial-bro-code'],
            [
                'title' => 'Java Tutorial for Beginners - Bro Code',
                'description' => 'A complete Java playlist-backed course covering setup, variables, control flow, arrays, methods, OOP, exceptions, files, GUI, and advanced Java concepts.',
            ]
        );

        CourseMeta::updateOrCreate(
            ['course_id' => $course->id],
            [
                'category' => 'Programming',
                'thumbnail' => 'java-course.png',
                'difficulty' => 'Beginner',
                'duration' => '13+ hours',
                'data' => [
                    'instructor' => 'Bro Code',
                    'playlist_id' => 'PLZPZq0r_RZOOj_NOZYq_R2PECIMglLemc',
                    'playlist_url' => 'https://www.youtube.com/playlist?list=PLZPZq0r_RZOOj_NOZYq_R2PECIMglLemc',
                ],
            ]
        );

        $playlistUrl = 'https://www.youtube.com/watch?v=xk4_1vDrzzo&list=PLZPZq0r_RZOOj_NOZYq_R2PECIMglLemc';

        $topics = [
            [
                'name' => 'Getting Started',
                'description' => 'Introduction to Java, toolchain setup, compilation, and your first program.',
                'modules' => [
                    [
                        'title' => 'Java Basics',
                        'description' => 'Java overview, JDK, IDEs, classes, and the main method.',
                        'contents' => [
                            ['title' => 'Java Introduction', 'body' => 'Understand what Java is and why it is widely used.', 'duration' => '00:00:00'],
                            ['title' => 'Java Compilation', 'body' => 'Learn how Java source becomes bytecode and runs on the JVM.', 'duration' => '00:01:02'],
                            ['title' => 'Project Setup', 'body' => 'Set up a Java project in an IDE and run the first class.', 'duration' => '00:06:06'],
                            ['title' => 'Main Method', 'body' => 'Write the Java entry point and understand method signatures.', 'duration' => '00:09:19'],
                        ],
                        'quiz' => [
                            ['text' => 'What does JDK stand for?', 'options' => ['A' => 'Java Development Kit', 'B' => 'Java Debug Kit', 'C' => 'Java Data Kit', 'D' => 'Java Deployment Kernel'], 'correct' => ['A']],
                            ['text' => 'What is bytecode?', 'options' => ['A' => 'Plain source code', 'B' => 'Platform-independent compiled code', 'C' => 'A GUI element', 'D' => 'A database file'], 'correct' => ['B']],
                            ['text' => 'Which method starts a Java program?', 'options' => ['A' => 'start()', 'B' => 'run()', 'C' => 'main()', 'D' => 'init()'], 'correct' => ['C']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Variables and Input',
                'description' => 'Variables, expressions, user input, math, random values, and GUI input basics.',
                'modules' => [
                    [
                        'title' => 'Variables and Data',
                        'description' => 'Declare variables and understand primitive data types.',
                        'contents' => [
                            ['title' => 'Variables', 'body' => 'Declare and use variables with the correct data types.', 'duration' => '00:20:26'],
                            ['title' => 'Swap Two Variables', 'body' => 'Practice value swapping and temporary variables.', 'duration' => '00:32:58'],
                            ['title' => 'User Input', 'body' => 'Read input from the console and parse values.', 'duration' => '00:36:42'],
                            ['title' => 'Expressions', 'body' => 'Combine variables and operators to form expressions.', 'duration' => '00:44:40'],
                            ['title' => 'Math Class', 'body' => 'Use the Math class for common calculations.', 'duration' => '00:55:01'],
                            ['title' => 'Random Numbers', 'body' => 'Generate random numbers and use them in programs.', 'duration' => '01:01:08'],
                        ],
                        'quiz' => [
                            ['text' => 'Which keyword is used to declare a variable that cannot be reassigned?', 'options' => ['A' => 'static', 'B' => 'final', 'C' => 'const', 'D' => 'fixed'], 'correct' => ['B']],
                            ['text' => 'Which class is commonly used for console input?', 'options' => ['A' => 'Scanner', 'B' => 'Reader', 'C' => 'InputStream', 'D' => 'ConsoleWriter'], 'correct' => ['A']],
                            ['text' => 'What does Math.random() return?', 'options' => ['A' => 'An integer between 1 and 100', 'B' => 'A double between 0 and 1', 'C' => 'A boolean', 'D' => 'A string'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Control Flow',
                'description' => 'If statements, switches, logical operators, and loops.',
                'modules' => [
                    [
                        'title' => 'Conditions and Loops',
                        'description' => 'Control execution flow with conditionals and loops.',
                        'contents' => [
                            ['title' => 'If Statements', 'body' => 'Use if/else logic to branch your program.', 'duration' => '01:05:39'],
                            ['title' => 'Switches', 'body' => 'Use switch statements for multi-branch decisions.', 'duration' => '01:11:51'],
                            ['title' => 'Logical Operators', 'body' => 'Combine conditions with AND and OR logic.', 'duration' => '01:16:36'],
                            ['title' => 'While Loop', 'body' => 'Repeat actions while a condition stays true.', 'duration' => '01:24:33'],
                            ['title' => 'For Loop', 'body' => 'Use for loops for counted iteration.', 'duration' => '01:28:13'],
                            ['title' => 'Nested Loops', 'body' => 'Build two-dimensional iteration patterns.', 'duration' => '01:32:23'],
                        ],
                        'quiz' => [
                            ['text' => 'Which operator is used for logical AND?', 'options' => ['A' => '||', 'B' => '&&', 'C' => '&', 'D' => '!'], 'correct' => ['B']],
                            ['text' => 'What is the difference between while and do-while?', 'options' => ['A' => 'No difference', 'B' => 'do-while runs at least once', 'C' => 'while is always faster', 'D' => 'do-while cannot loop'], 'correct' => ['B']],
                            ['text' => 'Which statement exits a loop early?', 'options' => ['A' => 'exit', 'B' => 'break', 'C' => 'return', 'D' => 'continue'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Arrays and Collections',
                'description' => 'Arrays, 2D arrays, string methods, wrapper classes, ArrayList, and for-each loops.',
                'modules' => [
                    [
                        'title' => 'Working with Arrays',
                        'description' => 'Store and process collections of values.',
                        'contents' => [
                            ['title' => 'Arrays', 'body' => 'Create and access values in single-dimensional arrays.', 'duration' => '01:38:28'],
                            ['title' => '2D Arrays', 'body' => 'Use rows and columns to represent tabular data.', 'duration' => '01:44:54'],
                            ['title' => 'String Methods', 'body' => 'Use built-in string helpers for text processing.', 'duration' => '01:52:59'],
                            ['title' => 'Wrapper Classes', 'body' => 'Understand object wrappers for primitive values.', 'duration' => '01:59:18'],
                            ['title' => 'ArrayList', 'body' => 'Use a resizable collection for dynamic lists.', 'duration' => '02:06:30'],
                            ['title' => 'For-each Loop', 'body' => 'Iterate through collections with enhanced for loops.', 'duration' => '02:17:35'],
                        ],
                        'quiz' => [
                            ['text' => 'How do you access the first array element?', 'options' => ['A' => 'array[0]', 'B' => 'array[1]', 'C' => 'array.first()', 'D' => 'array.get(0)'], 'correct' => ['A']],
                            ['text' => 'What is the default size behavior of an ArrayList?', 'options' => ['A' => 'Fixed size', 'B' => 'Resizable', 'C' => 'Always 1', 'D' => 'Always 100'], 'correct' => ['B']],
                            ['text' => 'What is the length of a 3x4 2D array?', 'options' => ['A' => '3', 'B' => '4', 'C' => '7', 'D' => '12'], 'correct' => ['D']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Methods and Utilities',
                'description' => 'Methods, overloading, printf, and constants.',
                'modules' => [
                    [
                        'title' => 'Methods',
                        'description' => 'Create reusable blocks of code with parameters and return values.',
                        'contents' => [
                            ['title' => 'Methods', 'body' => 'Write and call methods to reduce repetition.', 'duration' => '02:21:20'],
                            ['title' => 'Overloaded Methods', 'body' => 'Use the same method name with different parameter lists.', 'duration' => '02:32:10'],
                            ['title' => 'Printf', 'body' => 'Format output cleanly with printf.', 'duration' => '02:44:48'],
                            ['title' => 'Final Keyword', 'body' => 'Use final to prevent reassignment and extension.', 'duration' => '02:54:26'],
                        ],
                        'quiz' => [
                            ['text' => 'What does method overloading mean?', 'options' => ['A' => 'Multiple methods with the same name', 'B' => 'Methods in another file', 'C' => 'A method that runs twice', 'D' => 'A private method'], 'correct' => ['A']],
                            ['text' => 'What does printf help with?', 'options' => ['A' => 'Input handling', 'B' => 'Formatted output', 'C' => 'File reading', 'D' => 'Networking'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Object Oriented Programming',
                'description' => 'Objects, constructors, inheritance, abstraction, interfaces, polymorphism, and encapsulation.',
                'modules' => [
                    [
                        'title' => 'OOP Fundamentals',
                        'description' => 'Build classes and objects with clean structure.',
                        'contents' => [
                            ['title' => 'Objects (OOP)', 'body' => 'Instantiate objects and access fields and methods.', 'duration' => '03:05:00'],
                            ['title' => 'Constructors', 'body' => 'Initialize new objects using constructors.', 'duration' => '03:17:35'],
                            ['title' => 'Variable Scope', 'body' => 'Understand local, instance, and class scope.', 'duration' => '03:31:42'],
                            ['title' => 'Overloaded Constructors', 'body' => 'Provide multiple ways to construct an object.', 'duration' => '03:41:46'],
                            ['title' => 'toString Method', 'body' => 'Customize object string representation.', 'duration' => '03:54:14'],
                            ['title' => 'Array of Objects', 'body' => 'Store objects in arrays.', 'duration' => '04:04:54'],
                            ['title' => 'Object Passing', 'body' => 'Pass objects into methods safely.', 'duration' => '04:17:40'],
                            ['title' => 'Static Keyword', 'body' => 'Work with class-level members and methods.', 'duration' => '04:28:36'],
                            ['title' => 'Inheritance', 'body' => 'Reuse behavior through class inheritance.', 'duration' => '04:43:42'],
                            ['title' => 'Method Overriding', 'body' => 'Override parent methods in child classes.', 'duration' => '05:01:27'],
                            ['title' => 'Super Keyword', 'body' => 'Call parent constructors and methods.', 'duration' => '05:14:24'],
                            ['title' => 'Abstraction', 'body' => 'Hide implementation details behind abstract classes.', 'duration' => '05:25:36'],
                            ['title' => 'Access Modifiers', 'body' => 'Control visibility with public, private, and protected.', 'duration' => '05:38:50'],
                            ['title' => 'Encapsulation', 'body' => 'Protect data using private fields and getters/setters.', 'duration' => '05:51:18'],
                            ['title' => 'Copy Objects', 'body' => 'Clone or duplicate objects safely.', 'duration' => '06:03:56'],
                            ['title' => 'Interface', 'body' => 'Define contracts for implementing classes.', 'duration' => '06:16:36'],
                            ['title' => 'Polymorphism', 'body' => 'Use shared interfaces to support many forms.', 'duration' => '06:30:46'],
                            ['title' => 'Dynamic Polymorphism', 'body' => 'Resolve overridden methods at runtime.', 'duration' => '06:42:28'],
                        ],
                        'quiz' => [
                            ['text' => 'What is inheritance?', 'options' => ['A' => 'Copying objects', 'B' => 'A class acquiring properties from another class', 'C' => 'Creating arrays', 'D' => 'Grouping methods'], 'correct' => ['B']],
                            ['text' => 'What is polymorphism?', 'options' => ['A' => 'Many variables', 'B' => 'Ability to take many forms', 'C' => 'A file format', 'D' => 'A package name'], 'correct' => ['B']],
                            ['text' => 'Which keyword is used for inheritance?', 'options' => ['A' => 'implements', 'B' => 'extends', 'C' => 'inherits', 'D' => 'super'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Exceptions and Files',
                'description' => 'Exception handling and basic file operations.',
                'modules' => [
                    [
                        'title' => 'Error Handling',
                        'description' => 'Catch and handle exceptions, then read and write files.',
                        'contents' => [
                            ['title' => 'Exception Handling', 'body' => 'Handle runtime errors with try/catch/finally.', 'duration' => '06:57:30'],
                            ['title' => 'File Class', 'body' => 'Work with file paths and metadata.', 'duration' => '07:21:06'],
                            ['title' => 'FileWriter', 'body' => 'Write content to files safely.', 'duration' => '07:35:20'],
                            ['title' => 'FileReader', 'body' => 'Read data back from files.', 'duration' => '07:49:22'],
                        ],
                        'quiz' => [
                            ['text' => 'Why use exception handling?', 'options' => ['A' => 'To increase memory', 'B' => 'To handle errors gracefully', 'C' => 'To make code shorter', 'D' => 'To create arrays'], 'correct' => ['B']],
                            ['text' => 'Which class writes text to a file?', 'options' => ['A' => 'FileWriter', 'B' => 'FileReader', 'C' => 'FileOutput', 'D' => 'WriterInput'], 'correct' => ['A']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'GUI Basics',
                'description' => 'Swing windows, labels, panels, buttons, and layouts.',
                'modules' => [
                    [
                        'title' => 'Swing Basics',
                        'description' => 'Build your first desktop interface with Swing.',
                        'contents' => [
                            ['title' => 'GUI', 'body' => 'Create Swing windows and understand the event-driven model.', 'duration' => '08:04:10'],
                            ['title' => 'Labels', 'body' => 'Display text and images with labels.', 'duration' => '08:18:46'],
                            ['title' => 'Panels', 'body' => 'Group components using panels.', 'duration' => '08:30:22'],
                            ['title' => 'Buttons', 'body' => 'Respond to clicks with action listeners.', 'duration' => '08:41:38'],
                            ['title' => 'BorderLayout', 'body' => 'Arrange components in five regions.', 'duration' => '08:53:04'],
                            ['title' => 'FlowLayout', 'body' => 'Lay out components left to right.', 'duration' => '09:05:30'],
                            ['title' => 'GridLayout', 'body' => 'Build a fixed grid interface.', 'duration' => '09:17:04'],
                            ['title' => 'JLayeredPane', 'body' => 'Stack components in layers.', 'duration' => '09:28:36'],
                            ['title' => 'JOptionPane', 'body' => 'Use dialog popups for alerts and input.', 'duration' => '09:40:08'],
                        ],
                        'quiz' => [
                            ['text' => 'Which layout manager arranges components in regions?', 'options' => ['A' => 'FlowLayout', 'B' => 'BorderLayout', 'C' => 'GridBagLayout', 'D' => 'CardLayout'], 'correct' => ['B']],
                            ['text' => 'Which Swing component is commonly used for pop-up messages?', 'options' => ['A' => 'JPanel', 'B' => 'JOptionPane', 'C' => 'JLabel', 'D' => 'JFrame'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Advanced GUI',
                'description' => 'Text fields, listeners, drag and drop, key bindings, and graphics.',
                'modules' => [
                    [
                        'title' => 'Interactive GUI',
                        'description' => 'Create richer interfaces with inputs and listeners.',
                        'contents' => [
                            ['title' => 'TextField', 'body' => 'Collect typed input from users.', 'duration' => '09:53:40'],
                            ['title' => 'TextArea', 'body' => 'Support multi-line text entry.', 'duration' => '10:05:12'],
                            ['title' => 'Checkbox', 'body' => 'Add boolean UI controls.', 'duration' => '10:17:44'],
                            ['title' => 'RadioButton', 'body' => 'Create mutually exclusive choices.', 'duration' => '10:29:16'],
                            ['title' => 'ComboBox', 'body' => 'Provide selectable dropdown values.', 'duration' => '10:41:30'],
                            ['title' => 'MouseListener', 'body' => 'Respond to mouse clicks and presses.', 'duration' => '10:53:04'],
                            ['title' => 'MouseMotionListener', 'body' => 'React to mouse movement and dragging.', 'duration' => '11:05:32'],
                            ['title' => 'Drag and Drop', 'body' => 'Implement drag-and-drop interactions.', 'duration' => '11:17:46'],
                            ['title' => 'Key Bindings', 'body' => 'Map keyboard actions to commands.', 'duration' => '11:30:08'],
                            ['title' => '2D Graphics', 'body' => 'Draw shapes and images on screen.', 'duration' => '11:43:20'],
                            ['title' => '2D Animation', 'body' => 'Animate graphics with timers and repainting.', 'duration' => '11:55:30'],
                        ],
                        'quiz' => [
                            ['text' => 'Which listener handles keyboard input best?', 'options' => ['A' => 'MouseListener', 'B' => 'KeyListener', 'C' => 'WindowListener', 'D' => 'FocusListener'], 'correct' => ['B']],
                            ['text' => 'What is a primary use of key bindings?', 'options' => ['A' => 'Database access', 'B' => 'Map keys to actions', 'C' => 'File compression', 'D' => 'Network routing'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Advanced Java',
                'description' => 'Generics, serialization, timers, threads, packages, and JAR export.',
                'modules' => [
                    [
                        'title' => 'Modern Java Concepts',
                        'description' => 'Complete the playlist with advanced language features.',
                        'contents' => [
                            ['title' => 'Generics', 'body' => 'Write reusable type-safe code.', 'duration' => '12:07:00'],
                            ['title' => 'Serialization', 'body' => 'Persist object state to a stream or file.', 'duration' => '12:21:30'],
                            ['title' => 'TimerTask', 'body' => 'Schedule repeated background tasks.', 'duration' => '12:35:22'],
                            ['title' => 'Threads', 'body' => 'Work with concurrent execution.', 'duration' => '12:46:08'],
                            ['title' => 'Multithreading', 'body' => 'Coordinate multiple threads safely.', 'duration' => '12:58:30'],
                            ['title' => 'Packages', 'body' => 'Organize code into reusable namespaces.', 'duration' => '13:10:30'],
                            ['title' => 'Compile/Run Command Prompt', 'body' => 'Compile and run Java from the command line.', 'duration' => '13:18:50'],
                            ['title' => 'Executable JAR', 'body' => 'Package a Java program for distribution.', 'duration' => '13:26:30'],
                        ],
                        'quiz' => [
                            ['text' => 'What do generics improve?', 'options' => ['A' => 'Type safety', 'B' => 'Graphics speed', 'C' => 'File size', 'D' => 'GUI layout'], 'correct' => ['A']],
                            ['text' => 'What is the purpose of threads?', 'options' => ['A' => 'Sequential storage', 'B' => 'Concurrent execution', 'C' => 'Sorting arrays', 'D' => 'Formatting output'], 'correct' => ['B']],
                        ],
                    ],
                ],
            ],
        ];

        $createdContents = [];

        foreach ($topics as $topicOrder => $topicData) {
            $topic = Topic::firstOrCreate(
                ['course_id' => $course->id, 'order' => $topicOrder + 1],
                [
                    'name' => 'Module '.($topicOrder + 1).': '.$topicData['name'],
                    'description' => $topicData['description'],
                ]
            );

            foreach ($topicData['modules'] as $moduleOrder => $moduleData) {
                $module = Module::firstOrCreate(
                    ['topic_id' => $topic->id, 'order' => $moduleOrder + 1],
                    [
                        'title' => $moduleData['title'],
                        'description' => $moduleData['description'],
                    ]
                );

                foreach ($moduleData['contents'] as $contentOrder => $contentData) {
                    $content = Content::firstOrCreate(
                        ['module_id' => $module->id, 'order' => $contentOrder + 1],
                        [
                            'title' => $contentData['title'],
                            'body' => $contentData['body'],
                            'type' => ContentType::Video,
                            'content_url' => $playlistUrl,
                            'content_meta' => [
                                'duration' => $contentData['duration'],
                                'playlist_id' => 'PLZPZq0r_RZOOj_NOZYq_R2PECIMglLemc',
                                'module' => $moduleData['title'],
                            ],
                        ]
                    );

                    $createdContents[] = $content;

                    $this->createQuizWithQuestions(
                        $content->id,
                        $this->buildContentQuizQuestions($moduleData['title'], $contentData['title'], $contentData['body']),
                        QuizKind::Timestamped,
                        $this->buildTimestamp($contentData['duration'])
                    );

                }

                if (! empty($moduleData['quiz'])) {
                    $this->createQuizContentForModule($module, $moduleData['title'].' Assessment', $moduleData['quiz']);
                }

                if ($moduleData['title'] === 'Swing Basics') {
                    $content = $module->contents()->where('title', 'GUI')->first();

                    if ($content) {
                        $this->createQuizWithQuestions($content->id, [
                            ['text' => 'What package is Swing part of?', 'options' => ['A' => 'java.awt', 'B' => 'javax.swing', 'C' => 'java.io', 'D' => 'java.sql'], 'correct' => ['B']],
                        ], QuizKind::Timestamped, 10);
                    }
                }
            }
        }

        $finalTopic = Topic::firstOrCreate(
            ['course_id' => $course->id, 'order' => count($topics) + 1],
            [
                'name' => 'Final Assessment',
                'description' => 'Comprehensive exam covering the entire Java course.',
            ]
        );

        $finalModule = Module::firstOrCreate(
            ['topic_id' => $finalTopic->id, 'order' => 1],
            [
                'title' => 'Java Final Assessment',
                'description' => 'Capstone evaluation for the full Java tutorial.',
            ]
        );

        $finalContent = Content::firstOrCreate(
            ['module_id' => $finalModule->id, 'order' => 1],
            [
                'title' => 'Java Course Final Assessment',
                'body' => 'Pass score: 75%, Time limit: 90 minutes.',
                'type' => ContentType::Quiz,
                'content_url' => null,
                'content_meta' => [
                    'is_final_quiz' => true,
                    'pass_score' => 75,
                    'time_limit_minutes' => 90,
                ],
            ]
        );

        $this->createQuizWithQuestions($finalContent->id, [
            ['text' => 'What does OOP stand for?', 'options' => ['A' => 'Object-Oriented Programming', 'B' => 'Object-Output Programming', 'C' => 'Open-Oriented Programming', 'D' => 'Object-Operation Programming'], 'correct' => ['A']],
            ['text' => 'What keyword is used to call a parent constructor?', 'options' => ['A' => 'this', 'B' => 'super', 'C' => 'parent', 'D' => 'base'], 'correct' => ['B']],
            ['text' => 'Which collection is resizable?', 'options' => ['A' => 'Array', 'B' => 'ArrayList', 'C' => 'String', 'D' => 'int'], 'correct' => ['B']],
            ['text' => 'What is the purpose of serialization?', 'options' => ['A' => 'Convert objects to streams', 'B' => 'Sort collections', 'C' => 'Draw graphics', 'D' => 'Manage input'], 'correct' => ['A']],
            ['text' => 'What is the key benefit of multithreading?', 'options' => ['A' => 'Single-task execution', 'B' => 'Concurrent work', 'C' => 'Reduced syntax', 'D' => 'Better file naming'], 'correct' => ['B']],
        ], QuizKind::Content);

        $facultyUsers = User::query()
            ->where('role', 'faculty')
            ->orderBy('id')
            ->get();

        $admin = User::query()->where('role', 'admin')->orWhere('role', 'super_admin')->first();

        if ($facultyUsers->isEmpty() || ! $admin) {
            return;
        }

        $contentTrail = collect($createdContents)->pluck('id')->all();
        $progressPlans = [
            ['progress' => 100, 'xp' => 4200, 'days' => 28],
            ['progress' => 82, 'xp' => 3100, 'days' => 22],
            ['progress' => 65, 'xp' => 2200, 'days' => 16],
            ['progress' => 48, 'xp' => 1500, 'days' => 12],
            ['progress' => 30, 'xp' => 900, 'days' => 8],
        ];

        foreach ($facultyUsers->take(count($progressPlans)) as $index => $user) {
            $plan = $progressPlans[$index];

            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'enrolled_by' => $admin->id,
                    'batch_id' => 3001,
                    'deadline' => now()->addDays(30)->timestamp,
                    'enrolled_at' => now()->subDays(30),
                ]
            );

            $completedCount = (int) round((count($contentTrail) * $plan['progress']) / 100);
            foreach (array_slice($contentTrail, 0, $completedCount) as $contentId) {
                Progress::firstOrCreate(
                    ['user_id' => $user->id, 'content_id' => $contentId],
                    ['completed_at' => now()->subDays(random_int(1, $plan['days']))]
                );
            }

            Xp::updateOrCreate(
                ['user_id' => $user->id],
                ['xp' => $plan['xp']]
            );

            XpLog::create([
                'user_id' => $user->id,
                'xp_change' => $plan['xp'],
                'reason' => 'Java course completion seed',
            ]);
        }

        Comment::firstOrCreate(
            ['content_id' => $createdContents[0]->id, 'user_id' => $facultyUsers->first()->id, 'comment_text' => 'Great introduction to the Java toolchain.'],
            []
        );

        Comment::firstOrCreate(
            ['content_id' => $createdContents[5]->id, 'user_id' => $facultyUsers->first()->id, 'comment_text' => 'Arrays and ArrayList examples are very clear.'],
            []
        );

        Feedback::firstOrCreate(
            ['user_id' => $facultyUsers->first()->id, 'course_id' => $course->id],
            [
                'rating' => 5,
                'comments' => 'Full Java playlist is mapped very well into modules and quizzes.',
            ]
        );

        SpeedLog::firstOrCreate(
            ['user_id' => $facultyUsers->first()->id, 'content_id' => $createdContents[0]->id, 'event' => 'start'],
            ['speed' => 1.00]
        );

        SpeedLog::firstOrCreate(
            ['user_id' => $facultyUsers->first()->id, 'content_id' => $createdContents[0]->id, 'event' => 'pause'],
            ['speed' => 1.25]
        );
    }

    private function createQuizWithQuestions(int $contentId, array $questions, QuizKind $kind = QuizKind::Content, ?int $timestampSeconds = null): Quiz
    {
        $quiz = Quiz::create([
            'content_id' => $contentId,
            'kind' => $kind,
            'timestamp_seconds' => $timestampSeconds,
        ]);

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

        return $quiz;
    }

    private function createQuizContentForModule(Module $module, string $title, array $questions): Quiz
    {
        $quizContent = Content::firstOrCreate(
            [
                'module_id' => $module->id,
                'title' => $title,
            ],
            [
                'order' => ((int) $module->contents()->max('order')) + 1,
                'body' => 'Assessment for '.$module->title,
                'type' => ContentType::Quiz,
                'content_url' => null,
            ]
        );

        return $this->createQuizWithQuestions($quizContent->id, $questions, QuizKind::Content);
    }

    /**
     * @return array<int, array{text:string, options:array<string, string>, correct:array<int, string>}>
     */
    private function buildContentQuizQuestions(string $moduleTitle, string $contentTitle, string $contentBody): array
    {
        return [[
            'text' => 'In '.$moduleTitle.', what is the main focus of "'.$contentTitle.'"?',
            'options' => [
                'A' => $contentBody,
                'B' => 'A completely unrelated topic',
                'C' => 'Only database design',
                'D' => 'Only deployment tooling',
            ],
            'correct' => ['A'],
        ]];
    }

    private function buildTimestamp(string $duration): int
    {
        [$hours, $minutes, $seconds] = array_pad(array_map('intval', explode(':', $duration)), 3, 0);

        $timestamp = ($hours * 3600) + ($minutes * 60) + $seconds;

        return max(10, (int) floor($timestamp / 2));
    }
}
