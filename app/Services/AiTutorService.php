<?php

namespace App\Services;

use App\Models\Progress;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiTutorService
{
    protected ?string $apiKey = null;

    protected string $baseUrl = 'https://api.openai.com/v1';

    protected string $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function getResponse(
        string $message,
        string $context = 'general',
        array $enrollments = [],
        array $history = []
    ): string {
        if (empty($this->apiKey)) {
            return $this->getFallbackResponse($message, $context, $enrollments);
        }

        try {
            return $this->callOpenAi($message, $context, $enrollments, $history);
        } catch (\Throwable $e) {
            report($e);

            return $this->getFallbackResponse($message, $context, $enrollments);
        }
    }

    protected function callOpenAi(
        string $message,
        string $context,
        array $enrollments,
        array $history
    ): string {
        $systemPrompt = $this->buildSystemPrompt($context, $enrollments);

        $messages = collect($history)
            ->filter(fn ($msg) => in_array($msg['role'], ['user', 'assistant']))
            ->map(fn ($msg) => [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ])
            ->prepend(['role' => 'system', 'content' => $systemPrompt])
            ->push(['role' => 'user', 'content' => $message])
            ->values()
            ->toArray();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)
            ->post($this->baseUrl.'/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API error: '.$response->body());
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';
    }

    protected function buildSystemPrompt(string $context, array $enrollments): string
    {
        $courseList = collect($enrollments)
            ->map(fn ($e) => "- {$e['title']} (ID: {$e['id']})")
            ->implode("\n");

        $progressInfo = $this->getProgressInfo($enrollments);

        return <<<PROMPT
You are an enthusiastic, knowledgeable AI learning tutor for a Learning Management System (LMS). Your role is to help faculty members (teachers/instructors) with their professional development and teaching.

CONTEXT: You are in {$context} mode.

ENROLLED COURSES:
{$courseList}

USER PROGRESS:
{$progressInfo}

GUIDELINES:
1. Be friendly, encouraging, and supportive
2. Provide clear, accurate information
3. Use examples and analogies when explaining concepts
4. Break down complex topics into digestible parts
5. Suggest practical study strategies
6. When discussing courses, reference specific course content when possible
7. If you don't have enough context, ask clarifying questions
8. Format responses with markdown for readability
9. Keep responses focused and relevant

Respond to the user's question or request.helpfully.
PROMPT;
    }

    protected function getProgressInfo(array $enrollments): string
    {
        if (empty($enrollments)) {
            return 'No enrolled courses yet.';
        }

        $userId = auth()->id();

        $progress = Progress::query()
            ->where('user_id', $userId)
            ->whereIn('content_id', function ($query) use ($enrollments) {
                $courseIds = collect($enrollments)->pluck('id')->toArray();
                $query->select('id')
                    ->from('contents')
                    ->whereIn('course_id', $courseIds);
            })
            ->count();

        $totalContents = \DB::table('contents')
            ->whereIn('course_id', collect($enrollments)->pluck('id')->toArray())
            ->count();

        $percentage = $totalContents > 0 ? round(($progress / $totalContents) * 100) : 0;

        return "Completed {$progress} of {$totalContents} content items ({$percentage}%)";
    }

    protected function getFallbackResponse(string $message, string $context, array $enrollments): string
    {
        $messageLower = Str::lower($message);

        if (Str::contains($messageLower, ['hello', 'hi', 'hey', 'start'])) {
            return $this->getWelcomeMessage($enrollments);
        }

        if (Str::contains($messageLower, ['help', 'what can you do', 'capabilities'])) {
            return $this->getHelpMessage();
        }

        if (Str::contains($messageLower, ['study tip', 'study strategy', 'learn better'])) {
            return $this->getStudyTips();
        }

        if (Str::contains($messageLower, ['course', 'lesson', 'module', 'topic'])) {
            return $this->getCourseRelatedResponse($message, $enrollments);
        }

        if (Str::contains($messageLower, ['quiz', 'test', 'exam'])) {
            return $this->getQuizHelpResponse();
        }

        if (Str::contains($messageLower, ['explain', 'what is', 'how does', 'definition'])) {
            return $this->getExplanationResponse($message);
        }

        return $this->getGeneralResponse($message, $context, $enrollments);
    }

    protected function getWelcomeMessage(array $enrollments): string
    {
        $courses = collect($enrollments)->pluck('title')->toArray();

        $message = "Welcome back! 👋 I'm excited to help you on your learning journey.\n\n";

        if (! empty($courses)) {
            $message .= "I can see you're enrolled in **".count($courses)." course(s)**:\n";
            foreach (array_slice($courses, 0, 3) as $course) {
                $message .= "- {$course}\n";
            }
            if (count($courses) > 3) {
                $message .= '- And '.(count($courses) - 3)." more...\n";
            }
        } else {
            $message .= "You haven't enrolled in any courses yet. Browse our course catalog to get started!\n";
        }

        $message .= "\nWhat would you like to learn about today?";

        return $message;
    }

    protected function getHelpMessage(): string
    {
        return <<<'HELP'
Here's how I can help you:

**📚 Course Assistance**
- Ask about specific courses, lessons, or modules
- Get summaries of course content
- Understand key concepts within your courses

**💡 Study Support**
- Get personalized study strategies
- Tips for effective learning
- Time management advice

**🔍 Concept Explanations**
- Break down complex topics
- Simple explanations with examples
- Connect concepts to real-world applications

**📝 Quiz & Test Prep**
- Review key concepts
- Practice questions and explanations
- Study strategies for exams

**🎯 Just ask!** Type your question naturally and I'll do my best to help.

HELP;
    }

    protected function getStudyTips(): string
    {
        return <<<'TIPS'
Here are some effective study strategies:

**1. Spaced Repetition** 📅
Review material at increasing intervals (1 day, 3 days, 1 week). This strengthens memory retention.

**2. Active Recall** 🧠
Test yourself instead of passive re-reading. Close your notes and try to recall key points.

**3. Interleaving** 🔀
Mix different topics in one study session. It improves problem-solving and long-term retention.

**4. Pomodoro Technique** ⏱️
Study for 25 minutes, take a 5-minute break. After 4 cycles, take a longer 15-30 minute break.

**5. Feynman Technique** 📖
Explain a concept simply, as if teaching someone else. Identify gaps in your understanding.

**6. Mind Mapping** 🗺️
Create visual diagrams connecting concepts. Great for seeing relationships between ideas.

**7. Sleep & Rest** 😴
Get 7-9 hours of sleep. Your brain consolidates learning during sleep!

Would you like me to elaborate on any of these or suggest strategies for a specific subject?
TIPS;
    }

    protected function getCourseRelatedResponse(string $message, array $enrollments): string
    {
        if (empty($enrollments)) {
            return "You don't have any enrolled courses yet. Would you like to browse our course catalog to find something interesting?";
        }

        $courses = collect($enrollments)->pluck('title')->implode(', ');

        return <<<RESPONSE
I see you're asking about courses! You're currently enrolled in:

**{$courses}**

To help you better, could you tell me:
- Which specific course are you interested in?
- What lesson or topic would you like to explore?
- Or would you like me to give you an overview of your enrolled courses?

I can help explain concepts, summarize lessons, or guide you through course material!
RESPONSE;
    }

    protected function getQuizHelpResponse(): string
    {
        return <<<'QUIZ'
I'd be happy to help you prepare for quizzes! Here are some ways I can assist:

**📋 Quiz Preparation**
- Review key concepts that are likely to be tested
- Explain fundamental principles
- Clarify any confusing topics

**💪 Practice Strategies**
- Create practice questions for you
- Walk through example problems
- Explain the reasoning behind answers

**🎯 What to Focus On**
- Review your course materials and notes
- Pay attention to bold terms and key definitions
- Understand relationships between concepts

**⚠️ Common Mistakes**
- Don't just memorize - understand the "why"
- Read questions carefully
- Manage your time wisely during the quiz

Would you like me to help with a specific topic or create some practice questions for you?
QUIZ;
    }

    protected function getExplanationResponse(string $message): string
    {
        return "I'd be happy to explain that concept! To give you the best answer, could you tell me:\n\n1. **Which course** is this related to?\n2. What **specific topic** within that course?\n3. How much **detail** do you need (overview vs. deep dive)?\n\nOr, if you'd like a general explanation, just let me know what concept you'd like to understand!";
    }

    protected function getGeneralResponse(string $message, string $context, array $enrollments): string
    {
        return <<<RESPONSE
That's a great question! Let me help you with that.

I'm currently set to **{$context}** mode and I'm familiar with your {$this->getEnrollmentCountText($enrollments)}.

To give you the most relevant answer, could you provide a bit more context? For example:
- Which course is this related to?
- What specific topic are you exploring?
- Are you preparing for a quiz or studying for understanding?

I'm here to help make your learning journey smoother!
RESPONSE;
    }

    protected function getEnrollmentCountText(array $enrollments): string
    {
        $count = count($enrollments);

        return match (true) {
            $count === 0 => 'enrollments',
            $count === 1 => '1 enrolled course',
            default => "{$count} enrolled courses",
        };
    }
}
