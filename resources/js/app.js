document.addEventListener('alpine:init', () => {
    Alpine.data('youtubePlayer', (videoId, backendTime, itemId) => ({
        videoId,
        backendTime,
        itemId,
        player: null,
        completed: false,
        currentTime: 0,
        duration: 0,

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
            if (window.YT && window.YT.Player) {
                this.createPlayer();
                return;
            }

            const prev = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = () => {
                if (prev) prev();
                this.createPlayer();
            };

            if (!document.querySelector('script[src*="iframe_api"]')) {
                const tag = document.createElement('script');
                tag.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(tag);
            }
        },

        createPlayer() {
            this.player = new YT.Player('yt-player', {
                videoId: this.videoId,
                playerVars: {
                    rel: 0,
                    modestbranding: 1,
                    playsinline: 1,
                    start: this.backendTime,
                },
                events: {
                    onReady: () => {
                        this.duration = this.player.getDuration();
                        this.pollTime();
                    },
                    onStateChange: (e) => {
                        if (e.data === YT.PlayerState.PLAYING) {
                            this.pollTime();
                        }
                        if (e.data === YT.PlayerState.ENDED && !this.completed) {
                            this.completed = true;
                            this.currentTime = this.duration;
                            $wire.markVideoComplete(this.itemId, this.duration);
                        }
                    },
                },
            });
        },

        pollTime() {
            if (!this.player) return;
            this.currentTime = this.player.getCurrentTime();
            this.duration = this.player.getDuration();
            if (this.player.getPlayerState() === YT.PlayerState.PLAYING) {
                requestAnimationFrame(() => this.pollTime());
            }
        },

        destroy() {
            if (this.player && this.player.destroy) {
                this.player.destroy();
                this.player = null;
            }
        },
    }));
});