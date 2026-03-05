<?php

namespace App\Livewire\Faculty;

use App\Models\Streak;
use App\Models\Xp;
use Carbon\Carbon;
use Livewire\Component;

class Streaks extends Component
{
    public int $streak = 0;
    public int $weeklyStreak = 0;
    public array $weekData = [];
    public int $xpThisWeek = 0;
    public array $streakRewards = [];

    public function mount()
    {
        $this->loadData();
    }

    protected function loadData()
    {
        $user = auth()->user();
        if (!$user) return;

        // Get Streak
        $streakRecord = Streak::where('user_id', $user->id)->orderBy('date', 'desc')->first();
        $this->streak = $streakRecord?->count ?? 0;

        // Calculate weekly streak
        $weekStart = now()->startOfWeek();
        $weeklyStreaks = Streak::where('user_id', $user->id)
            ->where('date', '>=', $weekStart)
            ->get();
            
        $this->weeklyStreak = min($weeklyStreaks->count(), 7);

        $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $currentDay = now()->dayOfWeek === 0 ? 6 : now()->dayOfWeek - 1; // 0 = Mon, 6 = Sun

        $completedDates = $weeklyStreaks->pluck('date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();

        $this->weekData = array_map(function ($day, $index) use ($currentDay, $weekStart, $completedDates) {
            $date = $weekStart->copy()->addDays($index)->format('Y-m-d');
            $completed = in_array($date, $completedDates);
            $xp = $completed ? 50 : 0; // Mock 50 XP per streak day
            
            return [
                'day' => $day,
                'completed' => $completed,
                'xp' => $xp,
                'isToday' => $index === $currentDay,
                'isFuture' => $index > $currentDay,
                'index' => $index,
            ];
        }, $weekDays, array_keys($weekDays));

        $this->xpThisWeek = array_reduce($this->weekData, fn($sum, $item) => $sum + $item['xp'], 0);

        $this->streakRewards = [
            ['days' => 3, 'xp' => 50, 'achieved' => $this->streak >= 3],
            ['days' => 7, 'xp' => 150, 'achieved' => $this->streak >= 7],
            ['days' => 14, 'xp' => 350, 'achieved' => $this->streak >= 14],
            ['days' => 30, 'xp' => 1000, 'achieved' => $this->streak >= 30],
        ];
    }

    public function render()
    {
        return view('livewire.faculty.streaks')->layout('layouts.app');
    }
}
