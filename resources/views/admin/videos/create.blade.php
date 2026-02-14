@extends('admin.layout')

@section('title', 'Upload Video')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Upload video</h2>
            <div class="muted">Upload your video file. You'll create the transcript in the next step.</div>
        </div>
        <a class="btn btn-outline-secondary" href="/admin/videos">Back to list</a>
    </div>

    @if ($errors->any())
        <div class="notice error">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form id="upload-form" method="POST" action="/admin/videos" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="title" required value="{{ old('title') }}" placeholder="Street interview: confidence" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Level</label>
                        <select class="form-select" name="level" required>
                            @foreach (['A1','A2','B1','B2','C1'] as $level)
                                <option value="{{ $level }}" @if(old('level') === $level) selected @endif>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Language</label>
                        <input class="form-control" name="language" value="{{ old('language', 'en') }}" placeholder="en" />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Topic tags (comma separated)</label>
                        <input class="form-control" name="topic_tags" value="{{ old('topic_tags') }}" placeholder="interview, confidence" />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Video file</label>
                        <input class="form-control" type="file" name="video" accept="video/*" required />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" placeholder="Short description">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mt-4">
                    <button class="btn btn-primary" type="submit">Upload video</button>
                    <div class="muted">Use the transcript builder to auto-generate or manually create segments.</div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('upload-form');
            if (!form) return;

            form.addEventListener('submit', () => {
                const button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.textContent = 'Uploading...';
                    button.disabled = true;
                }
            });
        })();
    </script>
@endsection
