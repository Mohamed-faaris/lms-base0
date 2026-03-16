<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedColleges();
        $this->seedUsers();
    }

    private function seedColleges(): void
    {
        $colleges = [
            [
                'college_name' => 'Kongu Ramanathan Engineering College',
                'college_code' => 'krce',
                'address' => 'Thudupathi, Erode - 638 057, Tamil Nadu',
                'status' => 'active',
            ],
            [
                'college_name' => 'Kongu Research and Development Centre',
                'college_code' => 'krct',
                'address' => 'Thudupathi, Erode - 638 057, Tamil Nadu',
                'status' => 'active',
            ],
            [
                'college_name' => 'Mahatma Gandhi College of Engineering',
                'college_code' => 'mgce',
                'address' => 'Nilakottai, Dindigul - 624 219, Tamil Nadu',
                'status' => 'active',
            ],
        ];

        foreach ($colleges as $collegeData) {
            $college = College::create($collegeData);

            $departments = [
                ['department_name' => 'Artificial Intelligence & Data Science', 'department_code' => 'AI-DS', 'hod_name' => 'Dr. R. Mahesh'],
                ['department_name' => 'Computer Science & Engineering', 'department_code' => 'CSE', 'hod_name' => 'Dr. S. Kumar'],
                ['department_name' => 'Information Technology', 'department_code' => 'IT', 'hod_name' => 'Dr. P. Venkatesh'],
                ['department_name' => 'Electronics & Communication Engineering', 'department_code' => 'ECE', 'hod_name' => 'Dr. M. Rajendran'],
                ['department_name' => 'Mechanical Engineering', 'department_code' => 'MECH', 'hod_name' => 'Dr. A. Murugesan'],
            ];

            foreach ($departments as $deptData) {
                $department = Department::create([
                    'college_id' => $college->id,
                    'department_name' => $deptData['department_name'],
                    'department_code' => $deptData['department_code'],
                    'hod_name' => $deptData['hod_name'],
                    'status' => 'active',
                ]);

                $faculties = [
                    ['faculty_name' => 'Prof. '.$college->college_code.' HOD', 'faculty_email' => strtolower($deptData['department_code']).'_hod@'.$college->college_code.'.edu', 'designation' => 'Professor & HOD', 'status' => 'active'],
                    ['faculty_name' => 'Prof. '.$college->college_code.' AP1', 'faculty_email' => strtolower($deptData['department_code']).'_ap1@'.$college->college_code.'.edu', 'designation' => 'Assistant Professor', 'status' => 'active'],
                    ['faculty_name' => 'Prof. '.$college->college_code.' AP2', 'faculty_email' => strtolower($deptData['department_code']).'_ap2@'.$college->college_code.'.edu', 'designation' => 'Assistant Professor', 'status' => 'active'],
                ];

                foreach ($faculties as $facultyData) {
                    Faculty::create([
                        'college_id' => $college->id,
                        'department_id' => $department->id,
                        'faculty_name' => $facultyData['faculty_name'],
                        'faculty_email' => $facultyData['faculty_email'],
                        'designation' => $facultyData['designation'],
                        'status' => $facultyData['status'],
                    ]);
                }
            }
        }
    }

    private function seedUsers(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(5)->create();
    }
}
