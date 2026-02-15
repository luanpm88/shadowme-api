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

    <form method="GET" action="/admin/videos" class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select">
                        <option value="" {{ empty($active['sort'] ?? '') ? 'selected' : '' }}>Recently added</option>
                        <option value="featured" {{ ($active['sort'] ?? '') === 'featured' ? 'selected' : '' }}>Featured</option>
                        <option value="most_viewed" {{ ($active['sort'] ?? '') === 'most_viewed' ? 'selected' : '' }}>Most views</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select">
                        <option value="">All levels</option>
                        @foreach ($filters['levels'] ?? [] as $level)
                            <option value="{{ $level }}" {{ ($active['level'] ?? '') === $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Topic</label>
                    <select name="topic_tags" class="form-select">
                        <option value="">All topics</option>
                        @foreach ($filters['topic_tags'] ?? [] as $tag)
                            <option value="{{ $tag }}" {{ ($active['topic_tags'] ?? '') === $tag ? 'selected' : '' }}>{{ $tag }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Apply</button>
                    <a class="btn btn-outline-secondary w-100" href="/admin/videos">Clear</a>
                </div>
            </div>
        </div>
    </form>

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
                        <th>Featured</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($videos as $video)
                        <tr>
                            <td>
                                <div class="d-flex gap-3 align-items-start">
                                    @if ($video->getThumbUrl())
                                        <img src="{{ $video->getThumbUrl() }}" alt="{{ $video->title }}" class="video-thumb-small" />
                                    @else
                                        <div class="video-thumb-small bg-secondary d-flex align-items-center justify-content-center">
                                            <span style="color: var(--text-light);">No thumb</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $video->title }}</div>
                                        <div class="muted">{{ $video->duration_seconds }}s · {{ $video->source_type }}</div>
                                    </div>
                                </div>
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
                            <td>
                                @if ($video->is_featured)
                                    <span class="badge bg-success-subtle text-success">Featured</span>
                                @else
                                    <span class="muted">No</span>
                                @endif
                            </td>
                            <td class="muted">{{ $video->updated_at?->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-outline-secondary btn-sm" href="/admin/videos/{{ $video->id }}/transcript">Edit transcript</a>
                                    <form method="POST" action="/admin/videos/{{ $video->id }}/featured" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="featured" value="{{ $video->is_featured ? 0 : 1 }}" />
                                        <button class="btn btn-outline-primary btn-sm" type="submit">
                                            {{ $video->is_featured ? 'Remove feature' : 'Set featured' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/videos/{{ $video->id }}" class="m-0" onsubmit="return confirm('Delete this video? This removes the transcript and files.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                    </form>
                                </div>
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
