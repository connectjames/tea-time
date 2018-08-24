'use strict';

(function(window, $) {
    window.Member = function($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '.js-update-level-member',
            this.handleMemberLevelUpdate.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-activate-member',
            this.handleMemberActivate.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-deactivate-member',
            this.handleMemberDeactivate.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-member',
            this.handleMemberDelete.bind(this)
        );
    };

    $.extend(window.Member.prototype, {

        handleMemberLevelUpdate: function (e) {
            e.preventDefault();

            $.ajax({
                url: $(e.currentTarget).data('url') + "&level=" + $(e.currentTarget).val(),
                method: 'GET',
                dataType: 'html'
            }).done(function () {
            });
        },

        handleMemberActivate: function(e) {
            e.preventDefault();

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET',
                dataType: 'html'
            }).done(function () {
            });
        },

        handleMemberDeactivate: function(e) {
            e.preventDefault();

            $.ajax({
                url: $(e.currentTarget).data('url'),
                method: 'GET',
                dataType: 'html'
            }).done(function () {
            });
        },

        handleMemberDelete: function(e) {
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