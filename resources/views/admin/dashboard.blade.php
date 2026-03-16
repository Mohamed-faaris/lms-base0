@extends('layouts.admin')

@section('page-title', 'Dashboard')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<!-- College Overview Cards -->
<div style="margin-bottom: 32px;">
    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 16px;">Colleges Overview</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        @forelse($colleges as $index => $college)
        <div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h4 style="font-size: 18px; font-weight: 600; color: #1f2937;">{{ $college->college_name }}</h4>
                <span style="background: #dbeafe; color: #2563eb; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">Active</span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <p style="font-size: 12px; color: #6b7280;">Departments</p>
                    <p style="font-size: 24px; font-weight: 600; color: #111827;">{{ $college->total_departments }}</p>
                </div>
                <div>
                    <p style="font-size: 12px; color: #6b7280;">Faculties</p>
                    <p style="font-size: 24px; font-weight: 600; color: #111827;">{{ $college->total_faculties }}</p>
                </div>
                <div>
                    <p style="font-size: 12px; color: #6b7280;">Users</p>
                    <p style="font-size: 24px; font-weight: 600; color: #111827;">{{ $college->total_users }}</p>
                </div>
            </div>
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 12px; color: #6b7280;">Learning Progress</span>
                    <span style="font-size: 12px; color: #059669; font-weight: 500;">{{ $college->progress }}%</span>
                </div>
                <div style="width: 100%; height: 8px; background: #e5e7eb; border-radius: 4px;">
                    <div style="width: {{ $college->progress }}%; height: 100%; background: linear-gradient(90deg, #3b82f6, #059669); border-radius: 4px;"></div>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280;">
            No colleges found. Please set up colleges data.
        </div>
        @endforelse
    </div>
</div>

<!-- Recent Activities Section -->
<div style="margin-bottom: 32px;">
    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 16px;">Recent Activities</h3>
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        @forelse($recentActivities as $activity)
        <div style="padding: 16px 24px; display: flex; align-items: center; border-bottom: 1px solid #e5e7eb;">
            @if($activity->icon === 'course')
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <svg style="width: 20px; height: 20px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            @elseif($activity->icon === 'module')
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #d1fae5; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <svg style="width: 20px; height: 20px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            @elseif($activity->icon === 'quiz')
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <svg style="width: 20px; height: 20px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            @elseif($activity->icon === 'video')
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #fce7f3; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <svg style="width: 20px; height: 20px; color: #db2777;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            @else
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <svg style="width: 20px; height: 20px; color: #4f46e5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            @endif
            <div style="flex: 1;">
                <p style="font-size: 14px; color: #111827;">{{ $activity->title }}</p>
                <p style="font-size: 12px; color: #6b7280;">{{ $activity->description }} • {{ $activity->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div style="padding: 40px; text-align: center; color: #6b7280;">
            No recent activities found.
        </div>
        @endforelse
    </div>
</div>

<!-- Learning Progress Charts -->
<div>
    <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 16px;">Learning Progress Charts</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">

        <!-- College Progress Chart -->
        <div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px;">College Progress</h4>
            <canvas id="collegeProgressChart" height="200"></canvas>
        </div>

        <!-- Course Completion Chart -->
        <div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px;">Course Completion</h4>
            <canvas id="courseCompletionChart" height="200"></canvas>
        </div>

        <!-- User Activity Chart -->
        <div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h4 style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px;">User Activity by College</h4>
            <canvas id="userActivityChart" height="200"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // College Progress Chart (Bar)
    const collegeProgressCtx = document.getElementById('collegeProgressChart').getContext('2d');
    new Chart(collegeProgressCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['college_progress']->pluck('name')) !!},
            datasets: [{
                label: 'Progress %',
                data: {!! json_encode($chartData['college_progress']->pluck('progress')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)'
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Course Completion Chart (Doughnut)
    const courseCompletionCtx = document.getElementById('courseCompletionChart').getContext('2d');
    new Chart(courseCompletionCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['course_completion']->pluck('name')->take(5)) !!},
            datasets: [{
                data: {!! json_encode($chartData['course_completion']->pluck('completion')->take(5)) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(139, 92, 246, 0.7)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // User Activity Chart (Pie)
    const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
    new Chart(userActivityCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($chartData['user_activity']->pluck('college')) !!},
            datasets: [{
                label: 'Active Users',
                data: {!! json_encode($chartData['user_activity']->pluck('count')) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
@endsection
