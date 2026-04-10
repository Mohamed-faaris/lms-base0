<div>
    @include('livewire.admin.courses.partials.form', [
        'backUrl' => route('admin.courses.show', $course->id),
        'cancelUrl' => route('admin.courses.show', $course->id),
        'submitLabel' => 'Save Changes',
        'pageTitle' => 'Refine the course experience before learners see it',
        'pageSummary' => 'Tune the positioning, metadata, and narrative so the course page, enrollment flow, and structure all stay aligned.',
        'isEditing' => true,
        'course' => $course,
        'difficultyOptions' => $difficultyOptions,
    ])
</div>
