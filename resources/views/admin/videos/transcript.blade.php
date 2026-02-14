@extends('admin.layout')

@section('title', 'Transcript Builder')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Transcript Builder</h2>
            <div class="muted">Step 2: play the video and refine transcript segments.</div>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="/admin/videos/{{ $video->id }}/transcript/auto" class="m-0">
                @csrf
                <button class="btn btn-outline-secondary" type="submit">Auto-generate</button>
            </form>
            <a class="btn btn-outline-secondary" href="/admin/videos">Back to list</a>
        </div>
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

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="video-shell d-flex flex-column gap-2">
                <strong>{{ $video->title }}</strong>
                <video id="video" controls preload="metadata" style="width:100%;border-radius:12px;">
                <source src="{{ $videoUrl }}" type="video/mp4" />
                Your browser does not support the video tag.
                </video>
                <div class="muted">Tip: click a transcript row to jump to that time.</div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <form id="transcript-form" method="POST" action="/admin/videos/{{ $video->id }}/transcript">
                        @csrf
                        <input type="hidden" name="segments_json" id="segments_json" />
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button class="btn btn-primary" type="button" id="add-segment">Add segment</button>
                            <button class="btn btn-outline-secondary" type="button" id="sort-segments">Sort by time</button>
                            <button class="btn btn-outline-secondary" type="submit">Save transcript</button>
                        </div>
                        <div class="muted">Times are seconds. Make sure start &lt; end.</div>
                        <div id="segments" class="segments-list"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const segments = @json($segments);
            const list = document.getElementById('segments');
            const addBtn = document.getElementById('add-segment');
            const sortBtn = document.getElementById('sort-segments');
            const form = document.getElementById('transcript-form');
            const hidden = document.getElementById('segments_json');
            const video = document.getElementById('video');

            function render() {
                list.innerHTML = '';
                segments.forEach((seg, index) => {
                    const row = document.createElement('div');
                    row.className = 'segment-row';
                    row.dataset.index = String(index);

                    const start = document.createElement('input');
                    start.type = 'number';
                    start.min = '0';
                    start.step = '0.1';
                    start.value = seg.start_time;
                    start.className = 'form-control form-control-sm';

                    const end = document.createElement('input');
                    end.type = 'number';
                    end.min = '0';
                    end.step = '0.1';
                    end.value = seg.end_time;
                    end.className = 'form-control form-control-sm';

                    const text = document.createElement('textarea');
                    text.value = seg.text;
                    text.className = 'form-control form-control-sm';

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'btn btn-link btn-sm text-decoration-none p-0';
                    remove.textContent = 'Remove';

                    row.appendChild(start);
                    row.appendChild(end);
                    row.appendChild(text);
                    row.appendChild(remove);

                    row.addEventListener('click', (event) => {
                        if (event.target.tagName === 'BUTTON') return;
                        if (video) {
                            video.currentTime = Number(seg.start_time || 0);
                            video.play();
                        }
                    });

                    start.addEventListener('input', () => {
                        seg.start_time = Number(start.value || 0);
                    });
                    end.addEventListener('input', () => {
                        seg.end_time = Number(end.value || 0);
                    });
                    text.addEventListener('input', () => {
                        seg.text = text.value;
                    });
                    remove.addEventListener('click', () => {
                        segments.splice(index, 1);
                        render();
                    });

                    list.appendChild(row);
                });
            }

            addBtn.addEventListener('click', () => {
                segments.push({ start_time: 0, end_time: 0, text: '' });
                render();
            });

            sortBtn.addEventListener('click', () => {
                segments.sort((a, b) => Number(a.start_time) - Number(b.start_time));
                render();
            });

            form.addEventListener('submit', (event) => {
                const prepared = segments
                    .map((seg, index) => ({
                        start_time: Number(seg.start_time || 0),
                        end_time: Number(seg.end_time || 0),
                        text: String(seg.text || '').trim(),
                        position: index + 1,
                    }))
                    .filter((seg) => seg.text.length > 0 && seg.end_time > seg.start_time);

                if (!prepared.length) {
                    event.preventDefault();
                    alert('Please add at least one valid segment.');
                    return;
                }
                hidden.value = JSON.stringify(prepared);
            });

            render();
        })();
    </script>
@endsection
