var a1 = {};
/**
 * Блокировщик
 */
a1 = $.extend(a1, {
    block: function (settings) {
        try {
            settings = $.extend({focus: window}, settings);
            var id = $('#loading-mask');
            if (id.size() > 0) {
                return;
            }
            var left = '0px', top = '0px', height = '100%', width = '100%';
            if (typeof settings.el != 'undefined') {
                var el = typeof settings.el == 'string' ? $('//' + settings.el) : settings.el;
                height = el.height() + 'px';
                width = el.width() + 'px';
                var offset = el.offset();
                left = offset.left - $(window).scrollLeft() + 'px';
                top = offset.top - $(window).scrollTop() + 'px';
            }
            $('body').append('<div id="loading-mask" style="display:none; height:' + height + '; width:' + width + '; left:' + left + '; top:' + top + ';"><p class="loader"><img alt="Загрузка..." src="' + a1.defaults.loader + '"><br>Пожалуйста, подождите...</p></div>');
            $('#loading-mask').css({opacity: 0.0}).show().fadeTo('medium', 0.9);
        } catch (e) {

        }
        return this;
    },
    unblock: function () {
        $('#loading-mask').remove();
        return this;
    }
});
