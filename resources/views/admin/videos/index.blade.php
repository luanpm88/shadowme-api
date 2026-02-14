@extends('admin.layout')

@section('title', 'Videos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Video Library</h2>
            <div class="muted">Upload new videos and manage transcripts.</div>
        </div>
        <a class="btn btn-primary" href="/admin/videos/create">Upload new video</a>
    </div>

    @if (session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    @if ($videos->count())
        <div class="table-shell">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-video">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Level</th>
                        <th>Language</th>
                        <th>Transcript</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($videos as $video)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $video->title }}</div>
                                <div class="muted">{{ $video->duration_seconds }}s · {{ $video->source_type }}</div>
                            </td>
                            <td><span class="badge badge-soft">{{ $video->level }}</span></td>
                            <td>{{ $video->language ?? 'en' }}</td>
                            <td>
                                @if ($video->transcript)
                                    <span class="badge badge-soft">{{ $video->transcript->segments->count() }} segments</span>
                                @else
                                    <span class="muted">Not generated</span>
                                @endif
                            </td>
                            <td class="muted">{{ $video->updated_at?->diffForHumans() }}</td>
                            <td>
                                <a class="btn btn-outline-secondary btn-sm" href="/admin/videos/{{ $video->id }}/transcript">Edit transcript</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $videos->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <p class="mb-0">No videos yet. Upload your first video to start generating transcripts.</p>
            </div>
        </div>
    @endif
@endsection
