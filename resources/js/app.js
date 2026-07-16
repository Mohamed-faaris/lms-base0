import Sortable from 'sortablejs';

window.Sortable = Sortable;

document.addEventListener('alpine:init', () => {
    Alpine.data('youtubePlayer', (videoId, backendTime, itemId) => ({
        videoId,
        backendTime,
        itemId,
        player: null,
        completed: false,
        currentTime: 0,
        duration: 0,
        maxSeek: 0,

        dragging: false,
        seekTimeline: null,

        get lsKey() {
            return `yt_maxseek_${this.videoId}`;
        },

        get backendPercent() {
            return this.duration > 0
                ? Math.min(100, (this.backendTime / this.duration) * 100)
                : 0;
        },

        get progressPercent() {
            return this.duration > 0
                ? Math.min(100, (this.currentTime / this.duration) * 100)
                : 0;
        },

        get maxSeekPercent() {
            return this.duration > 0
                ? Math.min(100, (this.maxSeek / this.duration) * 100)
                : 0;
        },

        get formattedTime() {
            const m = Math.floor(this.currentTime / 60);
            const s = Math.floor(this.currentTime % 60);
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        get formattedDuration() {
            const m = Math.floor(this.duration / 60);
            const s = Math.floor(this.duration % 60);
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        },

        init() {
            console.log('🎬 ytPlayer.init', { videoId: this.videoId, backendTime: this.backendTime, itemId: this.itemId });
            const stored = parseFloat(localStorage.getItem(this.lsKey)) || 0;
            this.maxSeek = Math.max(this.backendTime, stored);
            if (window.YT && window.YT.Player) {
                console.log('🎬 YT API already loaded, creating player');
                this.createPlayer();
                return;
            }

            const prev = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = () => {
                console.log('🎬 YT iframe API ready callback fired');
                if (prev) prev();
                this.createPlayer();
            };

            if (!document.querySelector('script[src*="iframe_api"]')) {
                console.log('🎬 Loading YT iframe API script');
                const tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
            } else {
                console.log('🎬 YT API script tag already present, waiting for ready');
            }
        },

        createPlayer() {
            console.log('🎬 Creating YT.Player', { videoId: this.videoId, startAt: this.backendTime });
            const startAt = Math.max(0, this.backendTime - 3);
            this.player = new YT.Player('yt-player', {
                videoId: this.videoId,
                playerVars: {
                    rel: 0,
                    modestbranding: 1,
                    playsinline: 1,
                    start: startAt,
                },
                events: {
                    onReady: (e) => {
                        console.log('🎬 YT.Player onReady', { duration: e.target.getDuration(), videoId: this.videoId });
                        this.duration = this.player.getDuration();
                        this.pollTime();
                    },
                    onStateChange: (e) => {
                        const stateNames = { '-1': 'unstarted', '0': 'ended', '1': 'playing', '2': 'paused', '3': 'buffering', '5': 'cued' };
                        console.log('🎬 YT.Player onStateChange', { state: stateNames[e.data] ?? e.data, currentTime: this.player?.getCurrentTime() });
                        if (e.data === YT.PlayerState.PLAYING) {
                            this.pollTime();
                        }
                        if (e.data === YT.PlayerState.ENDED && !this.completed) {
                            console.log('🎬 Video ENDED event fired — calling markVideoComplete', { itemId: this.itemId, duration: this.duration });
                            this.completed = true;
                            this.currentTime = this.duration;
                            this.$wire.markVideoComplete(this.itemId, this.duration)
                                .then(() => {
                                    console.log('🎬 DB save completed (ENDED path)', { itemId: this.itemId });
                                    localStorage.removeItem(this.lsKey);
                                })
                                .catch((err) => console.error('🎬 DB save failed (ENDED path)', err));
                        }
                    },
                    onError: (e) => {
                        console.error('🎬 YT.Player error', { code: e.data, message: ['INVALID_PARAM', 'HTML5_PLAYER', 'NOT_FOUND', 'BLOCKED', 'NOT_EMBEDDABLE'][e.data - 2] || 'UNKNOWN' });
                    },
                    onPlaybackQualityChange: (e) => {
                        console.log('🎬 YT.Player quality change', { quality: e.data });
                    },
                    onPlaybackRateChange: (e) => {
                        console.log('🎬 YT.Player rate change', { rate: e.data });
                    },
                },
            });
        },

        pollTime() {
            if (!this.player) return;
            this.currentTime = this.player.getCurrentTime();
            this.duration = this.player.getDuration();
            if (this.currentTime > this.maxSeek) {
                this.maxSeek = this.currentTime;
                localStorage.setItem(this.lsKey, String(this.maxSeek));
            }
            if (!this.completed && this.duration > 0 && this.currentTime >= this.duration - 2) {
                console.log('🎬 pollTime detected near-end', { currentTime: this.currentTime, duration: this.duration, itemId: this.itemId });
                this.completed = true;
                this.currentTime = this.duration;
                localStorage.removeItem(this.lsKey);
                this.$wire.markVideoComplete(this.itemId, this.duration)
                    .then(() => console.log('🎬 DB save completed (pollTime path)', { itemId: this.itemId }))
                    .catch((err) => console.error('🎬 DB save failed (pollTime path)', err));
                return;
            }
            if (this.player.getPlayerState() === YT.PlayerState.PLAYING) {
                requestAnimationFrame(() => this.pollTime());
            }
        },

        destroy() {
            localStorage.setItem(this.lsKey, String(this.maxSeek));
            if (this.player && this.player.destroy) {
                this.player.destroy();
                this.player = null;
            }
        },

        seekFromEvent(e, timeline) {
            if (!this.player || !this.duration) return;
            timeline = timeline || e.currentTarget.closest('[data-timeline]');
            if (!timeline) return;
            const rect = timeline.getBoundingClientRect();
            const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            const seekTo = pct * this.duration;
            if (!this.completed && seekTo > this.maxSeek + 2) return;
            this.player.seekTo(seekTo, true);
            this.currentTime = seekTo;
        },

        seekStart(e) {
            if (!this.player || !this.duration) return;
            if (e.button !== 0) return;
            const timeline = e.currentTarget.closest('[data-timeline]');
            if (!timeline) return;
            this.seekTimeline = timeline;
            this.dragging = true;
            this.seekFromEvent(e, timeline);
            const onMove = (ev) => {
                if (!this.dragging) return;
                this.seekFromEvent(ev, this.seekTimeline);
            };
            const onEnd = () => {
                this.dragging = false;
                this.seekTimeline = null;
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onEnd);
            };
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onEnd);
        },
    }));
});