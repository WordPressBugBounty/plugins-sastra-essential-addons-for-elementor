(function ($) {
    "use strict";

    /* -------- Countdown Timer ------- */

    const Countdown = function ($scope, $) {

        var countDownWrap = $scope.children('.elementor-widget-container').children('.tmpcoder-countdown-wrap'),
            countDownInterval = null,
            dataInterval = countDownWrap.data('interval'),
            dataShowAgain = countDownWrap.data('show-again'),
            endTime = new Date(dataInterval * 1000);

        // Evergreen End Time
        if ('evergreen' === countDownWrap.data('type')) {
            var evergreenDate = new Date(),
                widgetID = $scope.attr('data-id'),
                settings = JSON.parse(localStorage.getItem('TmpcoderCountDownSettings')) || {};

            // End Time
            if (settings.hasOwnProperty(widgetID)) {
                if (Object.keys(settings).length === 0 || dataInterval !== settings[widgetID].interval) {
                    endTime = evergreenDate.setSeconds(evergreenDate.getSeconds() + dataInterval);
                } else {
                    endTime = settings[widgetID].endTime;
                }
            } else {
                endTime = evergreenDate.setSeconds(evergreenDate.getSeconds() + dataInterval);
            }

            if (endTime + dataShowAgain < evergreenDate.setSeconds(evergreenDate.getSeconds())) {
                endTime = evergreenDate.setSeconds(evergreenDate.getSeconds() + dataInterval);
            }

            // Settings
            settings[widgetID] = {
                interval: dataInterval,
                endTime: endTime
            };

            // Save Settings in Browser
            localStorage.setItem('TmpcoderCountDownSettings', JSON.stringify(settings));
        }

        // Start CountDown
        if (!editorCheck()) { //tmp
        }
        // Init on Load
        initCountDown();

        // Start CountDown
        countDownInterval = setInterval(initCountDown, 1000);

        function initCountDown() {
            var timeLeft = endTime - new Date();

            var numbers = {
                days: Math.floor(timeLeft / (1000 * 60 * 60 * 24)),
                hours: Math.floor(timeLeft / (1000 * 60 * 60) % 24),
                minutes: Math.floor(timeLeft / 1000 / 60 % 60),
                seconds: Math.floor(timeLeft / 1000 % 60)
            };

            if (numbers.days < 0 || numbers.hours < 0 || numbers.minutes < 0) {
                numbers = {
                    days: 0,
                    hours: 0,
                    minutes: 0,
                    seconds: 0
                };
            }

            $scope.find('.tmpcoder-countdown-number').each(function () {
                var number = numbers[$(this).attr('data-item')];

                if (1 === number.toString().length) {
                    number = '0' + number;
                }

                $(this).text(number);

                // Labels
                var labels = $(this).next();

                if (labels.length) {
                    if (!$(this).hasClass('tmpcoder-countdown-seconds')) {
                        var labelText = labels.data('text');

                        if ('01' == number) {
                            labels.text(labelText.singular);
                        } else {
                            labels.text(labelText.plural);
                        }
                    }
                }
            });

            // Stop Counting
            if (timeLeft < 0) {
                clearInterval(countDownInterval);

                // Actions
                expiredActions();
            }
        }

        function expiredActions() {
            var dataActions = countDownWrap.data('actions');

            if (!editorCheck()) {

                if (dataActions.hasOwnProperty('hide-timer')) {
                    countDownWrap.hide();
                }

                if (dataActions.hasOwnProperty('hide-element')) {
                    $(dataActions['hide-element']).hide();
                }

                if (dataActions.hasOwnProperty('message')) {
                    if (!$scope.children('.elementor-widget-container').children('.tmpcoder-countdown-message').length) {
                        countDownWrap.after('<div class="tmpcoder-countdown-message">' + dataActions['message'] + '</div>');
                    }
                }

                if (dataActions.hasOwnProperty('redirect')) {
                    window.location.href = TmpcodersanitizeURL(dataActions['redirect']);
                }

                if (dataActions.hasOwnProperty('load-template')) {
                    countDownWrap.next('.elementor').show();
                }
            }
        }
    }

    /* -------- Countdown Timer End ------- */

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/tmpcoder-countdown.default', Countdown);
    });
})(jQuery);

