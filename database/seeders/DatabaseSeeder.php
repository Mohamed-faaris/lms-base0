<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public const SEEDED_ENROLLMENT_DEADLINE = 1798761600;

    public function run(): void
    {
        $this->call([
            KRCTFacultySeeder::class,
            LmsDataSeeder::class,
            JavaCourseSeeder::class,
        ]);
    }
}
