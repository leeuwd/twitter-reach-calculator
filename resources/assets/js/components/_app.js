'use strict';
window.twitter = {};

twitter.App = (function ($) {
    let self = null;
    let module = 'twitter/app';
    let queue = [];
    let selectors = {body: 'body'};
    let $body = $(selectors.body);
    let pubSub = $({});

    return {
        HIBERNATE_BODY_CLASS: 'js--waiting',
        BOOTED_BODY_CLASS: 'js--booted',
        booted: false,

        /**
         * Initializes the application. This will initialize
         * the bootstrap process, which will execute asynchronously.
         */
        init: function () {
            self = this;

            // Boot
            $(self.bootstrap);
        },

        /**
         * Bootstraps the application.
         */
        bootstrap: function () {
            // Init pub sub
            $.each({
                trigger: 'publish',
                on: 'subscribe',
                off: 'unsubscribe'
            }, function (key, val) {
                jQuery[val] = function () {
                    // If we're not publishing log events, log the pubsub call
                    if (arguments[0].indexOf('twitter/log') === -1) {
                        pubSub.trigger('twitter/log/debug', ['$', val, arguments[0], arguments]);
                    }

                    pubSub[key].apply(pubSub, arguments);
                };
            });

            // Sort boot queue
            queue.sort(function sortFunction(a, b) {
                return (a[1] === b[1]) ? 0 : ((a[1] < b[1]) ? -1 : 1);
            });

            // Run boot queue
            for (let i in queue) {
                if (queue.hasOwnProperty(i)) {
                    // queue[i][0] contains module object, queue[i][2] optional parameters
                    queue[i][0].init.apply(queue[i][0], queue[i][2]);
                }
            }

            // Boot complete
            self.booted = true;
            $.publish('twitter/log/info', [module, 'boot completed']);
            $.publish(module + '/booted');

            // Body class
            $body
                .addClass(self.BOOTED_BODY_CLASS)
                .removeClass(self.HIBERNATE_BODY_CLASS);
        },

        /**
         * Add objects to boot sequence. Object should have an init()
         * function which will be called (with the object itself as the
         * this object) on boot.
         *
         * @param {object} object -
         * @param {number} [priority] - Optional priority 1 or higher (10 by default)
         * @param {array} [parameters] - Optional array of parameters
         */
        queue: function (object, priority, parameters) {
            queue.push([object, priority || 10, parameters]);
        }
    };

}(jQuery, document));
