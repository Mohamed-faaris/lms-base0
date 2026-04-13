const ensureYoutubeIframeApi = (() => {
    let promise = null;

    return () => {
        if (window.YT?.Player) {
            return Promise.resolve(window.YT);
        }

        if (promise) {
            return promise;
        }

        promise = new Promise((resolve) => {
            const previousReady = window.onYouTubeIframeAPIReady;

            window.onYouTubeIframeAPIReady = () => {
                if (typeof previousReady === 'function') {
                    previousReady();
                }

                resolve(window.YT);
            };

            if (! document.querySelector('script[data-youtube-iframe-api]')) {
                const script = document.createElement('script');
                script.src = 'https://www.youtube.com/iframe_api';
                script.async = true;
                script.dataset.youtubeIframeApi = 'true';
                document.head.appendChild(script);
            }
        });

        return promise;
    };
})();

window.courseVideoPlayer = function (config) {
    return {
        ...config,
        player: null,
        pollInterval: null,
        hideNoticeTimeout: null,
        playing: false,
        ready: false,
        currentTime: config.startTimeSeconds,
        sliderValue: config.startTimeSeconds,
        maxTime: config.startTimeSeconds,
        playerDuration: config.endTimeSeconds > config.startTimeSeconds ? config.endTimeSeconds : 0,
        volume: Number.parseInt(window.localStorage.getItem('course-player-volume') ?? '80', 10),
        playbackRate: Number.parseFloat(window.localStorage.getItem('course-player-rate') ?? '1'),
        captionsEnabled: window.localStorage.getItem('course-player-captions') === '1',
        playbackRates: [0.75, 1, 1.25, 1.5, 1.75, 2],
        blockedMessage: '',
        watchUnlocked: config.isCompleted || ! config.isVideoLesson,

        init() {
            this.volume = Number.isFinite(this.volume) ? this.volume : 80;
            this.playbackRate = this.playbackRates.includes(this.playbackRate) ? this.playbackRate : 1;

            if (! this.isVideoLesson) {
                this.maxTime = this.endTimeSeconds > this.startTimeSeconds ? this.endTimeSeconds : this.startTimeSeconds;
                this.currentTime = this.maxTime;
                this.sliderValue = this.currentTime;
                this.playerDuration = this.maxTime;

                return;
            }

            ensureYoutubeIframeApi().then(() => {
                if (! this.$refs.playerHost) {
                    return;
                }

                this.player = new window.YT.Player(this.playerElementId, {
                    videoId: this.youtubeId,
                    playerVars: {
                        controls: 0,
                        disablekb: 1,
                        fs: 0,
                        modestbranding: 1,
                        playsinline: 1,
                        rel: 0,
                        start: this.startTimeSeconds,
                        end: this.endTimeSeconds > this.startTimeSeconds ? this.endTimeSeconds : undefined,
                    },
                    events: {
                        onReady: () => this.handlePlayerReady(),
                        onStateChange: (event) => this.handlePlayerStateChange(event),
                    },
                });
            });
        },

        destroy() {
            window.clearInterval(this.pollInterval);
            window.clearTimeout(this.hideNoticeTimeout);

            if (this.player?.destroy) {
                this.player.destroy();
            }
        },

        get sliderMax() {
            return this.displayDuration;
        },

        get displayDuration() {
            return this.endTimeSeconds > this.startTimeSeconds
                ? this.endTimeSeconds
                : Math.max(this.playerDuration, this.startTimeSeconds);
        },

        get watchPercent() {
            const watchedSeconds = Math.max(0, this.maxTime - this.startTimeSeconds);
            const playableSeconds = Math.max(1, this.displayDuration - this.startTimeSeconds);

            return Math.min(100, Math.round((watchedSeconds / playableSeconds) * 100));
        },

        handlePlayerReady() {
            this.ready = true;
            this.playerDuration = this.displayDurationFromPlayer();
            this.currentTime = this.startTimeSeconds;
            this.sliderValue = this.currentTime;
            this.maxTime = this.startTimeSeconds;

            this.player.setVolume(this.volume);
            this.applyPlaybackRate(this.playbackRate);
            this.applyCaptions();
            this.startPolling();
        },

        handlePlayerStateChange(event) {
            this.playing = event.data === window.YT.PlayerState.PLAYING;

            if (event.data === window.YT.PlayerState.ENDED) {
                this.maxTime = this.displayDuration;
                this.currentTime = this.displayDuration;
                this.sliderValue = this.currentTime;
                this.unlockWatchGate();
            }
        },

        startPolling() {
            window.clearInterval(this.pollInterval);

            this.pollInterval = window.setInterval(() => {
                if (! this.player || ! this.ready) {
                    return;
                }

                const rawTime = this.player.getCurrentTime();
                const currentTime = Number.isFinite(rawTime) ? rawTime : this.startTimeSeconds;
                const boundedTime = Math.max(this.startTimeSeconds, currentTime);

                this.playerDuration = this.displayDurationFromPlayer();

                if (! this.seekForwardEnabled && boundedTime > this.maxTime + 1.35) {
                    this.seekTo(this.maxTime);
                    this.showBlockedMessage('Forward seek is locked until you watch that segment.');

                    return;
                }

                this.currentTime = Math.min(boundedTime, this.displayDuration);
                this.sliderValue = this.currentTime;
                this.maxTime = Math.max(this.maxTime, this.currentTime);

                if (this.endTimeSeconds > this.startTimeSeconds && this.currentTime >= this.endTimeSeconds - 0.35) {
                    this.player.pauseVideo();
                    this.seekTo(this.endTimeSeconds);
                    this.maxTime = this.endTimeSeconds;
                    this.currentTime = this.endTimeSeconds;
                    this.sliderValue = this.currentTime;
                    this.unlockWatchGate();
                }

                if (! this.watchUnlocked && this.watchPercent >= this.watchRequirementPercent) {
                    this.unlockWatchGate();
                }
            }, 250);
        },

        displayDurationFromPlayer() {
            if (this.endTimeSeconds > this.startTimeSeconds) {
                return this.endTimeSeconds;
            }

            const duration = this.player?.getDuration?.() ?? 0;

            return Number.isFinite(duration) && duration > 0 ? duration : this.startTimeSeconds + 1;
        },

        handleSeekInput(event) {
            this.sliderValue = Number.parseFloat(event.target.value);
        },

        handleSeekChange(event) {
            const nextTime = Number.parseFloat(event.target.value);

            if (! this.canSeekTo(nextTime)) {
                this.sliderValue = this.currentTime;
                this.showBlockedMessage('You are not allowed to seek forward.');

                return;
            }

            this.seekTo(nextTime);
        },

        canSeekTo(nextTime) {
            if (this.seekForwardEnabled) {
                return true;
            }

            return nextTime <= this.maxTime + 0.35;
        },

        seekTo(nextTime) {
            if (! this.player || ! this.ready) {
                return;
            }

            const boundedTime = Math.max(this.startTimeSeconds, Math.min(nextTime, this.displayDuration));
            this.player.seekTo(boundedTime, true);
            this.currentTime = boundedTime;
            this.sliderValue = boundedTime;
        },

        handlePlayPause() {
            if (! this.player || ! this.ready) {
                return;
            }

            if (this.playing) {
                this.player.pauseVideo();

                return;
            }

            this.player.playVideo();
        },

        handleRewind() {
            this.seekTo(Math.max(this.startTimeSeconds, this.currentTime - this.rewindSeconds));
        },

        handleForward() {
            if (! this.seekForwardEnabled) {
                return;
            }

            this.seekTo(this.currentTime + this.forwardSeconds);
        },

        handleVolumeChange(event) {
            this.volume = Number.parseInt(event.target.value, 10);
            window.localStorage.setItem('course-player-volume', String(this.volume));

            if (this.player?.setVolume) {
                this.player.setVolume(this.volume);
            }
        },

        handlePlaybackRateChange(event) {
            this.playbackRate = Number.parseFloat(event.target.value);
            window.localStorage.setItem('course-player-rate', String(this.playbackRate));
            this.applyPlaybackRate(this.playbackRate);
        },

        applyPlaybackRate(rate) {
            if (! this.player?.setPlaybackRate) {
                return;
            }

            this.player.setPlaybackRate(rate);
        },

        toggleCaptions() {
            this.captionsEnabled = ! this.captionsEnabled;
            window.localStorage.setItem('course-player-captions', this.captionsEnabled ? '1' : '0');
            this.applyCaptions();
        },

        applyCaptions() {
            if (! this.player) {
                return;
            }

            try {
                if (this.captionsEnabled) {
                    this.player.loadModule('captions');
                } else {
                    this.player.unloadModule('captions');
                }
            } catch (error) {
                console.debug('Caption toggle unavailable', error);
            }
        },

        toggleFullscreen() {
            if (! this.$refs.playerShell) {
                return;
            }

            if (document.fullscreenElement) {
                document.exitFullscreen();

                return;
            }

            this.$refs.playerShell.requestFullscreen?.();
        },

        unlockWatchGate() {
            this.watchUnlocked = true;
        },

        startQuiz() {
            if (! this.watchUnlocked && ! this.isCompleted) {
                this.showBlockedMessage(`Watch at least ${this.watchRequirementPercent}% to unlock the quiz.`);

                return;
            }

            this.$wire.startQuiz(this.watchUnlocked || this.isCompleted);
        },

        showBlockedMessage(message) {
            this.blockedMessage = message;
            window.clearTimeout(this.hideNoticeTimeout);
            this.hideNoticeTimeout = window.setTimeout(() => {
                this.blockedMessage = '';
            }, 2400);
        },

        formatTime(totalSeconds) {
            const seconds = Math.max(0, Math.floor(totalSeconds));
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const remainder = seconds % 60;

            if (hours > 0) {
                return `${hours}:${String(minutes).padStart(2, '0')}:${String(remainder).padStart(2, '0')}`;
            }

            return `${minutes}:${String(remainder).padStart(2, '0')}`;
        },
    };
};
