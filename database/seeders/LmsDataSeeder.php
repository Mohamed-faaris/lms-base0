<?php

namespace Database\Seeders;

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\Badge;
use App\Models\BadgeAssignment;
use App\Models\Content;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EndQuiz;
use App\Models\ModelQuiz;
use App\Models\Module;
use App\Models\Progress;
use App\Models\Question;
use App\Models\Streak;
use App\Models\Topic;
use App\Models\User;
use App\Models\Xp;
use Illuminate\Database\Seeder;

class LmsDataSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // Create Faculty Users (5 members)
        // ============================================
        
        // Faculty 1: Dr. Sarah Johnson (CSE department, KRCE college)
        $faculty1 = User::firstOrCreate(
            ['email' => 'sarah@example.com'],
            [
                'name' => 'Dr. Sarah Johnson',
                'role' => Role::Faculty,
                'college' => College::KRCE,
                'department' => Department::CSE,
                'password' => bcrypt('password'),
            ]
        );

        // Faculty 2: Prof. Michael Chen (ECE department, KRCT college)
        $faculty2 = User::firstOrCreate(
            ['email' => 'michael@example.com'],
            [
                'name' => 'Prof. Michael Chen',
                'role' => Role::Faculty,
                'college' => College::KRCT,
                'department' => Department::ECE,
                'password' => bcrypt('password'),
            ]
        );

        // Faculty 3: Dr. Emily Rodriguez (AI department, MKCE college)
        $faculty3 = User::firstOrCreate(
            ['email' => 'emily@example.com'],
            [
                'name' => 'Dr. Emily Rodriguez',
                'role' => Role::Faculty,
                'college' => College::MKCE,
                'department' => Department::AI,
                'password' => bcrypt('password'),
            ]
        );

        // Faculty 4: Prof. David Kim (IT department, KRCE college)
        $faculty4 = User::firstOrCreate(
            ['email' => 'david@example.com'],
            [
                'name' => 'Prof. David Kim',
                'role' => Role::Faculty,
                'college' => College::KRCE,
                'department' => Department::CSE,
                'password' => bcrypt('password'),
            ]
        );

        // Faculty 5: Dr. Lisa Patel (CSE department, KRCT college)
        $faculty5 = User::firstOrCreate(
            ['email' => 'lisa@example.com'],
            [
                'name' => 'Dr. Lisa Patel',
                'role' => Role::Faculty,
                'college' => College::KRCT,
                'department' => Department::CSE,
                'password' => bcrypt('password'),
            ]
        );

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => Role::Admin,
                'password' => bcrypt('password'),
            ]
        );

        // ============================================
        // Create React JS Full Course
        // ============================================
        
        $reactCourse = Course::create([
            'title' => 'React JS Full Course 2024 - Bro Code',
            'description' => 'Complete React JS tutorial from beginners to advanced. Learn React hooks, components, state management, routing, and build real-world applications.',
        ]);

        // ============================================
        // MODULE 1: React Fundamentals
        // ============================================
        
        $topic1 = Topic::create([
            'course_id' => $reactCourse->id,
            'name' => 'Module 1: React Fundamentals',
            'description' => 'Learn the core concepts of React including components, JSX, props, and state.',
            'order' => 1,
        ]);

        $module1 = Module::create([
            'topic_id' => $topic1->id,
            'title' => 'React Fundamentals',
            'description' => 'Core concepts of React including components, JSX, props, and state.',
            'order' => 1,
        ]);

        // Video 1.1: What is React? Introduction
        Content::create([
            'module_id' => $module1->id,
            'order' => 1,
            'title' => '1.1 What is React? Introduction',
            'body' => 'Learn what React is, why it\'s popular, and understand the virtual DOM concept.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=Ke90Tje7VS0',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '15:24',
                'youtube_id' => 'Ke90Tje7VS0'
            ]),
        ]);

        // Video 1.2: Setting Up React Environment
        Content::create([
            'module_id' => $module1->id,
            'order' => 2,
            'title' => '1.2 Setting Up React Environment',
            'body' => 'Install Node.js, npm, and create your first React app using Create React App.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=4UZrsTqkcW4',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '12:18',
                'youtube_id' => '4UZrsTqkcW4'
            ]),
        ]);

        // Video 1.3: JSX Syntax
        Content::create([
            'module_id' => $module1->id,
            'order' => 3,
            'title' => '1.3 JSX Syntax',
            'body' => 'Understanding JSX syntax, embedding expressions, and how JSX differs from HTML.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=7fPXI_MnBOY',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '18:45',
                'youtube_id' => '7fPXI_MnBOY'
            ]),
        ]);

        // Video 1.4: Functional Components
        Content::create([
            'module_id' => $module1->id,
            'order' => 4,
            'title' => '1.4 Functional Components',
            'body' => 'Create and use functional components, component composition, and props.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=N3AkSS5hXMA',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '22:10',
                'youtube_id' => 'N3AkSS5hXMA'
            ]),
        ]);

        // Module 1 Quiz Questions
        $this->createModule1QuizQuestions($module1);

        // ============================================
        // MODULE 2: React Hooks
        // ============================================
        
        $topic2 = Topic::create([
            'course_id' => $reactCourse->id,
            'name' => 'Module 2: React Hooks',
            'description' => 'Master React hooks including useState, useEffect, useContext, useReducer, and custom hooks.',
            'order' => 2,
        ]);

        $module2 = Module::create([
            'topic_id' => $topic2->id,
            'title' => 'React Hooks',
            'description' => 'Master React hooks including useState, useEffect, useContext, useReducer, and custom hooks.',
            'order' => 2,
        ]);

        // Video 2.1: useState Hook
        Content::create([
            'module_id' => $module2->id,
            'order' => 1,
            'title' => '2.1 useState Hook',
            'body' => 'Learn how to manage component state using the useState hook.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=O6P86uwfdR0',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '24:30',
                'youtube_id' => 'O6P86uwfdR0'
            ]),
        ]);

        // Video 2.2: useEffect Hook
        Content::create([
            'module_id' => $module2->id,
            'order' => 2,
            'title' => '2.2 useEffect Hook',
            'body' => 'Handle side effects in functional components with useEffect.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=0ZJgIjIuY7U',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '28:15',
                'youtube_id' => '0ZJgIjIuY7U'
            ]),
        ]);

        // Video 2.3: useContext Hook
        Content::create([
            'module_id' => $module2->id,
            'order' => 3,
            'title' => '2.3 useContext Hook',
            'body' => 'Share data across components without prop drilling using useContext.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=5LrDIWkK_Bc',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '20:45',
                'youtube_id' => '5LrDIWkK_Bc'
            ]),
        ]);

        // Video 2.4: useReducer Hook
        Content::create([
            'module_id' => $module2->id,
            'order' => 4,
            'title' => '2.4 useReducer Hook',
            'body' => 'Manage complex state logic with useReducer hook.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=kK_Wqx3RnHk',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '26:20',
                'youtube_id' => 'kK_Wqx3RnHk'
            ]),
        ]);

        // Video 2.5: Custom Hooks
        Content::create([
            'module_id' => $module2->id,
            'order' => 5,
            'title' => '2.5 Custom Hooks',
            'body' => 'Create reusable custom hooks to share logic between components.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=6ThXsUwLWvc',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '19:55',
                'youtube_id' => '6ThXsUwLWvc'
            ]),
        ]);

        // Module 2 Quiz Questions
        $this->createModule2QuizQuestions($module2);

        // ============================================
        // MODULE 3: React Router
        // ============================================
        
        $topic3 = Topic::create([
            'course_id' => $reactCourse->id,
            'name' => 'Module 3: React Router',
            'description' => 'Implement client-side routing and navigation in React applications using React Router.',
            'order' => 3,
        ]);

        $module3 = Module::create([
            'topic_id' => $topic3->id,
            'title' => 'React Router',
            'description' => 'Implement client-side routing and navigation in React applications.',
            'order' => 3,
        ]);

        // Video 3.1: Introduction to React Router
        Content::create([
            'module_id' => $module3->id,
            'order' => 1,
            'title' => '3.1 Introduction to React Router',
            'body' => 'Learn the basics of React Router and setting up routes.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=Law7wfdg_ls',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '18:30',
                'youtube_id' => 'Law7wfdg_ls'
            ]),
        ]);

        // Video 3.2: Route Parameters
        Content::create([
            'module_id' => $module3->id,
            'order' => 2,
            'title' => '3.2 Route Parameters',
            'body' => 'Handle dynamic routes and access route parameters.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=2k9Q0U0K6qY',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '21:15',
                'youtube_id' => '2k9Q0U0K6qY'
            ]),
        ]);

        // Video 3.3: Nested Routes
        Content::create([
            'module_id' => $module3->id,
            'order' => 3,
            'title' => '3.3 Nested Routes',
            'body' => 'Create nested routes and shared layouts for complex applications.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=JPPoK7R5u5M',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '23:40',
                'youtube_id' => 'JPPoK7R5u5M'
            ]),
        ]);

        // Video 3.4: Navigation Links
        Content::create([
            'module_id' => $module3->id,
            'order' => 4,
            'title' => '3.4 Navigation Links',
            'body' => 'Implement navigation between pages using Link and NavLink components.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=zEQiNFAwDGo',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '16:55',
                'youtube_id' => 'zEQiNFAwDGo'
            ]),
        ]);

        // Module 3 Quiz Questions
        $this->createModule3QuizQuestions($module3);

        // ============================================
        // MODULE 4: Redux State Management
        // ============================================
        
        $topic4 = Topic::create([
            'course_id' => $reactCourse->id,
            'name' => 'Module 4: Redux State Management',
            'description' => 'Master Redux for predictable state management in large React applications.',
            'order' => 4,
        ]);

        $module4 = Module::create([
            'topic_id' => $topic4->id,
            'title' => 'Redux State Management',
            'description' => 'Master Redux for predictable state management in large React applications.',
            'order' => 4,
        ]);

        // Video 4.1: Redux Fundamentals
        Content::create([
            'module_id' => $module4->id,
            'order' => 1,
            'title' => '4.1 Redux Fundamentals',
            'body' => 'Understand the core concepts of Redux: store, actions, and reducers.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=9boMnm5X9ak',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '32:10',
                'youtube_id' => '9boMnm5X9ak'
            ]),
        ]);

        // Video 4.2: React-Redux Integration
        Content::create([
            'module_id' => $module4->id,
            'order' => 2,
            'title' => '4.2 React-Redux Integration',
            'body' => 'Connect React components to Redux store using react-redux.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=9f8QOb9sVbU',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '28:45',
                'youtube_id' => '9f8QOb9sVbU'
            ]),
        ]);

        // Video 4.3: Redux Toolkit
        Content::create([
            'module_id' => $module4->id,
            'order' => 3,
            'title' => '4.3 Redux Toolkit',
            'body' => 'Learn Redux Toolkit, the official, opinionated way to write Redux logic.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=bbkBuqC1rU4',
            'content_meta' => json_encode([
                'has_quiz' => true,
                'duration' => '35:20',
                'youtube_id' => 'bbkBuqC1rU4'
            ]),
        ]);

        // Module 4 Quiz Questions
        $this->createModule4QuizQuestions($module4);

        // ============================================
        // MODULE 5: Real-World Project
        // ============================================
        
        $topic5 = Topic::create([
            'course_id' => $reactCourse->id,
            'name' => 'Module 5: Real-World Project',
            'description' => 'Apply everything you learned to build a complete task management application.',
            'order' => 5,
        ]);

        $module5 = Module::create([
            'topic_id' => $topic5->id,
            'title' => 'Real-World Project',
            'description' => 'Apply everything you learned to build a complete task management application.',
            'order' => 5,
        ]);

        // Video 5.1: Project Setup
        Content::create([
            'module_id' => $module5->id,
            'order' => 1,
            'title' => '5.1 Project Setup',
            'body' => 'Set up the project structure and plan the task management app.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=w7ejDZ8SWv8',
            'content_meta' => json_encode([
                'has_quiz' => false,
                'duration' => '22:15',
                'youtube_id' => 'w7ejDZ8SWv8'
            ]),
        ]);

        // Video 5.2: Building Components
        Content::create([
            'module_id' => $module5->id,
            'order' => 2,
            'title' => '5.2 Building Components',
            'body' => 'Create reusable components for the task management app.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=S66r8py5g1M',
            'content_meta' => json_encode([
                'has_quiz' => false,
                'duration' => '31:40',
                'youtube_id' => 'S66r8py5g1M'
            ]),
        ]);

        // Video 5.3: Implementing State
        Content::create([
            'module_id' => $module5->id,
            'order' => 3,
            'title' => '5.3 Implementing State',
            'body' => 'Add state management to the task management app.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=O5PTSY7f2vM',
            'content_meta' => json_encode([
                'has_quiz' => false,
                'duration' => '27:50',
                'youtube_id' => 'O5PTSY7f2vM'
            ]),
        ]);

        // Video 5.4: Adding Routing
        Content::create([
            'module_id' => $module5->id,
            'order' => 4,
            'title' => '5.4 Adding Routing',
            'body' => 'Implement routing and navigation in the task management app.',
            'type' => 'video',
            'content_url' => 'https://youtube.com/watch?v=l8DCPJ7pG7M',
            'content_meta' => json_encode([
                'has_quiz' => false,
                'duration' => '24:30',
                'youtube_id' => 'l8DCPJ7pG7M'
            ]),
        ]);

        // Module 5 Quiz Questions (Project-based - no quiz as per original spec, but adding for completeness)
        // Note: Module 5 has no quiz per requirements

        // ============================================
        // FINAL COURSE QUIZ
        // ============================================
        
        $finalQuizModule = Module::create([
            'topic_id' => $topic5->id,
            'title' => 'Final Assessment',
            'description' => 'Comprehensive test covering all modules of the React course',
            'order' => 6,
        ]);

        // Final Quiz Content
        $finalQuizContent = Content::create([
            'module_id' => $finalQuizModule->id,
            'order' => 1,
            'title' => 'React Course Final Assessment',
            'body' => 'Comprehensive test covering all modules of the React course. Pass score: 75%, Time limit: 60 minutes.',
            'type' => 'video',
            'content_url' => null,
            'content_meta' => json_encode([
                'has_quiz' => true,
                'is_final_quiz' => true,
                'pass_score' => 75,
                'time_limit_minutes' => 60,
            ]),
        ]);

        // Final Quiz Questions (using EndQuiz for final course quiz)
        $finalQuestions = [
            [
                'text' => 'What is the virtual DOM and how does it improve performance?',
                'options' => [
                    'A' => 'It\'s a direct copy of the real DOM that updates in real-time',
                    'B' => 'It\'s a lightweight JavaScript representation of the DOM that batches updates',
                    'C' => 'It\'s a database for storing DOM elements',
                    'D' => 'It\'s a new HTML specification'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Which hook would you use to perform an action after component renders?',
                'options' => [
                    'A' => 'useState',
                    'B' => 'useEffect',
                    'C' => 'useLayoutEffect',
                    'D' => 'useMemo'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'What is the purpose of the key prop in lists?',
                'options' => [
                    'A' => 'To style list items',
                    'B' => 'To help React identify which items have changed',
                    'C' => 'To order list items',
                    'D' => 'To add event listeners'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'In Redux, what is a reducer?',
                'options' => [
                    'A' => 'A function that returns the new state based on the action',
                    'B' => 'A component that reduces state',
                    'C' => 'A middleware for async actions',
                    'D' => 'A selector for getting state'
                ],
                'correct' => 'A',
            ],
            [
                'text' => 'What is the correct way to update state based on previous state?',
                'options' => [
                    'A' => 'setCount(count + 1)',
                    'B' => 'setCount(prevCount => prevCount + 1)',
                    'C' => 'count = count + 1',
                    'D' => 'this.state.count++'
                ],
                'correct' => 'B',
            ],
        ];

        foreach ($finalQuestions as $qData) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $qData['text'],
                'options' => json_encode($qData['options']),
                'correct_answer' => $qData['correct'],
            ]);
            
            // Use EndQuiz for final course quiz with correct Content ID
            EndQuiz::create([
                'content_id' => $finalQuizContent->id,
                'question_id' => $question->id,
            ]);
        }

        // ============================================
        // ENROLLMENT DATA
        // ============================================
        
        $faculties = [
            ['user' => $faculty1, 'progress' => 85, 'xp' => 2450, 'streak' => 15],
            ['user' => $faculty2, 'progress' => 62, 'xp' => 1800, 'streak' => 7],
            ['user' => $faculty3, 'progress' => 45, 'xp' => 950, 'streak' => 3],
            ['user' => $faculty4, 'progress' => 93, 'xp' => 3200, 'streak' => 22],
            ['user' => $faculty5, 'progress' => 28, 'xp' => 650, 'streak' => 1],
        ];

        // Get all course content (modules + videos)
        $allModules = Module::whereHas('topic', function ($query) use ($reactCourse) {
            $query->where('course_id', $reactCourse->id);
        })->with('contents')->get();

        $allContent = [];
        foreach ($allModules as $module) {
            $allContent[] = $module;
            foreach ($module->contents as $content) {
                $allContent[] = $content;
            }
        }
        $totalContent = count($allContent);

        foreach ($faculties as $facultyData) {
            $user = $facultyData['user'];
            $progressPercent = $facultyData['progress'];
            $xpValue = $facultyData['xp'];
            $streakDays = $facultyData['streak'];

            // Create enrollment (ignore duplicates)
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $reactCourse->id],
                [
                    'enrolled_by' => $admin->id,
                    'deadline' => 30,
                    'enrolled_at' => now()->subDays(30),
                ]
            );

            // Calculate completed content based on progress percentage
            $completedCount = (int) round(($progressPercent / 100) * $totalContent);

            // Create progress records (ignore duplicates)
            for ($i = 0; $i < $completedCount; $i++) {
                if (isset($allContent[$i])) {
                    $contentItem = $allContent[$i];
                    $contentId = $contentItem instanceof Module ? $contentItem->id : $contentItem->id;
                    
                    Progress::firstOrCreate(
                        ['user_id' => $user->id, 'content_id' => $contentId],
                        ['completed_at' => now()->subDays(rand(1, 25))]
                    );
                }
            }

            // Create XP record (update if exists)
            Xp::updateOrCreate(
                ['user_id' => $user->id],
                ['xp' => $xpValue]
            );

            // Create Streak records (ignore duplicates)
            for ($i = 0; $i < $streakDays; $i++) {
                $streakDate = now()->subDays($streakDays - 1 - $i)->toDateString();
                Streak::firstOrCreate(
                    ['user_id' => $user->id, 'date' => $streakDate],
                    ['count' => $i + 1]
                );
            }
        }

        // ============================================
        // PHP COURSE - PHP Full Course 2024 - Bro Code
        // ============================================
        
        $phpCourse = Course::firstOrCreate(
            ['title' => 'PHP Full Course 2024 - Bro Code'],
            [
                'description' => 'Complete PHP tutorial from beginners to advanced. Learn PHP programming, MySQL database integration, form handling, sessions, and build dynamic web applications.',
            ]
        );

        // PHP Module 1: PHP Fundamentals
        $phpTopic1 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 1: PHP Fundamentals',
            'description' => 'Learn the basics of PHP including syntax, variables, and data types.',
            'order' => 1,
        ]);

        $phpModule1 = Module::create([
            'topic_id' => $phpTopic1->id,
            'title' => 'PHP Fundamentals',
            'description' => 'Learn the basics of PHP including syntax, variables, and data types.',
            'order' => 1,
        ]);

        // PHP Videos Module 1
        $this->createPhpModule1Videos($phpModule1);
        $this->createPhpModule1Quiz($phpModule1);

        // PHP Module 2: Control Structures & Functions
        $phpTopic2 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 2: Control Structures & Functions',
            'description' => 'Learn about if/else, loops, and functions in PHP.',
            'order' => 2,
        ]);

        $phpModule2 = Module::create([
            'topic_id' => $phpTopic2->id,
            'title' => 'Control Structures & Functions',
            'description' => 'Learn about if/else, loops, and functions in PHP.',
            'order' => 2,
        ]);

        $this->createPhpModule2Videos($phpModule2);
        $this->createPhpModule2Quiz($phpModule2);

        // PHP Module 3: Arrays & Strings
        $phpTopic3 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 3: Arrays & Strings',
            'description' => 'Learn about indexed, associative, and multidimensional arrays.',
            'order' => 3,
        ]);

        $phpModule3 = Module::create([
            'topic_id' => $phpTopic3->id,
            'title' => 'Arrays & Strings',
            'description' => 'Learn about indexed, associative, and multidimensional arrays.',
            'order' => 3,
        ]);

        $this->createPhpModule3Videos($phpModule3);
        $this->createPhpModule3Quiz($phpModule3);

        // PHP Module 4: Forms & User Input
        $phpTopic4 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 4: Forms & User Input',
            'description' => 'Learn about form handling, validation, and file uploads.',
            'order' => 4,
        ]);

        $phpModule4 = Module::create([
            'topic_id' => $phpTopic4->id,
            'title' => 'Forms & User Input',
            'description' => 'Learn about form handling, validation, and file uploads.',
            'order' => 4,
        ]);

        $this->createPhpModule4Videos($phpModule4);
        $this->createPhpModule4Quiz($phpModule4);

        // PHP Module 5: MySQL Database Integration
        $phpTopic5 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 5: MySQL Database Integration',
            'description' => 'Learn about MySQL database operations in PHP.',
            'order' => 5,
        ]);

        $phpModule5 = Module::create([
            'topic_id' => $phpTopic5->id,
            'title' => 'MySQL Database Integration',
            'description' => 'Learn about MySQL database operations in PHP.',
            'order' => 5,
        ]);

        $this->createPhpModule5Videos($phpModule5);
        $this->createPhpModule5Quiz($phpModule5);

        // PHP Module 6: Sessions & Cookies
        $phpTopic6 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 6: Sessions & Cookies',
            'description' => 'Learn about PHP sessions, cookies, and user authentication.',
            'order' => 6,
        ]);

        $phpModule6 = Module::create([
            'topic_id' => $phpTopic6->id,
            'title' => 'Sessions & Cookies',
            'description' => 'Learn about PHP sessions, cookies, and user authentication.',
            'order' => 6,
        ]);

        $this->createPhpModule6Videos($phpModule6);
        $this->createPhpModule6Quiz($phpModule6);

        // PHP Module 7: Object-Oriented PHP
        $phpTopic7 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 7: Object-Oriented PHP',
            'description' => 'Learn OOP concepts in PHP: classes, objects, inheritance.',
            'order' => 7,
        ]);

        $phpModule7 = Module::create([
            'topic_id' => $phpTopic7->id,
            'title' => 'Object-Oriented PHP',
            'description' => 'Learn OOP concepts in PHP: classes, objects, inheritance.',
            'order' => 7,
        ]);

        $this->createPhpModule7Videos($phpModule7);
        $this->createPhpModule7Quiz($phpModule7);

        // PHP Module 8: Real-World Project
        $phpTopic8 = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Module 8: Building a Real-World Project',
            'description' => 'Apply everything learned to build a complete PHP application.',
            'order' => 8,
        ]);

        $phpModule8 = Module::create([
            'topic_id' => $phpTopic8->id,
            'title' => 'Building a Real-World Project',
            'description' => 'Apply everything learned to build a complete PHP application.',
            'order' => 8,
        ]);

        $this->createPhpModule8Videos($phpModule8);

        // PHP Final Quiz
        $phpFinalTopic = Topic::create([
            'course_id' => $phpCourse->id,
            'name' => 'Final Assessment',
            'description' => 'Comprehensive test covering all PHP modules.',
            'order' => 9,
        ]);

        $phpFinalModule = Module::create([
            'topic_id' => $phpFinalTopic->id,
            'title' => 'PHP Final Assessment',
            'description' => 'Comprehensive test covering all PHP modules.',
            'order' => 9,
        ]);

        $phpFinalQuizContent = Content::create([
            'module_id' => $phpFinalModule->id,
            'order' => 1,
            'title' => 'PHP Course Final Assessment',
            'body' => 'Comprehensive test covering all modules. Pass score: 75%, Time limit: 90 minutes.',
            'type' => 'video',
            'content_url' => null,
            'content_meta' => json_encode([
                'has_quiz' => true,
                'is_final_quiz' => true,
                'pass_score' => 75,
                'time_limit_minutes' => 90,
            ]),
        ]);

        $this->createPhpFinalQuiz($phpFinalModule, $phpFinalQuizContent);

        // PHP Course Enrollments
        $phpEnrollments = [
            ['user' => $faculty1, 'progress' => 72, 'addXp' => 1200],
            ['user' => $faculty2, 'progress' => 88, 'addXp' => 800],
            ['user' => $faculty3, 'progress' => 34, 'addXp' => 450],
            ['user' => $faculty4, 'progress' => 55, 'addXp' => 1500],
            ['user' => $faculty5, 'progress' => 91, 'addXp' => 600],
        ];

        $phpAllModules = Module::whereHas('topic', function ($query) use ($phpCourse) {
            $query->where('course_id', $phpCourse->id);
        })->with('contents')->get();

        $phpAllContent = [];
        foreach ($phpAllModules as $module) {
            $phpAllContent[] = $module;
            foreach ($module->contents as $content) {
                $phpAllContent[] = $content;
            }
        }
        $phpTotalContent = count($phpAllContent);

        foreach ($phpEnrollments as $phpData) {
            $user = $phpData['user'];
            $progressPercent = $phpData['progress'];
            $addXp = $phpData['addXp'];

            // Create PHP enrollment
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $phpCourse->id],
                [
                    'enrolled_by' => $admin->id,
                    'deadline' => 30,
                    'enrolled_at' => now()->subDays(25),
                ]
            );

            // Create PHP progress
            $completedCount = (int) round(($progressPercent / 100) * $phpTotalContent);
            for ($i = 0; $i < $completedCount; $i++) {
                if (isset($phpAllContent[$i])) {
                    $contentItem = $phpAllContent[$i];
                    $contentId = $contentItem instanceof Module ? $contentItem->id : $contentItem->id;
                    
                    Progress::firstOrCreate(
                        ['user_id' => $user->id, 'content_id' => $contentId],
                        ['completed_at' => now()->subDays(rand(1, 20))]
                    );
                }
            }

            // Update XP
            $userXp = Xp::where('user_id', $user->id)->first();
            if ($userXp) {
                $userXp->xp = $userXp->xp + $addXp;
                $userXp->save();
            } else {
                Xp::create(['user_id' => $user->id, 'xp' => $addXp]);
            }
        }

        // ============================================
        // PHP COURSE BADGES
        // ============================================
        
        $phpBadges = [
            ['title' => 'PHP Novice', 'description' => 'For beginners starting their PHP journey', 'image' => 'badge-php-novice.png'],
            ['title' => 'PHP Loop Master', 'description' => 'For completing control structures module', 'image' => 'badge-php-loop-master.png'],
            ['title' => 'Database Expert', 'description' => 'For completing MySQL module', 'image' => 'badge-database-expert.png'],
            ['title' => 'OOP Champion', 'description' => 'For completing OOP module', 'image' => 'badge-oop-champion.png'],
            ['title' => 'PHP Certified', 'description' => 'For completing the full PHP course', 'image' => 'badge-php-certified.png'],
        ];

        $createdPhpBadges = [];
        foreach ($phpBadges as $badgeData) {
            $badge = Badge::firstOrCreate(['title' => $badgeData['title']], $badgeData);
            $createdPhpBadges[$badgeData['title']] = $badge;
        }

        // Assign PHP badges based on progress
        // Faculty 1 (72%): PHP Novice + PHP Loop Master
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Novice']->id, 'user_id' => $faculty1->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Loop Master']->id, 'user_id' => $faculty1->id]);

        // Faculty 2 (88%): PHP Novice + PHP Loop Master + Database Expert + OOP Champion
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Novice']->id, 'user_id' => $faculty2->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Loop Master']->id, 'user_id' => $faculty2->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['Database Expert']->id, 'user_id' => $faculty2->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['OOP Champion']->id, 'user_id' => $faculty2->id]);

        // Faculty 3 (34%): PHP Novice
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Novice']->id, 'user_id' => $faculty3->id]);

        // Faculty 4 (55%): PHP Novice + PHP Loop Master
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Novice']->id, 'user_id' => $faculty4->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Loop Master']->id, 'user_id' => $faculty4->id]);

        // Faculty 5 (91%): All badges
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Novice']->id, 'user_id' => $faculty5->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Loop Master']->id, 'user_id' => $faculty5->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['Database Expert']->id, 'user_id' => $faculty5->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['OOP Champion']->id, 'user_id' => $faculty5->id]);
        BadgeAssignment::firstOrCreate(['badge_id' => $createdPhpBadges['PHP Certified']->id, 'user_id' => $faculty5->id]);

        // ============================================
        // CREATE BADGES
        // ============================================
        
        $badges = [
            [
                'title' => 'React Novice',
                'description' => 'For beginners starting their React journey',
                'image' => 'badge-react-novice.png',
                'conditions' => ['min_progress' => 0],
            ],
            [
                'title' => 'React Hooks Master',
                'description' => 'For completing Module 2 - React Hooks',
                'image' => 'badge-hooks-master.png',
                'conditions' => ['completed_module' => 2],
            ],
            [
                'title' => 'React Router Expert',
                'description' => 'For completing Module 3 - React Router',
                'image' => 'badge-router-expert.png',
                'conditions' => ['completed_module' => 3],
            ],
            [
                'title' => 'Redux Professional',
                'description' => 'For completing Module 4 - Redux',
                'image' => 'badge-redux-pro.png',
                'conditions' => ['completed_module' => 4],
            ],
            [
                'title' => 'React Certified',
                'description' => 'For completing the full React course',
                'image' => 'badge-react-certified.png',
                'conditions' => ['min_progress' => 100],
            ],
        ];

        $createdBadges = [];
        foreach ($badges as $badgeData) {
            $badge = Badge::firstOrCreate(
                ['title' => $badgeData['title']],
                $badgeData
            );
            $createdBadges[$badgeData['title']] = $badge;
        }

        // ============================================
        // ASSIGN BADGES BASED ON PROGRESS
        // ============================================
        
        // Faculty 1 (85% progress): React Novice + React Hooks Master + React Router Expert
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Novice']->id,
            'user_id' => $faculty1->id,
        ], [
            'assigned_at' => now()->subDays(10),
        ]);
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Hooks Master']->id,
            'user_id' => $faculty1->id,
        ], [
            'assigned_at' => now()->subDays(8),
        ]);
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Router Expert']->id,
            'user_id' => $faculty1->id,
        ], [
            'assigned_at' => now()->subDays(5),
        ]);

        // Faculty 2 (62% progress): React Novice + React Hooks Master
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Novice']->id,
            'user_id' => $faculty2->id,
        ], [
            'assigned_at' => now()->subDays(12),
        ]);
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Hooks Master']->id,
            'user_id' => $faculty2->id,
        ], [
            'assigned_at' => now()->subDays(7),
        ]);

        // Faculty 3 (45% progress): React Novice
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Novice']->id,
            'user_id' => $faculty3->id,
        ], [
            'assigned_at' => now()->subDays(15),
        ]);

        // Faculty 4 (93% progress): All badges except React Certified
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Novice']->id,
            'user_id' => $faculty4->id,
        ], [
            'assigned_at' => now()->subDays(20),
        ]);
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Hooks Master']->id,
            'user_id' => $faculty4->id,
        ], [
            'assigned_at' => now()->subDays(15),
        ]);
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Router Expert']->id,
            'user_id' => $faculty4->id,
        ], [
            'assigned_at' => now()->subDays(10),
        ]);
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['Redux Professional']->id,
            'user_id' => $faculty4->id,
        ], [
            'assigned_at' => now()->subDays(5),
        ]);

        // Faculty 5 (28% progress): React Novice
        BadgeAssignment::firstOrCreate([
            'badge_id' => $createdBadges['React Novice']->id,
            'user_id' => $faculty5->id,
        ], [
            'assigned_at' => now()->subDays(5),
        ]);
    }

    /**
     * Module 1 Quiz Questions - React Fundamentals
     */
    private function createModule1QuizQuestions(Module $module): void
    {
        $questions = [
            [
                'text' => 'What is React primarily used for?',
                'options' => [
                    'A' => 'Server-side programming',
                    'B' => 'Building user interfaces',
                    'C' => 'Database management',
                    'D' => 'Mobile app development only'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'What does JSX allow you to do?',
                'options' => [
                    'A' => 'Write SQL queries in JavaScript',
                    'B' => 'Write HTML-like syntax in JavaScript',
                    'C' => 'Style components directly',
                    'D' => 'Create CSS files'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Which of the following is a valid React component?',
                'options' => [
                    'A' => 'function App() { return <h1>Hello</h1>; }',
                    'B' => 'class App { render() { return "Hello"; } }',
                    'C' => 'const App = () => { return "Hello"; }',
                    'D' => 'All of the above are valid'
                ],
                'correct' => 'D',
            ],
            [
                'text' => 'What is the virtual DOM?',
                'options' => [
                    'A' => 'A copy of the actual DOM for better performance',
                    'B' => 'A database for storing DOM elements',
                    'C' => 'A new version of HTML',
                    'D' => 'A React component'
                ],
                'correct' => 'A',
            ],
            [
                'text' => 'How do you pass data from parent to child component?',
                'options' => [
                    'A' => 'Using state',
                    'B' => 'Using props',
                    'C' => 'Using context',
                    'D' => 'Using refs'
                ],
                'correct' => 'B',
            ],
        ];

        foreach ($questions as $qData) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $qData['text'],
                'options' => json_encode($qData['options']),
                'correct_answer' => $qData['correct'],
            ]);
            
            // Link question to module
            ModelQuiz::create([
                'content_id' => $module->id,
                'question_id' => $question->id,
                'timestamp' => 0,
            ]);
        }
    }

    /**
     * Module 2 Quiz Questions - React Hooks
     */
    private function createModule2QuizQuestions(Module $module): void
    {
        $questions = [
            [
                'text' => 'Which hook is used for side effects in functional components?',
                'options' => [
                    'A' => 'useState',
                    'B' => 'useEffect',
                    'C' => 'useContext',
                    'D' => 'useReducer'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'What does the dependency array in useEffect control?',
                'options' => [
                    'A' => 'When the effect runs',
                    'B' => 'The return value of the effect',
                    'C' => 'The state of the component',
                    'D' => 'The props passed to child components'
                ],
                'correct' => 'A',
            ],
            [
                'text' => 'Which hook is best for complex state logic with multiple sub-values?',
                'options' => [
                    'A' => 'useState',
                    'B' => 'useEffect',
                    'C' => 'useReducer',
                    'D' => 'useCallback'
                ],
                'correct' => 'C',
            ],
            [
                'text' => 'What is the primary purpose of useContext?',
                'options' => [
                    'A' => 'To create context',
                    'B' => 'To consume context values',
                    'C' => 'To update context',
                    'D' => 'To delete context'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Custom hooks must start with which prefix?',
                'options' => [
                    'A' => 'hook',
                    'B' => 'use',
                    'C' => 'custom',
                    'D' => 'react'
                ],
                'correct' => 'B',
            ],
        ];

        foreach ($questions as $qData) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $qData['text'],
                'options' => json_encode($qData['options']),
                'correct_answer' => $qData['correct'],
            ]);
            
            ModelQuiz::create([
                'content_id' => $module->id,
                'question_id' => $question->id,
                'timestamp' => 0,
            ]);
        }
    }

    /**
     * Module 3 Quiz Questions - React Router
     */
    private function createModule3QuizQuestions(Module $module): void
    {
        $questions = [
            [
                'text' => 'Which component is used to define routes in React Router?',
                'options' => [
                    'A' => '<Router>',
                    'B' => '<Route>',
                    'C' => '<Routes>',
                    'D' => '<Switch>'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'How do you access route parameters in a component?',
                'options' => [
                    'A' => 'this.params',
                    'B' => 'useParams() hook',
                    'C' => 'params prop',
                    'D' => 'router.params'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'Which component is used for programmatic navigation?',
                'options' => [
                    'A' => '<Link>',
                    'B' => '<NavLink>',
                    'C' => 'useNavigate hook',
                    'D' => '<Redirect>'
                ],
                'correct' => 'C',
            ],
            [
                'text' => 'What is the purpose of nested routes?',
                'options' => [
                    'A' => 'To create multiple pages',
                    'B' => 'To create hierarchical page structures',
                    'C' => 'To optimize performance',
                    'D' => 'To handle errors'
                ],
                'correct' => 'B',
            ],
        ];

        foreach ($questions as $qData) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $qData['text'],
                'options' => json_encode($qData['options']),
                'correct_answer' => $qData['correct'],
            ]);
            
            ModelQuiz::create([
                'content_id' => $module->id,
                'question_id' => $question->id,
                'timestamp' => 0,
            ]);
        }
    }

    /**
     * Module 4 Quiz Questions - Redux
     */
    private function createModule4QuizQuestions(Module $module): void
    {
        $questions = [
            [
                'text' => 'What is the Redux store?',
                'options' => [
                    'A' => 'A component that displays data',
                    'B' => 'A single source of truth for application state',
                    'C' => 'A database connection',
                    'D' => 'A routing library'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'What are Redux actions?',
                'options' => [
                    'A' => 'Functions that modify state directly',
                    'B' => 'Plain objects that describe what happened',
                    'C' => 'React components',
                    'D' => 'Database queries'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'What is the purpose of a reducer?',
                'options' => [
                    'A' => 'To connect components to the store',
                    'B' => 'To return the new state based on the action',
                    'C' => 'To create actions',
                    'D' => 'To dispatch actions'
                ],
                'correct' => 'B',
            ],
            [
                'text' => 'What problem does Redux Toolkit solve?',
                'options' => [
                    'A' => 'It replaces React',
                    'B' => 'It simplifies Redux setup and reduces boilerplate',
                    'C' => 'It adds more complexity',
                    'D' => 'It removes the need for state management'
                ],
                'correct' => 'B',
            ],
        ];

        foreach ($questions as $qData) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $qData['text'],
                'options' => json_encode($qData['options']),
                'correct_answer' => $qData['correct'],
            ]);
            
            ModelQuiz::create([
                'content_id' => $module->id,
                'question_id' => $question->id,
                'timestamp' => 0,
            ]);
        }
    }

    // ============================================
    // PHP COURSE HELPER METHODS
    // ============================================

    private function createPhpModule1Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '1.1 Introduction to PHP', 'url' => 'https://www.youtube.com/watch?v=2eebptXfEvw', 'yt' => '2eebptXfEvw', 'dur' => '18:30'],
            ['order' => 2, 'title' => '1.2 PHP Installation & Setup', 'url' => 'https://www.youtube.com/watch?v=WYufSGgaZL8', 'yt' => 'WYufSGgaZL8', 'dur' => '15:20'],
            ['order' => 3, 'title' => '1.3 PHP Syntax & Variables', 'url' => 'https://www.youtube.com/watch?v=qVU3V0A02k8', 'yt' => 'qVU3V0A02k8', 'dur' => '22:15'],
            ['order' => 4, 'title' => '1.4 Data Types & Operators', 'url' => 'https://www.youtube.com/watch?v=n9DkB5N4YB', 'yt' => 'n9DkB5N4YB', 'dur' => '19:45'],
            ['order' => 5, 'title' => '1.5 Echo & Print Statements', 'url' => 'https://www.youtube.com/watch?v=9zBue5q7j8Q', 'yt' => '9zBue5q7j8Q', 'dur' => '12:30'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule1Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'What does PHP stand for?', 'opts' => ['A' => 'Personal Home Page', 'B' => 'PHP: Hypertext Preprocessor', 'C' => 'Private Hosting Protocol', 'D' => 'Public HTML Processor'], 'correct' => 'B'],
            ['text' => 'Which symbol is used for variable declaration in PHP?', 'opts' => ['A' => '@', 'B' => '&', 'C' => '$', 'D' => '#'], 'correct' => 'C'],
            ['text' => 'How do you write comments in PHP?', 'opts' => ['A' => '// comment', 'B' => '# comment', 'C' => '/* comment */', 'D' => 'All of the above'], 'correct' => 'D'],
            ['text' => 'What is the correct way to end a PHP statement?', 'opts' => ['A' => ';', 'B' => '.', 'C' => ':', 'D' => ','], 'correct' => 'A'],
            ['text' => 'Which of the following is NOT a valid data type in PHP?', 'opts' => ['A' => 'integer', 'B' => 'float', 'C' => 'char', 'D' => 'boolean'], 'correct' => 'C'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule2Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '2.1 If/Else Statements', 'url' => 'https://www.youtube.com/watch?v=3VW0Z1m2l_g', 'yt' => '3VW0Z1m2l_g', 'dur' => '20:15'],
            ['order' => 2, 'title' => '2.2 Switch Statements', 'url' => 'https://www.youtube.com/watch?v=9V6D8J5X0qY', 'yt' => '9V6D8J5X0qY', 'dur' => '16:40'],
            ['order' => 3, 'title' => '2.3 While & For Loops', 'url' => 'https://www.youtube.com/watch?v=J6Xv_c3v8TA', 'yt' => 'J6Xv_c3v8TA', 'dur' => '24:20'],
            ['order' => 4, 'title' => '2.4 Functions & Parameters', 'url' => 'https://www.youtube.com/watch?v=G2k5B2J3x9A', 'yt' => 'G2k5B2J3x9A', 'dur' => '28:10'],
            ['order' => 5, 'title' => '2.5 Variable Scope', 'url' => 'https://www.youtube.com/watch?v=8c5L5n7k5pU', 'yt' => '8c5L5n7k5pU', 'dur' => '14:30'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule2Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'Which loop is guaranteed to execute at least once?', 'opts' => ['A' => 'for', 'B' => 'while', 'C' => 'do-while', 'D' => 'foreach'], 'correct' => 'C'],
            ['text' => 'What is the output of echo 5 == "5";?', 'opts' => ['A' => 'true', 'B' => 'false', 'C' => '5', 'D' => 'error'], 'correct' => 'A'],
            ['text' => 'Which statement is used to exit a loop prematurely?', 'opts' => ['A' => 'exit', 'B' => 'break', 'C' => 'stop', 'D' => 'return'], 'correct' => 'B'],
            ['text' => 'How do you define a function in PHP?', 'opts' => ['A' => 'function myFunction() {}', 'B' => 'def myFunction() {}', 'C' => 'create function myFunction() {}', 'D' => 'new function myFunction() {}'], 'correct' => 'A'],
            ['text' => 'What is the scope of a variable declared inside a function?', 'opts' => ['A' => 'global', 'B' => 'local', 'C' => 'static', 'D' => 'public'], 'correct' => 'B'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule3Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '3.1 Indexed Arrays', 'url' => 'https://www.youtube.com/watch?v=8k5L5n7k5pU', 'yt' => '8k5L5n7k5pU', 'dur' => '18:20'],
            ['order' => 2, 'title' => '3.2 Associative Arrays', 'url' => 'https://www.youtube.com/watch?v=9m5L5n7k5pU', 'yt' => '9m5L5n7k5pU', 'dur' => '20:15'],
            ['order' => 3, 'title' => '3.3 Multidimensional Arrays', 'url' => 'https://www.youtube.com/watch?v=0k5L5n7k5pU', 'yt' => '0k5L5n7k5pU', 'dur' => '25:30'],
            ['order' => 4, 'title' => '3.4 Array Functions', 'url' => 'https://www.youtube.com/watch?v=1k5L5n7k5pU', 'yt' => '1k5L5n7k5pU', 'dur' => '22:45'],
            ['order' => 5, 'title' => '3.5 String Functions', 'url' => 'https://www.youtube.com/watch?v=2k5L5n7k5pU', 'yt' => '2k5L5n7k5pU', 'dur' => '19:10'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule3Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'How do you create an indexed array in PHP?', 'opts' => ['A' => '$arr = array(1,2,3)', 'B' => '$arr = [1,2,3]', 'C' => 'Both A and B', 'D' => 'None'], 'correct' => 'C'],
            ['text' => 'Which function is used to count array elements?', 'opts' => ['A' => 'count()', 'B' => 'sizeof()', 'C' => 'array_count()', 'D' => 'Both A and B'], 'correct' => 'D'],
            ['text' => 'How do you access the first element of an array?', 'opts' => ['A' => '$arr[0]', 'B' => '$arr[1]', 'C' => '$arr.first()', 'D' => '$arr->0'], 'correct' => 'A'],
            ['text' => 'Which function merges two arrays?', 'opts' => ['A' => 'array_merge()', 'B' => 'array_combine()', 'C' => 'array_push()', 'D' => 'array_add()'], 'correct' => 'A'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule4Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '4.1 GET vs POST Methods', 'url' => 'https://www.youtube.com/watch?v=3k5L5n7k5pU', 'yt' => '3k5L5n7k5pU', 'dur' => '16:30'],
            ['order' => 2, 'title' => '4.2 Form Handling', 'url' => 'https://www.youtube.com/watch?v=4k5L5n7k5pU', 'yt' => '4k5L5n7k5pU', 'dur' => '24:15'],
            ['order' => 3, 'title' => '4.3 Form Validation', 'url' => 'https://www.youtube.com/watch?v=5k5L5n7k5pU', 'yt' => '5k5L5n7k5pU', 'dur' => '21:40'],
            ['order' => 4, 'title' => '4.4 File Uploads', 'url' => 'https://www.youtube.com/watch?v=6k5L5n7k5pU', 'yt' => '6k5L5n7k5pU', 'dur' => '28:20'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule4Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'Which method is more secure for sending sensitive data?', 'opts' => ['A' => 'GET', 'B' => 'POST', 'C' => 'Both', 'D' => 'None'], 'correct' => 'B'],
            ['text' => 'How do you access form data in PHP?', 'opts' => ['A' => '$_GET', 'B' => '$_POST', 'C' => '$_REQUEST', 'D' => 'All of the above'], 'correct' => 'D'],
            ['text' => 'Which superglobal contains uploaded files?', 'opts' => ['A' => '$_FILES', 'B' => '$_UPLOAD', 'C' => '$_FILE', 'D' => '$FILES'], 'correct' => 'A'],
            ['text' => 'What is the purpose of form validation?', 'opts' => ['A' => 'Security', 'B' => 'Data integrity', 'C' => 'User experience', 'D' => 'All of the above'], 'correct' => 'D'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule5Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '5.1 Introduction to MySQL', 'url' => 'https://www.youtube.com/watch?v=7k5L5n7k5pU', 'yt' => '7k5L5n7k5pU', 'dur' => '20:30'],
            ['order' => 2, 'title' => '5.2 Connecting to Database', 'url' => 'https://www.youtube.com/watch?v=8k5L5n7k5pU', 'yt' => '8k5L5n7k5pU', 'dur' => '18:45'],
            ['order' => 3, 'title' => '5.3 CRUD Operations', 'url' => 'https://www.youtube.com/watch?v=9k5L5n7k5pU', 'yt' => '9k5L5n7k5pU', 'dur' => '30:15'],
            ['order' => 4, 'title' => '5.4 Prepared Statements', 'url' => 'https://www.youtube.com/watch?v=0k5L5n7k5pU', 'yt' => '0k5L5n7k5pU', 'dur' => '25:20'],
            ['order' => 5, 'title' => '5.5 Fetching Data', 'url' => 'https://www.youtube.com/watch?v=1k5L5n7k5pU', 'yt' => '1k5L5n7k5pU', 'dur' => '22:10'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule5Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'Which MySQL extension is used in PHP?', 'opts' => ['A' => 'mysqli', 'B' => 'mysql', 'C' => 'pdo', 'D' => 'Both A and C'], 'correct' => 'D'],
            ['text' => 'What is the purpose of prepared statements?', 'opts' => ['A' => 'Security (prevent SQL injection)', 'B' => 'Performance', 'C' => 'Code reusability', 'D' => 'None'], 'correct' => 'A'],
            ['text' => 'Which SQL command inserts data?', 'opts' => ['A' => 'SELECT', 'B' => 'INSERT', 'C' => 'UPDATE', 'D' => 'DELETE'], 'correct' => 'B'],
            ['text' => 'What does CRUD stand for?', 'opts' => ['A' => 'Create, Read, Update, Delete', 'B' => 'Copy, Run, Use, Debug', 'C' => 'Create, Return, Update, Deliver', 'D' => 'None'], 'correct' => 'A'],
            ['text' => 'Which function executes SQL queries?', 'opts' => ['A' => 'query()', 'B' => 'execute()', 'C' => 'mysqli_query()', 'D' => 'All of the above'], 'correct' => 'D'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule6Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '6.1 PHP Sessions', 'url' => 'https://www.youtube.com/watch?v=2k5L5n7k5pU', 'yt' => '2k5L5n7k5pU', 'dur' => '19:30'],
            ['order' => 2, 'title' => '6.2 PHP Cookies', 'url' => 'https://www.youtube.com/watch?v=3k5L5n7k5pU', 'yt' => '3k5L5n7k5pU', 'dur' => '16:45'],
            ['order' => 3, 'title' => '6.3 Login Systems', 'url' => 'https://www.youtube.com/watch?v=4k5L5n7k5pU', 'yt' => '4k5L5n7k5pU', 'dur' => '32:20'],
            ['order' => 4, 'title' => '6.4 User Authentication', 'url' => 'https://www.youtube.com/watch?v=5k5L5n7k5pU', 'yt' => '5k5L5n7k5pU', 'dur' => '28:15'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule6Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'How do you start a PHP session?', 'opts' => ['A' => 'session_start()', 'B' => 'start_session()', 'C' => 'init_session()', 'D' => 'begin_session()'], 'correct' => 'A'],
            ['text' => 'How do you create a cookie in PHP?', 'opts' => ['A' => 'setcookie()', 'B' => 'createcookie()', 'C' => 'new cookie()', 'D' => 'make_cookie()'], 'correct' => 'A'],
            ['text' => 'What is the default session lifetime?', 'opts' => ['A' => 'Until browser closes', 'B' => '24 minutes', 'C' => '1 hour', 'D' => 'Until manually destroyed'], 'correct' => 'A'],
            ['text' => 'Which is more secure - sessions or cookies?', 'opts' => ['A' => 'Sessions', 'B' => 'Cookies', 'C' => 'Both same', 'D' => 'None'], 'correct' => 'A'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule7Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '7.1 Classes & Objects', 'url' => 'https://www.youtube.com/watch?v=6k5L5n7k5pU', 'yt' => '6k5L5n7k5pU', 'dur' => '24:30'],
            ['order' => 2, 'title' => '7.2 Constructors & Destructors', 'url' => 'https://www.youtube.com/watch?v=7k5L5n7k5pU', 'yt' => '7k5L5n7k5pU', 'dur' => '20:15'],
            ['order' => 3, 'title' => '7.3 Inheritance', 'url' => 'https://www.youtube.com/watch?v=8k5L5n7k5pU', 'yt' => '8k5L5n7k5pU', 'dur' => '26:40'],
            ['order' => 4, 'title' => '7.4 Polymorphism', 'url' => 'https://www.youtube.com/watch?v=9k5L5n7k5pU', 'yt' => '9k5L5n7k5pU', 'dur' => '22:20'],
            ['order' => 5, 'title' => '7.5 Encapsulation', 'url' => 'https://www.youtube.com/watch?v=0k5L5n7k5pU', 'yt' => '0k5L5n7k5pU', 'dur' => '18:45'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => true, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpModule7Quiz(Module $module): void
    {
        $questions = [
            ['text' => 'What is a class in OOP?', 'opts' => ['A' => 'A blueprint for objects', 'B' => 'An instance of an object', 'C' => 'A function', 'D' => 'A variable'], 'correct' => 'A'],
            ['text' => 'What is a constructor?', 'opts' => ['A' => 'Method called when object is created', 'B' => 'Method called when object is destroyed', 'C' => 'A variable', 'D' => 'None'], 'correct' => 'A'],
            ['text' => 'What is inheritance?', 'opts' => ['A' => 'Creating a new class', 'B' => 'Class acquiring properties of another', 'C' => 'Hiding data', 'D' => 'None'], 'correct' => 'B'],
            ['text' => 'What keyword is used for inheritance?', 'opts' => ['A' => 'extends', 'B' => 'implements', 'C' => 'inherits', 'D' => 'parent'], 'correct' => 'A'],
            ['text' => 'What is encapsulation?', 'opts' => ['A' => 'Hiding internal details', 'B' => 'Inheriting properties', 'C' => 'Creating objects', 'D' => 'None'], 'correct' => 'A'],
        ];
        $this->createPhpQuizQuestions($module, $questions);
    }

    private function createPhpModule8Videos(Module $module): void
    {
        $videos = [
            ['order' => 1, 'title' => '8.1 Project Planning & Setup', 'url' => 'https://www.youtube.com/watch?v=1k5L5n7k5pU', 'yt' => '1k5L5n7k5pU', 'dur' => '25:30'],
            ['order' => 2, 'title' => '8.2 Building the Database', 'url' => 'https://www.youtube.com/watch?v=2k5L5n7k5pU', 'yt' => '2k5L5n7k5pU', 'dur' => '30:15'],
            ['order' => 3, 'title' => '8.3 Creating the Frontend', 'url' => 'https://www.youtube.com/watch?v=3k5L5n7k5pU', 'yt' => '3k5L5n7k5pU', 'dur' => '35:20'],
            ['order' => 4, 'title' => '8.4 Implementing Backend Logic', 'url' => 'https://www.youtube.com/watch?v=4k5L5n7k5pU', 'yt' => '4k5L5n7k5pU', 'dur' => '40:10'],
            ['order' => 5, 'title' => '8.5 Adding Authentication', 'url' => 'https://www.youtube.com/watch?v=5k5L5n7k5pU', 'yt' => '5k5L5n7k5pU', 'dur' => '28:45'],
            ['order' => 6, 'title' => '8.6 Testing & Deployment', 'url' => 'https://www.youtube.com/watch?v=6k5L5n7k5pU', 'yt' => '6k5L5n7k5pU', 'dur' => '22:30'],
        ];
        foreach ($videos as $v) {
            Content::create([
                'module_id' => $module->id, 'order' => $v['order'], 'title' => $v['title'], 'body' => 'Learn about ' . strtolower($v['title']),
                'type' => 'video', 'content_url' => $v['url'],
                'content_meta' => json_encode(['has_quiz' => false, 'duration' => $v['dur'], 'youtube_id' => $v['yt']])
            ]);
        }
    }

    private function createPhpFinalQuiz(Module $module, Content $finalQuizContent): void
    {
        $questions = [
            ['text' => 'What does PHP stand for?', 'opts' => ['A' => 'Personal Home Page', 'B' => 'PHP: Hypertext Preprocessor', 'C' => 'Private Hosting Protocol', 'D' => 'Public HTML Processor'], 'correct' => 'B'],
            ['text' => 'Which loop is guaranteed to execute at least once?', 'opts' => ['A' => 'for', 'B' => 'while', 'C' => 'do-while', 'D' => 'foreach'], 'correct' => 'C'],
            ['text' => 'What is the purpose of prepared statements?', 'opts' => ['A' => 'Security', 'B' => 'Performance', 'C' => 'Code reusability', 'D' => 'None'], 'correct' => 'A'],
            ['text' => 'How do you start a PHP session?', 'opts' => ['A' => 'session_start()', 'B' => 'start_session()', 'C' => 'init_session()', 'D' => 'begin_session()'], 'correct' => 'A'],
            ['text' => 'What is a class in OOP?', 'opts' => ['A' => 'A blueprint for objects', 'B' => 'An instance of an object', 'C' => 'A function', 'D' => 'A variable'], 'correct' => 'A'],
            ['text' => 'Which method is more secure for forms?', 'opts' => ['A' => 'GET', 'B' => 'POST', 'C' => 'Both', 'D' => 'None'], 'correct' => 'B'],
            ['text' => 'What does CRUD stand for?', 'opts' => ['A' => 'Create, Read, Update, Delete', 'B' => 'Copy, Run, Use, Debug', 'C' => 'Create, Return, Update, Deliver', 'D' => 'None'], 'correct' => 'A'],
            ['text' => 'What keyword is used for inheritance?', 'opts' => ['A' => 'extends', 'B' => 'implements', 'C' => 'inherits', 'D' => 'parent'], 'correct' => 'A'],
            ['text' => 'What is encapsulation?', 'opts' => ['A' => 'Hiding internal details', 'B' => 'Inheriting properties', 'C' => 'Creating objects', 'D' => 'None'], 'correct' => 'A'],
            ['text' => 'Which superglobal contains uploaded files?', 'opts' => ['A' => '$_FILES', 'B' => '$_UPLOAD', 'C' => '$_FILE', 'D' => '$FILES'], 'correct' => 'A'],
        ];
        
        foreach ($questions as $q) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $q['text'],
                'options' => json_encode($q['opts']),
                'correct_answer' => $q['correct'],
            ]);
            // Use EndQuiz for final course quiz with correct Content ID
            EndQuiz::create([
                'content_id' => $finalQuizContent->id,
                'question_id' => $question->id,
            ]);
        }
    }

    private function createPhpQuizQuestions(Module $module, array $questions): void
    {
        foreach ($questions as $q) {
            $question = Question::create([
                'type' => 'multiple_choice',
                'question_text' => $q['text'],
                'options' => json_encode($q['opts']),
                'correct_answer' => $q['correct'],
            ]);
            ModelQuiz::create([
                'content_id' => $module->id,
                'question_id' => $question->id,
                'timestamp' => 0,
            ]);
        }
    }
}
