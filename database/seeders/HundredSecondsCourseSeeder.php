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

class HundredSecondsCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::firstOrCreate(
            ['slug' => '100-seconds-of-code'],
            [
                'title' => '100 Seconds of Code',
                'description' => 'Learn programming concepts in 100 seconds each. A fast-paced playlist covering essential topics in software development.',
            ]
        );

        CourseMeta::updateOrCreate(
            ['course_id' => $course->id],
            [
                'category' => 'Programming',
                'thumbnail' => 'https://i.ytimg.com/vi/DC471a9qrU4/maxresdefault.jpg',
                'difficulty' => 'Beginner',
                'duration' => '4 hours',
            ]
        );

        $topics = [
            [
                'name' => 'JavaScript & Frontend',
                'description' => 'JavaScript, frameworks, and frontend technologies',
                'contents' => [
                    ['title' => 'Array Map in 100 Seconds', 'duration' => 101, 'id' => 'DC471a9qrU4'],
                    ['title' => 'Big-O Notation in 100 Seconds', 'duration' => 100, 'id' => 'g2o22C3CRfU'],
                    ['title' => 'Array Reduce in 100 seconds', 'duration' => 101, 'id' => 'tVCYa_bnITg'],
                    ['title' => 'Recursion in 100 Seconds', 'duration' => 100, 'id' => 'rf60MejMz3E'],
                    ['title' => 'JavaScript Modules in 100 Seconds', 'duration' => 104, 'id' => 'qgRUr-YUk1Q'],
                    ['title' => 'React in 100 Seconds', 'duration' => 128, 'id' => 'Tn6-PIqc4UM'],
                    ['title' => 'Vue.js Explained in 100 Seconds', 'duration' => 124, 'id' => 'nhBVL41-_Cw'],
                    ['title' => 'Angular in 100 Seconds', 'duration' => 120, 'id' => 'Ata9cSC2WpM'],
                    ['title' => 'Svelte in 100 Seconds', 'duration' => 132, 'id' => 'rv3Yq-B8qp4'],
                    ['title' => 'Next.js in 100 Seconds', 'duration' => 712, 'id' => 'Sklc_fQBmcs'],
                    ['title' => 'Nuxt in 100 Seconds', 'duration' => 170, 'id' => 'dCxSsr5xuL8'],
                    ['title' => 'SvelteKit in 100 seconds', 'duration' => 166, 'id' => 'H1eEFfAkIik'],
                    ['title' => 'Solid in 100 Seconds', 'duration' => 152, 'id' => 'hw3Bx5vxKl0'],
                    ['title' => 'Redux in 100 Seconds', 'duration' => 153, 'id' => '_shA5Xwe8_4'],
                    ['title' => 'React Query in 100 Seconds', 'duration' => 153, 'id' => 'novnyCaa7To'],
                    ['title' => 'React Native in 100 Seconds', 'duration' => 137, 'id' => 'gvkqT_Uoahw'],
                    ['title' => 'TypeScript in 100 Seconds', 'duration' => 145, 'id' => 'zQnBQ4tB3ZA'],
                    ['title' => 'JS Destructuring in 100 Seconds', 'duration' => 211, 'id' => 'UgEaJBz3bjY'],
                    ['title' => 'What is THIS in JavaScript? in 100 seconds', 'duration' => 398, 'id' => 'YOlr79NaAtQ'],
                    ['title' => 'Closures Explained in 100 Seconds', 'duration' => 297, 'id' => 'vKJpN5FAeF4'],
                    ['title' => 'jQuery in 100 Seconds', 'duration' => 131, 'id' => 'UU-GebNqdbg'],
                ],
            ],
            [
                'name' => 'CSS & Styling',
                'description' => 'CSS, styling tools, and design frameworks',
                'contents' => [
                    ['title' => 'CSS Flexbox in 100 Seconds', 'duration' => 104, 'id' => 'K74l26pE4YA'],
                    ['title' => 'CSS Grid in 100 Seconds', 'duration' => 111, 'id' => 'uuOXPWCh-6o'],
                    ['title' => 'CSS Variables in 100 Seconds', 'duration' => 116, 'id' => 'NtRmIp4eMjs'],
                    ['title' => 'CSS Animation in 100 Seconds', 'duration' => 125, 'id' => 'HZHHBwzmJLk'],
                    ['title' => 'CSS Pseudo-classes in 100 Seconds', 'duration' => 120, 'id' => 'kpXKwDGtjGE'],
                    ['title' => 'CSS Pseudo-elements in 100 Seconds', 'duration' => 117, 'id' => 'e1KpKBHJOrA'],
                    ['title' => 'Tailwind in 100 Seconds', 'duration' => 141, 'id' => 'mr15Xzb1Ook'],
                    ['title' => 'Sass in 100 Seconds', 'duration' => 151, 'id' => 'akDIJa0AP5c'],
                    ['title' => 'PostCSS in 100 Seconds', 'duration' => 133, 'id' => 'WhCXiEwdU1A'],
                    ['title' => 'CSS in 100 Seconds', 'duration' => 140, 'id' => 'OEV8gMkCHXQ'],
                ],
            ],
            [
                'name' => 'Backend & APIs',
                'description' => 'Backend technologies, APIs, and server-side development',
                'contents' => [
                    ['title' => 'Deno in 100 Seconds', 'duration' => 130, 'id' => 'F0G9lZ7gecE'],
                    ['title' => 'Bun in 100 Seconds', 'duration' => 166, 'id' => 'M4TufsFlv_o'],
                    ['title' => 'NestJS in 100 Seconds', 'duration' => 141, 'id' => '0M8AYU_hPas'],
                    ['title' => 'RESTful APIs in 100 Seconds', 'duration' => 680, 'id' => '-MTSQjw5DrM'],
                    ['title' => 'GraphQL Explained in 100 Seconds', 'duration' => 142, 'id' => 'eIQh02xuVw4'],
                    ['title' => 'Session vs Token Authentication in 100 Seconds', 'duration' => 138, 'id' => 'UBUNrFtufWo'],
                    ['title' => 'WebSockets in 100 Seconds', 'duration' => 511, 'id' => '1BfCnjr_Vjg'],
                    ['title' => 'Serverless Computing in 100 Seconds', 'duration' => 1006, 'id' => 'W_VV2Fx32_Y'],
                ],
            ],
            [
                'name' => 'Databases',
                'description' => 'Database systems and data storage',
                'contents' => [
                    ['title' => 'SQL Explained in 100 Seconds', 'duration' => 143, 'id' => 'zsjvFFKOm3c'],
                    ['title' => 'PostgreSQL in 100 Seconds', 'duration' => 157, 'id' => 'n2Fluyr3lbc'],
                    ['title' => 'MongoDB in 100 Seconds', 'duration' => 147, 'id' => '-bt_y4Loofg'],
                    ['title' => 'Redis in 100 Seconds', 'duration' => 146, 'id' => 'G1rOthIU-uo'],
                    ['title' => 'Firebase in 100 Seconds', 'duration' => 155, 'id' => 'vAoB4VbhRzM'],
                    ['title' => 'Supabase in 100 Seconds', 'duration' => 157, 'id' => 'zBZgdTb-dns'],
                    ['title' => 'Neo4j in 100 Seconds', 'duration' => 157, 'id' => 'T6L9EoBy8Zk'],
                    ['title' => 'Cassandra in 100 Seconds', 'duration' => 146, 'id' => 'ziq7FUKpCS8'],
                    ['title' => 'SurrealDB in 100 Seconds', 'duration' => 167, 'id' => 'C7WFwgDRStM'],
                    ['title' => 'TimescaleDB in 100 Seconds', 'duration' => 154, 'id' => '69Tzh_0lHJ8'],
                    ['title' => 'DuckDB in 100 Seconds', 'duration' => 143, 'id' => 'uHm6FEb2Re4'],
                ],
            ],
            [
                'name' => 'DevOps & Cloud',
                'description' => 'DevOps, cloud platforms, and infrastructure',
                'contents' => [
                    ['title' => 'Docker in 100 Seconds', 'duration' => 127, 'id' => 'Gjnup-PuquQ'],
                    ['title' => 'Kubernetes Explained in 100 Seconds', 'duration' => 127, 'id' => 'PziYflu8cB8'],
                    ['title' => 'Terraform in 100 Seconds', 'duration' => 138, 'id' => 'tomUWcQ0P3k'],
                    ['title' => 'Ansible in 100 Seconds', 'duration' => 154, 'id' => 'xRMPKQweySE'],
                    ['title' => 'AWS for the Haters in 100 Seconds', 'duration' => 150, 'id' => 'ZzI9JE0i6Lc'],
                    ['title' => 'Git Explained in 100 Seconds', 'duration' => 117, 'id' => 'hwP7WQkmECE'],
                    ['title' => 'GitHub Pull Request in 100 Seconds', 'duration' => 112, 'id' => '8lGpZkjnkt4'],
                    ['title' => 'NGINX Explained in 100 Seconds', 'duration' => 125, 'id' => 'JKxlsvZXG7c'],
                    ['title' => 'DevOps CI/CD Explained in 100 Seconds', 'duration' => 116, 'id' => 'scEDHsr3APg'],
                ],
            ],
            [
                'name' => 'Programming Languages',
                'description' => 'Various programming languages',
                'contents' => [
                    ['title' => 'Python in 100 Seconds', 'duration' => 144, 'id' => 'x7X9w_GIm1s'],
                    ['title' => 'Java in 100 Seconds', 'duration' => 145, 'id' => 'l9AzO1FMgM8'],
                    ['title' => 'Go in 100 Seconds', 'duration' => 150, 'id' => '446E-r0rXHI'],
                    ['title' => 'Rust in 100 Seconds', 'duration' => 149, 'id' => '5C_HPTJg5ek'],
                    ['title' => 'C in 100 Seconds', 'duration' => 145, 'id' => 'U3aXWizDbQ4'],
                    ['title' => 'C++ in 100 Seconds', 'duration' => 166, 'id' => 'MNeX4EGtR5Y'],
                    ['title' => 'C# in 100 Seconds', 'duration' => 147, 'id' => 'ravLFzIguCM'],
                    ['title' => 'PHP in 100 Seconds', 'duration' => 141, 'id' => 'a7_WFUlFS94'],
                    ['title' => 'Ruby in 100 Seconds', 'duration' => 157, 'id' => 'UYm0kfnRTJk'],
                    ['title' => 'Swift in 100 Seconds', 'duration' => 145, 'id' => 'nAchMctX4YA'],
                    ['title' => 'Kotlin in 100 Seconds', 'duration' => 142, 'id' => 'xT8oP0wy-A0'],
                    ['title' => 'Dart in 100 Seconds', 'duration' => 151, 'id' => 'NrO0CJCbYLA'],
                    ['title' => 'Haskell in 100 Seconds', 'duration' => 150, 'id' => 'Qa8IfEeBJqk'],
                    ['title' => 'Elixir in 100 Seconds', 'duration' => 155, 'id' => 'R7t7zca8SyM'],
                    ['title' => 'Scala in 100 Seconds', 'duration' => 208, 'id' => 'I7-hxTbpscU'],
                    ['title' => 'Lua in 100 Seconds', 'duration' => 144, 'id' => 'jUuqBZwwkQw'],
                    ['title' => 'Perl in 100 Seconds', 'duration' => 146, 'id' => '74_7LrRe5DI'],
                    ['title' => 'COBOL in 100 seconds', 'duration' => 123, 'id' => '7d7-etf-wNI'],
                    ['title' => 'FORTRAN in 100 Seconds', 'duration' => 159, 'id' => 'NMWzgy8FsKs'],
                    ['title' => 'Assembly Language in 100 Seconds', 'duration' => 164, 'id' => '4gwYkEK0gOk'],
                    ['title' => 'Brainf**k in 100 Seconds', 'duration' => 130, 'id' => 'hdHjjBS4cs8'],
                ],
            ],
            [
                'name' => 'Mobile & Desktop',
                'description' => 'Mobile and desktop development frameworks',
                'contents' => [
                    ['title' => 'Flutter in 100 seconds', 'duration' => 130, 'id' => 'lHhRhPV--G0'],
                    ['title' => 'Expo in 100 Seconds', 'duration' => 159, 'id' => 'vFW_TxKLyrE'],
                    ['title' => 'Electron JS in 100 Seconds', 'duration' => 109, 'id' => 'm3OjWNFREJo'],
                    ['title' => 'Tauri in 100 Seconds', 'duration' => 160, 'id' => '-X8evddpu7M'],
                    ['title' => 'Unity in 100 Seconds', 'duration' => 165, 'id' => 'iqlH4okiQqg'],
                    ['title' => 'Godot in 100 Seconds', 'duration' => 160, 'id' => 'QKgTZWbwD1U'],
                    ['title' => 'Unreal in 100 Seconds', 'duration' => 172, 'id' => 'DXDe-2BC4cE'],
                    ['title' => 'Arduino in 100 Seconds', 'duration' => 142, 'id' => '1ENiVwk8idM'],
                ],
            ],
            [
                'name' => 'Tools & Technologies',
                'description' => 'Development tools and utilities',
                'contents' => [
                    ['title' => 'Vite in 100 Seconds', 'duration' => 149, 'id' => 'KCrXgy8qtjM'],
                    ['title' => 'Vim in 100 Seconds', 'duration' => 713, 'id' => '-txKSRn0qeA'],
                    ['title' => 'Neovim in 100 Seconds', 'duration' => 131, 'id' => 'c4OyfL5o7DU'],
                    ['title' => 'VS Code in 100 Seconds', 'duration' => 154, 'id' => 'KMxo3T_MTvY'],
                    ['title' => 'Bash in 100 Seconds', 'duration' => 153, 'id' => 'I4EWvMFj37g'],
                    ['title' => 'Linux in 100 Seconds', 'duration' => 162, 'id' => 'rrB13utjYV4'],
                    ['title' => 'Linux Directories Explained in 100 Seconds', 'duration' => 173, 'id' => '42iQKuQodW4'],
                    ['title' => 'Raspberry Pi Explained in 100 Seconds', 'duration' => 128, 'id' => 'eZ74x6dVYes'],
                    ['title' => 'Regular Expressions (RegEx) in 100 Seconds', 'duration' => 142, 'id' => 'sXQxhojSdZM'],
                    ['title' => 'SVG Explained in 100 Seconds', 'duration' => 140, 'id' => 'emFMHH2Bfvo'],
                    ['title' => 'CORS in 100 Seconds', 'duration' => 151, 'id' => '4KHiSt0oLJ0'],
                    ['title' => 'DNS Explained in 100 Seconds', 'duration' => 136, 'id' => 'UVR9lhUGAyU'],
                    ['title' => 'Computer Networking in 100 Seconds', 'duration' => 138, 'id' => 'keeqnciDVOo'],
                    ['title' => 'HTML in 100 Seconds', 'duration' => 154, 'id' => 'ok-plXXHlWw'],
                    ['title' => 'Web Assembly (WASM) in 100 Seconds', 'duration' => 136, 'id' => 'cbB3QEwWMlA'],
                    ['title' => 'Progressive Web Apps in 100 Seconds', 'duration' => 490, 'id' => 'sFsRylCQblw'],
                    ['title' => 'WebRTC in 100 Seconds', 'duration' => 679, 'id' => 'WmR9IMUD_CY'],
                    ['title' => 'Binary Explained in 01100100 Seconds', 'duration' => 147, 'id' => 'zDNaUi2cjv4'],
                    ['title' => 'Binary Search Algorithm in 100 Seconds', 'duration' => 140, 'id' => 'MFhxShGxHWc'],
                    ['title' => 'Graph Search Algorithms in 100 Seconds', 'duration' => 630, 'id' => 'cWNEl4HE2OE'],
                    ['title' => 'gzip file compression in 100 Seconds', 'duration' => 138, 'id' => 'NLtt4S9ErIA'],
                    ['title' => 'FFmpeg in 100 Seconds', 'duration' => 140, 'id' => '26Mayv5JPz0'],
                ],
            ],
            [
                'name' => 'AI & Data Science',
                'description' => 'Machine learning, AI, and data tools',
                'contents' => [
                    ['title' => 'Machine Learning Explained in 100 Seconds', 'duration' => 155, 'id' => 'PeMlggyqz0Y'],
                    ['title' => 'TensorFlow in 100 Seconds', 'duration' => 159, 'id' => 'i8NETqtGHms'],
                    ['title' => 'PyTorch in 100 Seconds', 'duration' => 163, 'id' => 'ORMx45xqWkA'],
                    ['title' => 'JAX in 100 Seconds', 'duration' => 204, 'id' => '_0D5lXDjNpw'],
                    ['title' => 'D3.js in 100 Seconds', 'duration' => 140, 'id' => 'bp2GF8XcJdY'],
                    ['title' => 'Nvidia CUDA in 100 Seconds', 'duration' => 193, 'id' => 'pPStdjuYzSI'],
                ],
            ],
            [
                'name' => 'Web3 & Blockchain',
                'description' => 'Blockchain and Web3 technologies',
                'contents' => [
                    ['title' => 'Bitcoin in 100 Seconds', 'duration' => 809, 'id' => 'qF7dkrce-mQ'],
                    ['title' => 'Solidity in 100 Seconds', 'duration' => 142, 'id' => 'kdvVwGrV7ec'],
                    ['title' => 'Erlang in 100 Seconds', 'duration' => 164, 'id' => 'M7uo5jmFDUw'],
                ],
            ],
            [
                'name' => 'Frameworks & CMS',
                'description' => 'Web frameworks and content management systems',
                'contents' => [
                    ['title' => 'Laravel in 100 Seconds', 'duration' => 142, 'id' => 'rIfdg_Ot-LI'],
                    ['title' => 'Ruby on Rails in 100 Seconds', 'duration' => 213, 'id' => '2DvrRadXwWY'],
                    ['title' => 'Astro in 100 Seconds', 'duration' => 137, 'id' => 'dsTXcSeAZq8'],
                    ['title' => 'Hugo in 100 Seconds', 'duration' => 153, 'id' => '0RKpf3rK57I'],
                    ['title' => 'Blazor in 100 Seconds', 'duration' => 153, 'id' => 'QXxNlpjnulI'],
                    ['title' => 'htmx in 100 seconds', 'duration' => 147, 'id' => 'r-GSGH2RxJs'],
                    ['title' => 'Redwood in 100 Seconds', 'duration' => 152, 'id' => 'o5Mwa_TJ3HM'],
                    ['title' => 'SST in 100 seconds', 'duration' => 145, 'id' => 'JY_d0vf-rfw'],
                    ['title' => 'TanStack Start in 100 Seconds', 'duration' => 139, 'id' => '1fUBWAETmkk'],
                ],
            ],
            [
                'name' => 'Systems & Low Level',
                'description' => 'Systems programming and low-level concepts',
                'contents' => [
                    ['title' => 'How a CPU Works in 100 Seconds', 'duration' => 764, 'id' => 'vqs_0W-MSB0'],
                    ['title' => 'WebGL 3D Graphics Explained in 100 Seconds', 'duration' => 127, 'id' => 'f-9LEoYYvE4'],
                    ['title' => 'LLVM in 100 Seconds', 'duration' => 156, 'id' => 'BT2Cv-Tjq7Q'],
                    ['title' => 'Zig in 100 Seconds', 'duration' => 159, 'id' => 'kxT8-C1vmd4'],
                    ['title' => 'Nim in 100 Seconds', 'duration' => 155, 'id' => 'WHyOHQ_GkNo'],
                    ['title' => 'Nix in 100 Seconds', 'duration' => 213, 'id' => 'FJVFXsNzYZQ'],
                ],
            ],
            [
                'name' => 'Security & Privacy',
                'description' => 'Security tools and privacy technologies',
                'contents' => [
                    ['title' => 'Ethical Hacking in 100 Seconds', 'duration' => 665, 'id' => 'v969_M6cWk0'],
                    ['title' => 'Firebase Security in 100 Seconds', 'duration' => 140, 'id' => 'sw1Uy3zwsLs'],
                    ['title' => 'Auth0 in 100 Seconds', 'duration' => 504, 'id' => 'yufqeJLP1rI'],
                    ['title' => 'Tails OS in 100 Seconds', 'duration' => 162, 'id' => 'mVKAyw0xqxw'],
                ],
            ],
            [
                'name' => 'More Technologies',
                'description' => 'Additional tools and platforms',
                'contents' => [
                    ['title' => 'Kafka in 100 Seconds', 'duration' => 155, 'id' => 'uvb00oaa3k8'],
                    ['title' => 'Apache Spark in 100 Seconds', 'duration' => 200, 'id' => 'IELMSD2kdmk'],
                    ['title' => 'Appwrite in 100 Seconds', 'duration' => 156, 'id' => 'L07xPMyL8sY'],
                    ['title' => '.NET in 100 Seconds', 'duration' => 164, 'id' => 'MFsYaRnrcPQ'],
                    ['title' => 'Hasura in 100 Seconds', 'duration' => 136, 'id' => 'xiZ61BkMKo8'],
                    ['title' => 'Prisma in 100 Seconds', 'duration' => 154, 'id' => 'rLRIB6AF2Dg'],
                    ['title' => 'Cypress in 100 Seconds', 'duration' => 151, 'id' => 'BQqzfHQkREo'],
                    ['title' => 'Software Testing Explained in 100 Seconds', 'duration' => 136, 'id' => 'u6QfIXgjwGQ'],
                    ['title' => 'SEO for Developers in 100 Seconds', 'duration' => 712, 'id' => '-B58GgsehKQ'],
                    ['title' => 'Get Paid with Stripe in 100 Seconds', 'duration' => 130, 'id' => '7edR32QVp_A'],
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
                $content = Content::firstOrCreate(
                    ['module_id' => $module->id, 'order' => $contentOrder],
                    [
                        'title' => $video['title'],
                        'body' => 'Learn about '.str_replace([' in 100 Seconds', ' in 01100100 Seconds', ' in 100 seconds'], '', $video['title']),
                        'type' => ContentType::Video,
                        'content_url' => 'https://youtu.be/'.$video['id'],
                        'content_meta' => [
                            'youtube_id' => $video['id'],
                            'duration' => $video['duration'],
                            'thumbnail' => 'https://i.ytimg.com/vi/'.$video['id'].'/maxresdefault.jpg',
                        ],
                    ]
                );

                $this->createVideoQuiz($content, $video['title']);
                $contentOrder++;
            }

            $this->createTopicQuiz($module, $topicData['name']);
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

    private function createTopicQuiz(Module $module, string $topicName): void
    {
        $quizContent = Content::firstOrCreate(
            [
                'module_id' => $module->id,
                'title' => $topicName.' Assessment',
            ],
            [
                'order' => ((int) $module->contents()->max('order')) + 1,
                'body' => 'Test your knowledge of '.$topicName,
                'type' => ContentType::Quiz,
                'content_url' => null,
            ]
        );

        $quiz = Quiz::firstOrCreate(
            ['content_id' => $quizContent->id],
            ['kind' => QuizKind::Content]
        );

        $questions = $this->generateTopicQuestions($topicName);
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

    private function createVideoQuiz(Content $content, string $videoTitle): void
    {
        $quiz = Quiz::firstOrCreate(
            ['content_id' => $content->id],
            ['kind' => QuizKind::Content]
        );

        $questions = $this->generateVideoQuestions($videoTitle);
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

    private function generateVideoQuestions(string $videoTitle): array
    {
        $cleanTitle = str_replace([' in 100 Seconds', ' in 01100100 Seconds', ' in 100 seconds', ' Explained in 100 Seconds', ' in NaN Seconds'], '', $videoTitle);

        return [
            ['text' => 'What is '.$cleanTitle.'?', 'options' => ['A' => 'A programming concept', 'B' => 'A framework', 'C' => 'A database', 'D' => 'An operating system'], 'correct' => ['A']],
            ['text' => 'Why is '.$cleanTitle.' important?', 'options' => ['A' => 'It improves performance', 'B' => 'It is essential for modern development', 'C' => 'It is only for beginners', 'D' => 'It is obsolete'], 'correct' => ['B']],
            ['text' => 'How is '.$cleanTitle.' used in practice?', 'options' => ['A' => 'Only in large projects', 'B' => 'In various applications and systems', 'C' => 'Only in tutorials', 'D' => 'It is not used anymore'], 'correct' => ['B']],
        ];
    }

    private function generateTopicQuestions(string $topicName): array
    {
        $topicQuestions = [
            'JavaScript & Frontend' => [
                ['text' => 'Which React hook is used for side effects?', 'options' => ['A' => 'useState', 'B' => 'useEffect', 'C' => 'useContext', 'D' => 'useReducer'], 'correct' => ['B']],
                ['text' => 'What does JSX stand for?', 'options' => ['A' => 'JavaScript XML', 'B' => 'Java Syntax Extension', 'C' => 'JavaScript Extension', 'D' => 'Java XML'], 'correct' => ['A']],
                ['text' => 'Which is a JavaScript framework?', 'options' => ['A' => 'Laravel', 'B' => 'Django', 'C' => 'Vue.js', 'D' => 'Spring'], 'correct' => ['C']],
            ],
            'CSS & Styling' => [
                ['text' => 'Which property creates a flex container?', 'options' => ['A' => 'flex-wrap', 'B' => 'display: flex', 'C' => 'justify-content', 'D' => 'align-items'], 'correct' => ['B']],
                ['text' => 'What does CSS Grid handle?', 'options' => ['A' => '1D layouts', 'B' => '2D layouts', 'C' => '3D layouts', 'D' => 'Animations'], 'correct' => ['B']],
                ['text' => 'Which is a CSS preprocessor?', 'options' => ['A' => 'Bootstrap', 'B' => 'Tailwind', 'C' => 'Sass', 'D' => 'Foundation'], 'correct' => ['C']],
            ],
            'Backend & APIs' => [
                ['text' => 'What does REST stand for?', 'options' => ['A' => 'Remote Enterprise State Transfer', 'B' => 'Representational State Transfer', 'C' => 'Reliable State Transfer', 'D' => 'Resource State Transfer'], 'correct' => ['B']],
                ['text' => 'Which is a Node.js framework?', 'options' => ['A' => 'Laravel', 'B' => 'Django', 'C' => 'Express', 'D' => 'Flask'], 'correct' => ['C']],
                ['text' => 'What is GraphQL?', 'options' => ['A' => 'A database', 'B' => 'A query language for APIs', 'C' => 'A templating engine', 'D' => 'A testing framework'], 'correct' => ['B']],
            ],
            'Databases' => [
                ['text' => 'Which is a NoSQL database?', 'options' => ['A' => 'PostgreSQL', 'B' => 'MySQL', 'C' => 'MongoDB', 'D' => 'SQLite'], 'correct' => ['C']],
                ['text' => 'What does SQL stand for?', 'options' => ['A' => 'Simple Query Language', 'B' => 'Structured Query Language', 'C' => 'Standard Query Logic', 'D' => 'System Query Language'], 'correct' => ['B']],
                ['text' => 'Which is a key-value store?', 'options' => ['A' => 'Neo4j', 'B' => 'Redis', 'C' => 'Cassandra', 'D' => 'CouchDB'], 'correct' => ['B']],
            ],
            'DevOps & Cloud' => [
                ['text' => 'What is Docker?', 'options' => ['A' => 'A programming language', 'B' => 'A containerization platform', 'C' => 'A version control system', 'D' => 'A cloud provider'], 'correct' => ['B']],
                ['text' => 'What is Kubernetes used for?', 'options' => ['A' => 'Container orchestration', 'B' => 'Code compilation', 'C' => 'Database management', 'D' => 'Web hosting'], 'correct' => ['A']],
                ['text' => 'What does CI/CD stand for?', 'options' => ['A' => 'Continuous Integration/Continuous Deployment', 'B' => 'Code Integration/Code Delivery', 'C' => 'Central Integration/Central Deployment', 'D' => 'Complete Integration/Complete Deployment'], 'correct' => ['A']],
            ],
            'Programming Languages' => [
                ['text' => 'Which language is known for memory safety?', 'options' => ['A' => 'C', 'B' => 'C++', 'C' => 'Rust', 'D' => 'Assembly'], 'correct' => ['C']],
                ['text' => 'What is Python primarily used for?', 'options' => ['A' => 'System programming', 'B' => 'Data science and web development', 'C' => 'Mobile development', 'D' => 'Game development'], 'correct' => ['B']],
                ['text' => 'Which language runs on the JVM?', 'options' => ['A' => 'Ruby', 'B' => 'Python', 'C' => 'Kotlin', 'D' => 'Go'], 'correct' => ['C']],
            ],
            'Mobile & Desktop' => [
                ['text' => 'What is Flutter?', 'options' => ['A' => 'A database', 'B' => 'A UI toolkit for cross-platform apps', 'C' => 'A web framework', 'D' => 'An operating system'], 'correct' => ['B']],
                ['text' => 'Which is used for desktop apps with web tech?', 'options' => ['A' => 'React Native', 'B' => 'Electron', 'C' => 'Flutter', 'D' => 'Swift'], 'correct' => ['B']],
                ['text' => 'What game engine uses C#?', 'options' => ['A' => 'Godot', 'B' => 'Unity', 'C' => 'Unreal', 'D' => 'Blender'], 'correct' => ['B']],
            ],
            'Tools & Technologies' => [
                ['text' => 'What is Vim?', 'options' => ['A' => 'A programming language', 'B' => 'A text editor', 'C' => 'A version control system', 'D' => 'A database'], 'correct' => ['B']],
                ['text' => 'What does Git do?', 'options' => ['A' => 'Database management', 'B' => 'Version control', 'C' => 'Web hosting', 'D' => 'Code compilation'], 'correct' => ['B']],
                ['text' => 'What is VS Code?', 'options' => ['A' => 'A programming language', 'B' => 'A code editor', 'C' => 'A version control system', 'D' => 'A database'], 'correct' => ['B']],
            ],
            'AI & Data Science' => [
                ['text' => 'What is TensorFlow?', 'options' => ['A' => 'A database', 'B' => 'A machine learning framework', 'C' => 'A web framework', 'D' => 'An operating system'], 'correct' => ['B']],
                ['text' => 'What is PyTorch?', 'options' => ['A' => 'A deep learning framework', 'B' => 'A database', 'C' => 'A web server', 'D' => 'A container tool'], 'correct' => ['A']],
                ['text' => 'What does ML stand for?', 'options' => ['A' => 'Machine Logic', 'B' => 'Machine Learning', 'C' => 'Modern Language', 'D' => 'Meta Language'], 'correct' => ['B']],
            ],
            'Web3 & Blockchain' => [
                ['text' => 'What is Bitcoin?', 'options' => ['A' => 'A programming language', 'B' => 'A cryptocurrency', 'C' => 'A web framework', 'D' => 'A database'], 'correct' => ['B']],
                ['text' => 'What is Solidity?', 'options' => ['A' => 'A smart contract language', 'B' => 'A database', 'C' => 'A web framework', 'D' => 'An operating system'], 'correct' => ['A']],
                ['text' => 'What is a blockchain?', 'options' => ['A' => 'A type of database', 'B' => 'A distributed ledger', 'C' => 'A programming language', 'D' => 'A web server'], 'correct' => ['B']],
            ],
            'Frameworks & CMS' => [
                ['text' => 'What is Laravel?', 'options' => ['A' => 'A JavaScript framework', 'B' => 'A PHP framework', 'C' => 'A Python framework', 'D' => 'A Ruby framework'], 'correct' => ['B']],
                ['text' => 'What is Astro?', 'options' => ['A' => 'A static site builder', 'B' => 'A database', 'C' => 'A mobile framework', 'D' => 'A programming language'], 'correct' => ['A']],
                ['text' => 'What is htmx?', 'options' => ['A' => 'A JavaScript library for HTML extensions', 'B' => 'A database', 'C' => 'A CSS framework', 'D' => 'A backend framework'], 'correct' => ['A']],
            ],
            'Systems & Low Level' => [
                ['text' => 'What does CPU stand for?', 'options' => ['A' => 'Central Processing Unit', 'B' => 'Computer Personal Unit', 'C' => 'Central Program Utility', 'D' => 'Computer Processing Unit'], 'correct' => ['A']],
                ['text' => 'What is LLVM?', 'options' => ['A' => 'A compiler infrastructure', 'B' => 'A database', 'C' => 'A web framework', 'D' => 'An operating system'], 'correct' => ['A']],
                ['text' => 'What is Zig?', 'options' => ['A' => 'A programming language', 'B' => 'A database', 'C' => 'A web framework', 'D' => 'A version control system'], 'correct' => ['A']],
            ],
            'Security & Privacy' => [
                ['text' => 'What is ethical hacking?', 'options' => ['A' => 'Illegally accessing systems', 'B' => 'Authorized security testing', 'C' => 'Creating viruses', 'D' => 'Stealing data'], 'correct' => ['B']],
                ['text' => 'What is OAuth?', 'options' => ['A' => 'A programming language', 'B' => 'An authorization framework', 'C' => 'A database', 'D' => 'A web server'], 'correct' => ['B']],
                ['text' => 'What does HTTPS use?', 'options' => ['A' => 'SSL/TLS', 'B' => 'HTTP', 'C' => 'FTP', 'D' => 'SMTP'], 'correct' => ['A']],
            ],
            'More Technologies' => [
                ['text' => 'What is Apache Kafka?', 'options' => ['A' => 'A web framework', 'B' => 'An event streaming platform', 'C' => 'A database', 'D' => 'A programming language'], 'correct' => ['B']],
                ['text' => 'What is Prisma?', 'options' => ['A' => 'An ORM', 'B' => 'A database', 'C' => 'A web server', 'D' => 'A programming language'], 'correct' => ['A']],
                ['text' => 'What is Cypress?', 'options' => ['A' => 'A testing framework', 'B' => 'A database', 'C' => 'A web server', 'D' => 'A programming language'], 'correct' => ['A']],
            ],
        ];

        return $topicQuestions[$topicName] ?? [
            ['text' => 'What is '.$topicName.'?', 'options' => ['A' => 'A technology', 'B' => 'A framework', 'C' => 'A tool', 'D' => 'All of the above'], 'correct' => ['D']],
        ];
    }
}
