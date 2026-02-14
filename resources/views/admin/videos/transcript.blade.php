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
                <div class="muted">Click a segment to jump to that time. Segments auto-highlight during playback.</div>
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
        /**
         * TranscriptItem class - manages individual segment UI and behavior
         */
        class TranscriptItem {
            constructor(data, index, manager) {
                this.data = data;
                this.index = index;
                this.manager = manager;
                this.element = null;
                this.inputs = {};
                this.buttons = {};
            }

            render() {
                const row = document.createElement('div');
                row.className = 'segment-row';
                row.dataset.index = String(this.index);

                // Time inputs
                const startInput = this.createInput('number', this.data.start_time, 'start_time');
                const endInput = this.createInput('number', this.data.end_time, 'end_time');
                
                // Text input
                const textArea = this.createTextArea(this.data.text);

                // Action buttons container
                const actions = document.createElement('div');
                actions.className = 'segment-actions';
                this.buttons.play = this.createActionButton('▶', 'Play', () => this.play());
                this.buttons.pause = this.createActionButton('⏸', 'Pause', () => this.pause());
                this.buttons.replay = this.createActionButton('↻', 'Replay', () => this.replay());
                this.buttons.remove = this.createActionButton('✕', 'Remove', () => this.remove());
                
                actions.appendChild(this.buttons.play);
                actions.appendChild(this.buttons.pause);
                actions.appendChild(this.buttons.replay);
                actions.appendChild(this.buttons.remove);

                row.appendChild(startInput);
                row.appendChild(endInput);
                row.appendChild(textArea);
                row.appendChild(actions);

                this.element = row;
                this.updateButtonVisibility();
                return row;
            }

            createInput(type, value, field) {
                const input = document.createElement('input');
                input.type = type;
                input.min = '0';
                input.step = '0.01';
                input.value = value;
                input.className = 'form-control form-control-sm';
                input.addEventListener('input', () => {
                    this.data[field] = Number(input.value || 0);
                });
                this.inputs[field] = input;
                return input;
            }

            createTextArea(value) {
                const textarea = document.createElement('textarea');
                textarea.value = value;
                textarea.className = 'form-control form-control-sm';
                textarea.rows = 2;
                textarea.addEventListener('input', () => {
                    this.data.text = textarea.value;
                });
                this.inputs.text = textarea;
                return textarea;
            }

            createActionButton(symbol, title, onClick) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-action';
                btn.textContent = symbol;
                btn.title = title;
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    onClick();
                });
                return btn;
            }

            play() {
                const video = this.manager.video;
                if (!video) return;
                
                video.currentTime = this.data.start_time;
                video.play();
                this.manager.setPlayingSegment(this);
            }

            pause() {
                const video = this.manager.video;
                if (video) {
                    video.pause();
                }
                this.manager.setPlayingSegment(null);
            }

            replay() {
                this.play();
            }

            remove() {
                if (confirm('Remove this segment?')) {
                    this.manager.removeSegment(this.index);
                }
            }

            setHighlight(active) {
                if (!this.element) return;
                
                if (active) {
                    this.element.classList.add('segment-active');
                } else {
                    this.element.classList.remove('segment-active');
                }
            }

            isTimeInRange(currentTime) {
                return currentTime >= this.data.start_time && currentTime < this.data.end_time;
            }

            updateButtonVisibility() {
                if (!this.buttons.play || !this.buttons.pause) return;
                
                const isPlaying = this.manager.isVideoPlaying;
                this.buttons.play.style.display = isPlaying ? 'none' : 'flex';
                this.buttons.pause.style.display = isPlaying ? 'flex' : 'none';
            }
        }

        /**
         * TranscriptManager class - manages all segments and video interaction
         */
        class TranscriptManager {
            constructor(initialSegments, videoElement, containerElement) {
                this.segments = initialSegments.map((seg, i) => new TranscriptItem(seg, i, this));
                this.video = videoElement;
                this.container = containerElement;
                this.currentPlayingSegment = null;
                this.activeHighlightedSegment = null;
                this.isVideoPlaying = false;
                
                this.init();
            }

            init() {
                this.render();
                this.setupVideoListeners();
            }

            setupVideoListeners() {
                if (!this.video) return;

                // Track play state
                this.video.addEventListener('play', () => {
                    this.isVideoPlaying = true;
                    this.updateAllButtonVisibility();
                });

                this.video.addEventListener('pause', () => {
                    this.isVideoPlaying = false;
                    this.updateAllButtonVisibility();
                });

                // Update highlight during playback
                this.video.addEventListener('timeupdate', () => {
                    this.updateActiveHighlight();
                    this.checkSegmentEnd();
                });

                // Clear playing segment when video ends
                this.video.addEventListener('ended', () => {
                    this.setPlayingSegment(null);
                });
            }

            updateAllButtonVisibility() {
                this.segments.forEach(segment => segment.updateButtonVisibility());
            }

            updateActiveHighlight() {
                if (!this.video) return;
                
                const currentTime = this.video.currentTime;
                let foundActive = false;

                this.segments.forEach(segment => {
                    if (segment.isTimeInRange(currentTime)) {
                        if (this.activeHighlightedSegment !== segment) {
                            this.clearHighlight();
                            segment.setHighlight(true);
                            this.activeHighlightedSegment = segment;
                            this.scrollToSegment(segment);
                        }
                        foundActive = true;
                    }
                });

                if (!foundActive && this.activeHighlightedSegment) {
                    this.clearHighlight();
                }
            }

            checkSegmentEnd() {
                if (!this.currentPlayingSegment || !this.video) return;

                const currentTime = this.video.currentTime;
                if (currentTime >= this.currentPlayingSegment.data.end_time) {
                    this.video.pause();
                    this.setPlayingSegment(null);
                }
            }

            setPlayingSegment(segment) {
                this.currentPlayingSegment = segment;
            }

            clearHighlight() {
                if (this.activeHighlightedSegment) {
                    this.activeHighlightedSegment.setHighlight(false);
                    this.activeHighlightedSegment = null;
                }
            }

            scrollToSegment(segment) {
                if (segment.element) {
                    segment.element.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest' 
                    });
                }
            }

            render() {
                this.container.innerHTML = '';
                this.segments.forEach(segment => {
                    this.container.appendChild(segment.render());
                });
            }

            addSegment() {
                const newData = { 
                    start_time: this.video ? Number(this.video.currentTime.toFixed(2)) : 0, 
                    end_time: this.video ? Number((this.video.currentTime + 5).toFixed(2)) : 5, 
                    text: '' 
                };
                const newSegment = new TranscriptItem(newData, this.segments.length, this);
                this.segments.push(newSegment);
                this.render();
                
                // Focus new segment's text input
                setTimeout(() => {
                    if (newSegment.inputs.text) {
                        newSegment.inputs.text.focus();
                    }
                }, 100);
            }

            removeSegment(index) {
                this.segments.splice(index, 1);
                // Re-index remaining segments
                this.segments.forEach((seg, i) => seg.index = i);
                this.render();
            }

            sortByTime() {
                this.segments.sort((a, b) => a.data.start_time - b.data.start_time);
                this.segments.forEach((seg, i) => seg.index = i);
                this.render();
            }

            getData() {
                return this.segments.map(seg => seg.data);
            }

            getValidSegments() {
                return this.segments
                    .map((seg, index) => ({
                        start_time: Number(seg.data.start_time || 0),
                        end_time: Number(seg.data.end_time || 0),
                        text: String(seg.data.text || '').trim(),
                        position: index + 1,
                    }))
                    .filter(seg => seg.text.length > 0 && seg.end_time > seg.start_time);
            }
        }

        // Initialize transcript manager
        (function () {
            const initialSegments = @json($segments);
            const video = document.getElementById('video');
            const container = document.getElementById('segments');
            const addBtn = document.getElementById('add-segment');
            const sortBtn = document.getElementById('sort-segments');
            const form = document.getElementById('transcript-form');
            const hiddenInput = document.getElementById('segments_json');

            const manager = new TranscriptManager(initialSegments, video, container);

            // Button handlers
            addBtn.addEventListener('click', () => manager.addSegment());
            sortBtn.addEventListener('click', () => manager.sortByTime());

            // Form submission
            form.addEventListener('submit', (event) => {
                const validSegments = manager.getValidSegments();
                
                if (!validSegments.length) {
                    event.preventDefault();
                    alert('Please add at least one valid segment.');
                    return;
                }
                
                hiddenInput.value = JSON.stringify(validSegments);
            });
        })();
    </script>
@endsection
