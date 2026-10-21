@php($isRtl = app()->getLocale() === 'ar')
<nav class="project-pagination" id="projectPagination" aria-label="{{ __('projects.pagination_aria') }}">
    @if (! $paginator->onFirstPage())
        <a class="pagination-direction previous" href="{{ $paginator->previousPageUrl() }}" aria-label="{{ __('common.previous_page') }}"><x-ui-icon :name="$isRtl ? 'chevronRight' : 'chevronLeft'" /></a>
    @endif
    <div class="project-pagination-pages">
        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
            <a class="pagination-page{{ $page === $paginator->currentPage() ? ' active' : '' }}" href="{{ $paginator->url($page) }}" aria-label="{{ __('common.page') }} {{ $page }}"@if ($page === $paginator->currentPage()) aria-current="page"@endif>{{ $page }}</a>
        @endfor
    </div>
    @if ($paginator->hasMorePages())
        <a class="pagination-direction next" href="{{ $paginator->nextPageUrl() }}" aria-label="{{ __('common.next_page') }}"><x-ui-icon :name="$isRtl ? 'chevronLeft' : 'chevronRight'" /></a>
    @endif
</nav>
