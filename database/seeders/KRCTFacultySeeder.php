<?php

namespace Database\Seeders;

use App\Enums\College;
use App\Enums\Department;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KRCTFacultySeeder extends Seeder
{
    public function run(): void
    {
        $college = College::KRCT->value;

        // ============================================
        // EEE DEPARTMENT FACULTY
        // ============================================
        $eeeFaculty = [
            ['name' => 'Mr.A.T.Sankara Subramanian', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Dr.J.Mahil', 'designation' => 'Professor', 'qualification' => 'Ph.D'],
            ['name' => 'Dr.R.Madavan', 'designation' => 'Professor', 'qualification' => 'Ph.D'],
            ['name' => 'Dr.S.Jeyasudha', 'designation' => 'Professor', 'qualification' => 'Ph.D'],
            ['name' => 'Mr.T.RamKumar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.A.Anton Amala Praveen', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.M.D.Udayakumar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mrs.S.Vijaya Lakshmi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.P.Sabarish', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.R.Jaiganesh', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.V.Suresh Kumar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.P.Sekar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.P.Prakash', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mrs.K.Bhagyalakshmi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.Arya Rajan', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.Aliyar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.P.Govindaraj', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
        ];

        foreach ($eeeFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'eeefaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::EEE->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // ECE DEPARTMENT FACULTY
        // ============================================
        $eceFaculty = [
            ['name' => 'Dr.VASUDEVAN N', 'designation' => 'Professor & Principal', 'qualification' => 'M.Tech.,Ph.D'],
            ['name' => 'Dr.SUGANTHI S', 'designation' => 'Professor & Centre Head', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.KAVITHA A', 'designation' => 'Professor & Head', 'qualification' => 'M.Tech.,Ph.D'],
            ['name' => 'Dr.KAVITHA M', 'designation' => 'Professor & Library Incharge', 'qualification' => 'M.Tech.,Ph.D'],
            ['name' => 'Dr.PUNITHA A', 'designation' => 'Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.RAJINIKANTH C', 'designation' => 'Associate Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.SYEDAKBAR S', 'designation' => 'Assistant Professor & Head of Department', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.ASHOKRAJ M', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.MONISHA S', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Mr.MANJUNATHAN A', 'designation' => 'Assistant Professor & Assistant Head of Department', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.NITHYA S', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.DEEPA J', 'designation' => 'Assistant Professor', 'qualification' => 'M.Tech.,(PhD)'],
            ['name' => 'Ms.GEERTHANA S', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.GAYATHRI S', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mr.MANI P', 'designation' => 'Assistant Professor & Assistant Head of Department', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.KARPOORA SUNDARI K', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.INDUMATHI R', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.BHAVANI R', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mr.PARTHIBARAJ A', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mr.RAJA M', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.SANGEETHA K', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.REVATHI G', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Ms.NISHANTHI G', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.SUDHA P', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.KEERTHANA G', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.GOWRI P', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.JAYA RATNAM P', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
        ];

        foreach ($eceFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'ecefaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::ECE->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // CIVIL DEPARTMENT FACULTY
        // ============================================
        $civilFaculty = [
            ['name' => 'Dr. S. SUJATHA', 'designation' => 'Professor', 'qualification' => 'Ph.D'],
            ['name' => 'Mr.A. OORKALAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.A. KARTHICK', 'designation' => 'Assistant Professor', 'qualification' => 'M.Tech'],
            ['name' => 'Mr.S. VIGNESH KANNAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mrs.T.SOUNDHARYA', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mr.M.MUTHU KUMARAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Mrs.R.ESWARI', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Dr.G.SREELAL', 'designation' => 'Assistant Professor', 'qualification' => 'Ph.D'],
            ['name' => 'Mr.B.DHILIPKUMAR', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Dr.S.HEMAVATHI', 'designation' => 'Assistant Professor', 'qualification' => 'Ph.D'],
            ['name' => 'Mr.E.ARUN REVANTH', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Dr.G.BALAJI PONRAJ', 'designation' => 'Assistant Professor', 'qualification' => 'Ph.D'],
            ['name' => 'M.MANONMANI', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
        ];

        foreach ($civilFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'civilfaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::CIVIL->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // CSE DEPARTMENT FACULTY
        // ============================================
        $cseFaculty = [
            ['name' => 'Dr.A.Delphin Carolina Rani', 'designation' => 'Professor and Head', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.G.Thiruchelvi', 'designation' => 'Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Dr.C.Shyamala', 'designation' => 'Associate Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'P.Matheswaran', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'R.Rajavarman', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.,(Ph.D)'],
            ['name' => 'G.Rajendra Kannammal', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'V.Kalpana', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'M.Mathumathi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'M.Aarthi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'I.Monica Tresa', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'M.Pavithra', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'M.Haritha', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'S.Rahmath Nisha', 'designation' => 'Assistant Professor', 'qualification' => 'M.Tech., (Ph.D)'],
            ['name' => 'A.Malarmannan', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'K.Shanmuga Priya', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'M.Asma Begum', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'S.Uma Mageshwari', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.A.Dhivya Bharathi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.K.Vallipriadharshini', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.M.Nathiya', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Ms.B.Pushpalatha', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.P.Silambarasi', 'designation' => 'Assistant Professor', 'qualification' => 'M.Tech.'],
            ['name' => 'Ms.V.Sowmiya', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.S.Gayathiri', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Ms.P.Karthika', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mr.S.Senthil', 'designation' => 'Assistant Professor', 'qualification' => 'M.E., (Ph.D)'],
            ['name' => 'Mrs.R.Sathya', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.,(Ph.D)'],
            ['name' => 'Dr.K.Deepa', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.,Ph.D'],
            ['name' => 'Mrs.A.Thenmozhi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
        ];

        foreach ($cseFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'csefaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::CSE->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // MECH DEPARTMENT FACULTY
        // ============================================
        $mechFaculty = [
            ['name' => 'Dr.T.RAJ KUMAR', 'designation' => 'Assistant Professor and Head', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Dr.B.SURESH KUMAR', 'designation' => 'Associate Professor', 'qualification' => 'ME/M. Tech and PhD'],
            ['name' => 'Dr.V.VIJAYAN', 'designation' => 'Professor', 'qualification' => 'ME/M. Tech and PhD'],
            ['name' => 'Dr.S.SARAVANAN', 'designation' => 'Associate Professor', 'qualification' => 'ME/M. Tech and PhD'],
            ['name' => 'Dr.R.YOKESWARAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Dr.M.CHANDRASEKAR', 'designation' => 'Professor', 'qualification' => 'M.E/M.Tech and PhD'],
            ['name' => 'Mr.K.B.HARIHARAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Mr.K.RAJAGURU', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Dr.G.SATHISH KUMAR', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Mr.N.PARKUNAM', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Mr.R.RAM KUMAR', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Mr.S.DINESH', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Mr.A.GODWIN ANTONY', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Mr.G.ARUN KUMAR', 'designation' => 'Assistant Professor', 'qualification' => 'M.E/M.Tech'],
            ['name' => 'Dr.T.S.Senthil Kumar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Dr.V.VIGNESH KUMAR', 'designation' => 'Associate Professor', 'qualification' => 'M.E, Ph.D'],
        ];

        foreach ($mechFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'mechfaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::MECH->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // IT DEPARTMENT FACULTY
        // ============================================
        $itFaculty = [
            ['name' => 'Dr.C.Shyamala', 'designation' => 'Associate Professor and Head', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'Mrs.R.Hema', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
            ['name' => 'Mrs.S.TamilThendral', 'designation' => 'Assistant Professor', 'qualification' => 'M.E.'],
        ];

        foreach ($itFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'itfaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::IT->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // AI DEPARTMENT FACULTY
        // ============================================
        $aiFaculty = [
            ['name' => 'Dr.T.Avudaiappan', 'designation' => 'Associate Professor', 'qualification' => 'M.E., Ph.D'],
            ['name' => 'S.Murugavalli', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'M A Reetha Jeyarani', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'D.Deena Rose', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'P.B.Aravind Prasad', 'designation' => 'Assistant Professor', 'qualification' => 'M. Tech'],
            ['name' => 'P.Jasmine Jose', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Muthukumaran C', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'T.Praveen Kumar', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'A.Sumathi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'R.Roshan Joshua', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'A Joshua Issac', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'S.Geetha', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'R.Tharchius', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'M.Arunprasath', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'Joany Franklin', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'G.Nalina Keerthana', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'SHYAMSUNDAR T', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'M.Bharathi', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'S.Prabhasri', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'S.Chris Lavanya', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
            ['name' => 'E.Sri Santhoshini', 'designation' => 'Assistant Professor', 'qualification' => 'M.E'],
        ];

        foreach ($aiFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'aifaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::AI->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // SCIENCE & HUMANITIES DEPARTMENT FACULTY
        // ============================================
        $shFaculty = [
            ['name' => 'Mr. S. SARAVANA KUMAR', 'designation' => 'Assistant Professor & Head', 'qualification' => 'M.Sc., M.Phil., B.Ed., (Ph.D)'],
            ['name' => 'Dr.D.DEVAHI', 'designation' => 'Assistant Professor & A.HOD', 'qualification' => 'MA., M.Phil., B.Ed.Ph.D'],
            ['name' => 'Mr. M. PRABHAKARAN', 'designation' => 'Assistant Professor & A.HOD', 'qualification' => 'M.A., M.Phil., B.ED., PGDJMC., (Ph.D)'],
            ['name' => 'Mr. L. ARULDOSS', 'designation' => 'Assistant Professor', 'qualification' => 'BTh., B.A., M.A., M.Phil., B.Ed'],
            ['name' => 'Mr. A. VINOTH', 'designation' => 'Assistant Professor', 'qualification' => 'MA., M.Phil., B.Ed., SET'],
            ['name' => 'Mr.R.JAYAKUMARAN', 'designation' => 'Assistant Professor', 'qualification' => 'MA., M.Phil., B.Ed.'],
            ['name' => 'Dr. C. PRIYA', 'designation' => 'Assistant Professor', 'qualification' => 'M.A., M.Phil., Ph.D'],
            ['name' => 'Dr.A.RAJASEKARAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.A., M.Phil.,B.Ed., Ph.D'],
            ['name' => 'Mrs. B. BHUVANESWARI', 'designation' => 'Assistant Professor & A.HOD', 'qualification' => 'M.Sc., M.Phil., B.Ed., (Ph.D)'],
            ['name' => 'Mr. R. DHANDAPANI', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., B.Ed., (Ph.D)'],
            ['name' => 'Dr. M. ARULMANI', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., Ph.D'],
            ['name' => 'Dr. M. MANIKANDAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., Ph.D.'],
            ['name' => 'Dr.V.C. BHARATH SABARISH', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., Ph.D.'],
            ['name' => 'Dr. M. GOVINDARAJ', 'designation' => 'Associate Professor & A.HOD', 'qualification' => 'M.Sc., M.Phil., B.Ed., Ph.D'],
            ['name' => 'Mr. C. KALAIVANAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., B.Ed., (Ph.D)'],
            ['name' => 'Dr. M. DEEPAN KUMAR', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., Ph.D.'],
            ['name' => 'Dr. M. KANDASAMY', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc.,M.Phil., Ph.D.'],
            ['name' => 'Dr.E.DHARMARAJ', 'designation' => 'Assistant Professor', 'qualification' => 'M.sc,M.Phil,B.Ed,Ph.D'],
            ['name' => 'Dr.M.VELMURUGAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., B.Ed., Ph.D.,PDF'],
            ['name' => 'Dr. S. SARANYASRI', 'designation' => 'Assistant Professor & A.HOD', 'qualification' => 'M.Sc., M.Phil., Ph.D'],
            ['name' => 'Dr. S. VENKATESAN', 'designation' => 'Professor', 'qualification' => 'M.Sc.,M.Phil., Ph.D'],
            ['name' => 'Mrs. B. MAHALAKSHMI', 'designation' => 'Assistant Professor & AHOD', 'qualification' => 'M.Sc., M.Phil., B.Ed.'],
            ['name' => 'Mrs. B. RAJALAKSHMI', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil'],
            ['name' => 'Mr. K. SARAVANA KUMAR', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., B.Ed., (Ph.D)'],
            ['name' => 'Mr. N. PARTHIBAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., B.Ed.'],
            ['name' => 'Dr. M. MARIA AROCKIA RAJ', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., Ph.D'],
            ['name' => 'Mr. T. PITCHAIMANI', 'designation' => 'Assistant Professor', 'qualification' => 'M.SC., B.Ed., M.Phil'],
            ['name' => 'Ms. V. BAGYALAKSHMI', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., B.Ed., SET'],
            ['name' => 'Dr.J.SEBASTIAN AROCKIA JENIFER', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., Ph.D'],
            ['name' => 'Dr.D.SURJITH JIJI', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., Ph.D'],
            ['name' => 'S.INFANCY VIMAL PRIYA', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., Ph.D'],
            ['name' => 'Mr.S.MANIKANDAN', 'designation' => 'Assistant Professor', 'qualification' => 'M.Sc., M.Phil., PGCD.(SET)'],
            ['name' => 'Mrs.V. BHAMARUKMANI', 'designation' => 'Tutor', 'qualification' => 'M.Sc.'],
            ['name' => 'Ms. J. GAYATHRI', 'designation' => 'Tutor', 'qualification' => 'M.Sc., M.Phil'],
        ];

        foreach ($shFaculty as $index => $faculty) {
            User::create([
                'name' => $faculty['name'],
                'email' => 'shfaculty'.str_pad($index + 1, 2, '0', STR_PAD_LEFT).'@krct.ac.in',
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => Department::SH->value,
                'role' => Role::Faculty->value,  // Using Faculty enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // ADMIN USERS
        // ============================================
        $admins = [
            ['name' => 'Super Admin User', 'email' => 'superadmin@krct.ac.in', 'role' => Role::SuperAdmin],
            ['name' => 'Admin User', 'email' => 'admin@krct.ac.in', 'role' => Role::Admin],
            ['name' => 'Principal', 'email' => 'principal@krct.ac.in', 'role' => Role::Admin],
        ];

        foreach ($admins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => null,
                'role' => $admin['role']->value,
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // HOD USERS (Department Heads)
        // ============================================
        $hods = [
            ['name' => 'Dr.A.Delphin Carolina Rani', 'department' => Department::CSE->value, 'email' => 'hodcse@krct.ac.in'],
            ['name' => 'Dr.SYEDAKBAR S', 'department' => Department::ECE->value, 'email' => 'hodece@krct.ac.in'],
            ['name' => 'Dr.J.Mahil', 'department' => Department::EEE->value, 'email' => 'hodeee@krct.ac.in'],
            ['name' => 'Dr.T.RAJ KUMAR', 'department' => Department::MECH->value, 'email' => 'hodmech@krct.ac.in'],
            ['name' => 'Dr. S. SUJATHA', 'department' => Department::CIVIL->value, 'email' => 'hodcivil@krct.ac.in'],
            ['name' => 'Dr.C.Shyamala', 'department' => Department::IT->value, 'email' => 'hodit@krct.ac.in'],
            ['name' => 'Dr.T.Avudaiappan', 'department' => Department::AI->value, 'email' => 'hodai@krct.ac.in'],
            ['name' => 'Mr. S. SARAVANA KUMAR', 'department' => Department::SH->value, 'email' => 'hodsh@krct.ac.in'],
        ];

        foreach ($hods as $hod) {
            User::create([
                'name' => $hod['name'],
                'email' => $hod['email'],
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => $hod['department'],
                'role' => Role::Manager->value,  // Using Manager enum for HODs
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ============================================
        // STAFF USERS (Lab Assistants, Office Staff, etc.)
        // ============================================
        $staff = [
            ['name' => 'Lab Assistant CSE', 'email' => 'labcse@krct.ac.in', 'department' => Department::CSE->value],
            ['name' => 'Lab Assistant ECE', 'email' => 'labece@krct.ac.in', 'department' => Department::ECE->value],
            ['name' => 'Lab Assistant EEE', 'email' => 'labeee@krct.ac.in', 'department' => Department::EEE->value],
            ['name' => 'Lab Assistant MECH', 'email' => 'labmech@krct.ac.in', 'department' => Department::MECH->value],
            ['name' => 'Office Staff', 'email' => 'office@krct.ac.in', 'department' => null],
        ];

        foreach ($staff as $staffMember) {
            User::create([
                'name' => $staffMember['name'],
                'email' => $staffMember['email'],
                'password' => Hash::make('password'),
                'college' => $college,
                'department' => $staffMember['department'],
                'role' => Role::Staff->value,  // Using Staff enum
                'image' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ KRCT College Faculty Seeder Completed Successfully!');
        $this->command->info('========================================');
        $this->command->info('📊 DEPARTMENT WISE FACULTY COUNT:');
        $this->command->info('   EEE: '.count($eeeFaculty).' faculty');
        $this->command->info('   ECE: '.count($eceFaculty).' faculty');
        $this->command->info('   CIVIL: '.count($civilFaculty).' faculty');
        $this->command->info('   CSE: '.count($cseFaculty).' faculty');
        $this->command->info('   MECH: '.count($mechFaculty).' faculty');
        $this->command->info('   IT: '.count($itFaculty).' faculty');
        $this->command->info('   AI: '.count($aiFaculty).' faculty');
        $this->command->info('   S&H: '.count($shFaculty).' faculty');
        $this->command->info('----------------------------------------');
        $this->command->info('   TOTAL FACULTY: '.(count($eeeFaculty) + count($eceFaculty) + count($civilFaculty) + count($cseFaculty) + count($mechFaculty) + count($itFaculty) + count($aiFaculty) + count($shFaculty)).' faculty');
        $this->command->info('   ADMIN/SUPERADMIN: 3 users');
        $this->command->info('   HOD/MANAGER: 8 users');
        $this->command->info('   STAFF: 5 users');
        $this->command->info('========================================');
        $this->command->info('🔑 DEFAULT PASSWORD: password');
        $this->command->info('========================================');
    }
}
