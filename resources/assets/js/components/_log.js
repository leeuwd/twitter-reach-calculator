/* global twitter */

'use strict';

twitter.Log = (function ($) {
    let self = null;
    let module = 'twitter/log';

    return {
        DEBUG_HANDLER: 'consoleHandler',
        INFO_HANDLER: 'consoleHandler',
        WARNING_HANDLER: 'consoleHandler',
        ERROR_HANDLER: 'consoleHandler',

        /**
         * Initialization.
         */
        init: function () {
            self = this;

            if (self.isEnabled()) {
                self.enable();
            }
        },

        /**
         * Returns whether we want to debug,
         * i.e. log to console.
         *
         * @returns {boolean}
         */
        isEnabled: function () {
            return document.location.hostname.toLowerCase().indexOf('dev') >= 0;
        },

        /**
         * Subscribe to remote events.
         */
        enable: function () {
            $.subscribe(module + '/debug', self.debugHandler);
            $.subscribe(module + '/info', self.infoHandler);
            $.subscribe(module + '/warning', self.warningHandler);
            $.subscribe(module + '/error', self.errorHandler);
        },

        /**
         * Shutdown.
         */
        disable: function () {
            $.unsubscribe(module + '/debug');
            $.unsubscribe(module + '/info');
            $.unsubscribe(module + '/warning');
            $.unsubscribe(module + '/error');
        },

        /**
         * Handle debug messages from events.
         *
         * @param {object} e - Event
         * @param {...*} args - Arguments
         */
        debugHandler: function (e, args) {
            self[self.DEBUG_HANDLER]('log', arguments);
        },

        /**
         * Handle info messages from events.
         *
         * @param {object} e - Event
         * @param {...*} args - Arguments
         */
        infoHandler: function (e, args) {
            self[self.INFO_HANDLER]('info', arguments);
        },

        /**
         * Handle warning messages from events.
         *
         * @param {object} e - Event
         * @param {...*} args - Arguments
         */
        warningHandler: function (e, args) {
            self[self.WARNING_HANDLER]('warning', arguments);
        },

        /**
         * Handle error messages from events.
         *
         * @param {object} e - Event
         * @param {...*} args - Arguments
         */
        errorHandler: function (e, args) {
            self[self.ERROR_HANDLER]('error', arguments);
        },

        /**
         * Handle generic messages from events.
         *
         * @param {string} method
         * @param {Arguments} args - Arguments array with event as first element
         */
        consoleHandler: function (method, args) {
            let params = Array.prototype.slice.call(args);

            params.shift();
            params[0] = '%c' + params[0];
            params.splice(1, 0, 'color: blue;');

            /* eslint-disable no-console */
            console[method].apply(console, params);
            /* eslint-enable no-console */
        }
    };
}(jQuery));

twitter.App.queue(twitter.Log);
