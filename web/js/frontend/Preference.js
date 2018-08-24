'use strict';

(function(window, $) {
    window.Preference = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-create-preference',
            this.handlePreferenceCreate.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-preference',
            this.handlePreferenceDelete.bind(this)
        );
    };

    $.extend(window.Preference.prototype, {

        handlePreferenceCreate: function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget).closest('tr');

            $.ajax({
                url: $(e.currentTarget).data('url') + "?newPreference=" + $el.find('input').val(),
                method: 'GET',
                dataType: 'html'
            }).done(function(data) {
                var place = 'div#preference';

                var result = $(data);

                $(place).html(result);
            });
        },

        handlePreferenceDelete: function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget).closest('tr');

            $(e.currentTarget).children('i').removeClass('fa-trash')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET',
                dataType: 'html'
            }).done(function () {
                $el.fadeOut();
            })
        }
    });
})(window, jQuery);