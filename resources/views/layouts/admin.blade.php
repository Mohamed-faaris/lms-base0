<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-blue-950 text-white flex flex-col">

        <!-- Logo -->
        <div class="p-6 border-b border-blue-800">
            <h2 class="text-xl font-bold">EduAdmin</h2>
            <p class="text-xs text-blue-300">LMS Dashboard</p>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-4 space-y-2">

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-800">
                📊 Dashboard
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                🎓 Colleges
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                🏫 Departments
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                👨‍🏫 Faculties
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                📚 Courses
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                📝 Quizzes
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                📈 Reports
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800 transition">
                ⚙ Settings
            </a>

        </nav>

        <div class="p-4 text-xs text-blue-300">
            © 2026 EduAdmin
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col">

        <!-- TOP NAVBAR -->
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">

            <!-- Search -->
            <input 
                type="text" 
                placeholder="Search colleges, courses, faculties..." 
                class="w-96 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200"
            >

            <!-- Profile -->
            <div class="flex items-center gap-3">
                🔔
                <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center">
                    AD
                </div>
                <div>
                    <p class="text-sm font-semibold">Admin User</p>
                    <p class="text-xs text-gray-500">Super Admin</p>
                </div>
            </div>

        </header>

        <!-- PAGE CONTENT -->
        <main class="p-8">
            {{ $slot }}
        </main>

    </div>

</div>

</body>
</html>