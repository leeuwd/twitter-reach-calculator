/* global twitter */
/* global axios */

'use strict';

twitter.Wizard = (function ($) {
    let self = null;
    let module = 'twitter/wizard';
    let selectors = {
        wizard: '.wizard',
        form: '.wizard__form',
        urlInput: '.wizard__form__url-input',
        button: '.wizard__button',
        results: '.wizard__results'
    };
    let $wizard = $(selectors.wizard);
    let $form = $(selectors.form);
    let $urlInput = $(selectors.urlInput);
    let $button = $(selectors.button);
    let $results = $(selectors.results);

    return {
        HAS_RESULTS_CLASS: 'wizard--has-results',
        HAS_ERROR_CLASS: 'wizard--has-error',

        /**
         * Initialization.
         */
        init: function () {
            self = this;

            self.initAjax();
            self.initMessages();
        },

        /**
         * Initialize form submission. I
         * use the pubsub pattern here.
         */
        initAjax: function () {
            self = this;

            // Request-response to backend
            $.subscribe('wizard/form/submit', function () {
                $.when(self.xmlHttpRequest($form.attr('action'), {tweet: $urlInput.val()}))
                    .always(function () {
                        self.toggleButton(true);
                    })
                    .then(function (response) {
                        $.publish('twitter/log/debug', [module, 'request completed', response]);
                        $.publish('wizard/ajax/done', response);
                    })
                    .catch(function (error) {
                        $.publish('twitter/log/error', [module, 'request error', error.response.data.error]);
                        $.publish('wizard/ajax/error', {message: error.response.data.error});
                    });
            });

            // Publish event when form is submitted
            $form.submit(function (e) {
                e.preventDefault();

                // Reset form to initial state
                self.resetState();

                // Submit...
                $.publish('wizard/form/submit');
            });
        },

        /**
         * Init showing results and error messages.
         */
        initMessages: function () {
            // Handle errors
            $.subscribe('wizard/ajax/error', function (e, error) {
                self.setError(error.message);
            });
        },

        /**
         * Set error message.
         *
         * @param {string} message
         */
        setError: function (message) {
            $results
                .text(message)
                .attr('aria-hidden', false);

            // Slides out results panel
            $wizard.addClass(self.HAS_RESULTS_CLASS)
                .addClass(self.HAS_ERROR_CLASS);
        },

        /**
         * Return to initial wizard state.
         */
        resetState: function () {
            // Remove variant classes on base
            $wizard.removeClass(self.HAS_RESULTS_CLASS)
                .removeClass(self.HAS_ERROR_CLASS);

            // Results are hidden
            $results.attr('aria-hidden', true);

            // Re-enable submit button
            self.toggleButton(true);
        },

        /**
         * Toggle submit button state.
         *
         * @param {boolean} enabled
         */
        toggleButton: function (enabled) {
            $button
                .toggleClass('disabled', !enabled)
                .prop('disabled', !enabled);
        },

        /**
         * XMLHttpRequest.
         *
         * @param {string} url
         * @param {Object} payload
         * @returns {Promise<string>}
         */
        xmlHttpRequest: function (url, payload) {
            // Axios implements Promise interface and also
            // takes care of the XSRF token and cookies
            return window.axios.get(url, {
                params: payload,
                responseType: 'json'
            });
        }
    };
}(jQuery));

twitter.App.queue(twitter.Wizard);
