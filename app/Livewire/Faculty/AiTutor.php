<?php

namespace App\Livewire\Faculty;

use App\Services\AiTutorService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class AiTutor extends Component
{
    public array $messages = [];

    public string $input = '';

    public bool $isLoading = false;

    public string $currentContext = 'general';

    #[Locked]
    public array $enrollments = [];

    public function __construct()
    {
        $this->loadInitialContext();
    }

    protected function loadInitialContext(): void
    {
        $user = auth()->user();
        $this->enrollments = $user->enrollments()
            ->with('course')
            ->get()
            ->map(fn ($enrollment) => [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
                'slug' => $enrollment->course->slug,
            ])
            ->all();

        $this->messages = [
            [
                'role' => 'assistant',
                'content' => "Hello! I'm your AI learning assistant. I can help you with:\n\n- 📚 **Course Questions** - Ask about any enrolled course material\n- 💡 **Study Tips** - Get personalized study strategies\n- 🔍 **Concept Explanations** - Break down complex topics\n- 📝 **Quiz Help** - Review and practice for quizzes\n\nSelect a course below or just type your question!",
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    public function sendMessage(): void
    {
        if (blank(trim($this->input))) {
            return;
        }

        $userMessage = trim($this->input);
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];
        $this->input = '';
        $this->isLoading = true;

        $this->processWithAi($userMessage);
    }

    protected function processWithAi(string $userMessage): void
    {
        $service = app(AiTutorService::class);

        $response = $service->getResponse(
            message: $userMessage,
            context: $this->currentContext,
            enrollments: $this->enrollments,
            history: $this->messages
        );

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $response,
            'timestamp' => now()->toIso8601String(),
        ];
        $this->isLoading = false;
    }

    public function setContext(string $context): void
    {
        $this->currentContext = $context;

        $this->messages[] = [
            'role' => 'system',
            'content' => 'Switched to context: '.ucfirst($context),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function clearChat(): void
    {
        $this->loadInitialContext();
    }

    #[On('scroll-to-bottom')]
    public function scrollToBottom(): void
    {
        $this->dispatch('scroll-to-bottom');
    }

    public function render(): View
    {
        return view('livewire.faculty.ai-tutor')
            ->layout('layouts.app');
    }
}
