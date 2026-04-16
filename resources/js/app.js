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
    let playerInstance = null;
    const hasPlayerMethod = (methodName) => typeof playerInstance?.[methodName] === 'function';

    return {
        ...config,
        pollInterval: null,
        hideNoticeTimeout: null,
        visibilityChangeHandler: null,
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
        timestampedQuizOpen: false,
        completedTimestampedQuizIds: [...(config.completedTimestampedQuizIds ?? [])],

        init() {
            this.volume = Number.isFinite(this.volume) ? this.volume : 80;
            this.playbackRate = this.playbackRates.includes(this.playbackRate) ? this.playbackRate : 1;

            // Debug logging
            console.log('Playback rate initialization:', {
                stored: window.localStorage.getItem('course-player-rate'),
                parsed: Number.parseFloat(window.localStorage.getItem('course-player-rate') ?? '1'),
                validated: this.playbackRate,
                availableRates: this.playbackRates
            });

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

                playerInstance = new window.YT.Player(this.playerElementId, {
                    videoId: this.youtubeId,
                    width: '100%',
                    height: '100%',
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
                        onReady: (event) => this.handlePlayerReady(event),
                        onStateChange: (event) => this.handlePlayerStateChange(event),
                    },
                });
            });

            // Add visibility change listener to pause video when tab is not active
            this.visibilityChangeHandler = () => {
                // Check for hidden state across different browsers
                const isHidden = document.hidden || document.webkitHidden || document.mozHidden || document.msHidden;

                console.log('Visibility change detected:', {
                    hidden: isHidden,
                    ready: this.ready,
                    isVideoLesson: this.isVideoLesson,
                    playing: this.playing
                });

                if (! this.ready || ! this.isVideoLesson) {
                    return;
                }

                if (isHidden && this.playing && hasPlayerMethod('pauseVideo')) {
                    console.log('Pausing video due to tab becoming hidden');
                    playerInstance.pauseVideo();
                }
            };

            // Add event listeners for different browser prefixes
            const visibilityEvents = ['visibilitychange', 'webkitvisibilitychange', 'mozvisibilitychange', 'msvisibilitychange'];
            visibilityEvents.forEach(event => {
                document.addEventListener(event, this.visibilityChangeHandler);
            });
        },

        destroy() {
            window.clearInterval(this.pollInterval);
            window.clearTimeout(this.hideNoticeTimeout);

            // Remove visibility change listeners
            if (this.visibilityChangeHandler) {
                const visibilityEvents = ['visibilitychange', 'webkitvisibilitychange', 'mozvisibilitychange', 'msvisibilitychange'];
                visibilityEvents.forEach(event => {
                    document.removeEventListener(event, this.visibilityChangeHandler);
                });
            }

            if (hasPlayerMethod('destroy')) {
                playerInstance.destroy();
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

        handlePlayerReady(event) {
            playerInstance = event?.target ?? playerInstance;
            const iframe = hasPlayerMethod('getIframe') ? playerInstance.getIframe() : null;
            if (iframe) {
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.style.position = 'absolute';
                iframe.style.inset = '0';
            }
            this.ready = true;
            this.playerDuration = this.displayDurationFromPlayer();
            this.currentTime = this.startTimeSeconds;
            this.sliderValue = this.currentTime;
            this.maxTime = this.startTimeSeconds;

            if (hasPlayerMethod('setVolume')) {
                playerInstance.setVolume(this.volume);
            }

            // Ensure playback rate is applied immediately and matches the stored/validated value
            this.applyPlaybackRate(this.playbackRate);

            // Double-check that the YouTube player is using our rate after a short delay
            setTimeout(() => {
                if (hasPlayerMethod('getPlaybackRate')) {
                    const currentRate = playerInstance.getPlaybackRate();
                    if (Math.abs(currentRate - this.playbackRate) > 0.01) {
                        console.log(`Correcting playback rate from ${currentRate} to ${this.playbackRate}`);
                        this.applyPlaybackRate(this.playbackRate);
                    }
                }
            }, 200);

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
                if (! this.ready || ! hasPlayerMethod('getCurrentTime')) {
                    return;
                }

                const rawTime = playerInstance.getCurrentTime();
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

                this.checkTimestampedQuizzes();

                if (this.endTimeSeconds > this.startTimeSeconds && this.currentTime >= this.endTimeSeconds - 0.35) {
                    if (hasPlayerMethod('pauseVideo')) {
                        playerInstance.pauseVideo();
                    }
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

        checkTimestampedQuizzes() {
            if (! this.isVideoLesson || this.timestampedQuizOpen) {
                return;
            }

            const nextCheckpoint = (this.timestampedQuizzes ?? []).find((quiz) => {
                if (this.completedTimestampedQuizIds.includes(quiz.id)) {
                    return false;
                }

                return this.currentTime >= Math.max(this.startTimeSeconds, quiz.timestamp_seconds);
            });

            if (! nextCheckpoint) {
                return;
            }

            if (hasPlayerMethod('pauseVideo')) {
                playerInstance.pauseVideo();
            }

            this.timestampedQuizOpen = true;
            this.$wire.openTimestampedQuiz(nextCheckpoint.id);
        },

        displayDurationFromPlayer() {
            if (this.endTimeSeconds > this.startTimeSeconds) {
                return this.endTimeSeconds;
            }

            const duration = hasPlayerMethod('getDuration') ? playerInstance.getDuration() : 0;

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
            if (! this.ready || ! hasPlayerMethod('seekTo')) {
                return;
            }

            const boundedTime = Math.max(this.startTimeSeconds, Math.min(nextTime, this.displayDuration));
            playerInstance.seekTo(boundedTime, true);
            this.currentTime = boundedTime;
            this.sliderValue = boundedTime;
        },

        handlePlayPause() {
            if (! this.ready) {
                return;
            }

            if (this.playing && hasPlayerMethod('pauseVideo')) {
                playerInstance.pauseVideo();

                return;
            }

            if (hasPlayerMethod('playVideo')) {
                playerInstance.playVideo();
            }
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

            if (hasPlayerMethod('setVolume')) {
                playerInstance.setVolume(this.volume);
            }
        },

        handlePlaybackRateChange(event) {
            this.playbackRate = Number.parseFloat(event.target.value);
            window.localStorage.setItem('course-player-rate', String(this.playbackRate));
            this.applyPlaybackRate(this.playbackRate);
        },

        applyPlaybackRate(rate) {
            if (! hasPlayerMethod('setPlaybackRate')) {
                return;
            }

            playerInstance.setPlaybackRate(rate);
        },

        toggleCaptions() {
            this.captionsEnabled = ! this.captionsEnabled;
            window.localStorage.setItem('course-player-captions', this.captionsEnabled ? '1' : '0');
            this.applyCaptions();
        },

        applyCaptions() {
            if (! playerInstance) {
                return;
            }

            try {
                if (this.captionsEnabled) {
                    // Try to enable captions by setting a track
                    if (hasPlayerMethod('setOption')) {
                        // Try to set to English, or get available tracks if possible
                        try {
                            const tracklist = playerInstance.getOption('captions', 'tracklist');
                            if (tracklist && tracklist.length > 0) {
                                // Use the first available track
                                playerInstance.setOption('captions', 'track', {languageCode: tracklist[0].languageCode});
                            } else {
                                // Fallback to English
                                playerInstance.setOption('captions', 'track', {languageCode: 'en'});
                            }
                        } catch (e) {
                            // If getOption fails, try setting to English
                            playerInstance.setOption('captions', 'track', {languageCode: 'en'});
                        }
                    } else if (hasPlayerMethod('loadModule')) {
                        playerInstance.loadModule('captions');
                    }
                } else {
                    // Disable captions
                    if (hasPlayerMethod('setOption')) {
                        playerInstance.setOption('captions', 'track', {});
                    } else if (hasPlayerMethod('unloadModule')) {
                        playerInstance.unloadModule('captions');
                    }
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

        handleTimestampedQuizResolved(detail) {
            const payload = Array.isArray(detail) ? detail[0] : detail;
            const quizId = payload?.quizId;

            if (typeof quizId === 'number' && ! this.completedTimestampedQuizIds.includes(quizId)) {
                this.completedTimestampedQuizIds.push(quizId);
            }

            this.timestampedQuizOpen = false;
            this.showBlockedMessage('Checkpoint completed. Press play to continue.');
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
