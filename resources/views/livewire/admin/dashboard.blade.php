<div class="p-8 bg-gray-100 min-h-screen">

    <!-- Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 text-sm">Overview of your learning management system</p>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-3 gap-6 mb-8">

        <!-- Total Colleges -->
        <div class="bg-white rounded-2xl shadow-md p-6 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Colleges</p>
                <h2 class="text-3xl font-bold mt-2">{{ $totalColleges }}</h2>
            </div>
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">🎓</div>
        </div>

        <!-- Total Departments -->
        <div class="bg-white rounded-2xl shadow-md p-6 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Departments</p>
                <h2 class="text-3xl font-bold mt-2">{{ $totalDepartments }}</h2>
            </div>
            <div class="bg-green-100 text-green-600 p-3 rounded-full">🏫</div>
        </div>

        <!-- Example: Total Faculties (static for now) -->
        <div class="bg-white rounded-2xl shadow-md p-6 flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Total Faculties</p>
                <h2 class="text-3xl font-bold mt-2">1,240</h2>
            </div>
            <div class="bg-orange-100 text-orange-600 p-3 rounded-full">👨‍🏫</div>
        </div>

    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-3 gap-6">

        <!-- College Chart -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h2 class="font-semibold mb-4">College-wise Progress</h2>
            <canvas id="collegeChart"></canvas>
        </div>

        <!-- Department Chart -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h2 class="font-semibold mb-4">Department Performance</h2>
            <canvas id="departmentChart"></canvas>
        </div>

        <!-- Quiz Chart (example static data) -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h2 class="font-semibold mb-4">Quiz Score Distribution</h2>
            <canvas id="quizChart"></canvas>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {


    // Quiz Chart (static example)
    new Chart(document.getElementById('quizChart'), {
        type: 'doughnut',
        data: {
            labels: ['90-100','80-90','70-80','60-70','Below 60'],
            datasets: [{
                data: [20,30,25,15,10],
                backgroundColor: [
                    '#2563EB','#10B981','#F59E0B','#EF4444','#6B7280'
                ]
            }]
        }
    });

});
</script>