<div>
    @include('livewire.admin.courses.partials.form', [
        'backUrl' => route('admin.courses.index'),
        'cancelUrl' => route('admin.courses.index'),
        'submitLabel' => 'Create Course',
        'pageTitle' => 'Build a course that reads like a real product',
        'pageSummary' => 'Define the course identity, catalog metadata, and learner outcomes before you start assembling topics and modules.',
        'isEditing' => false,
        'course' => null,
        'difficultyOptions' => $difficultyOptions,
    ])
</div>
