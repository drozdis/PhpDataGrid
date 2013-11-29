var a1 = {
    timers: [],

    defaults: {
        loader: '/res/css/img/ajax-loader-tr.gif'
    },

    checkAll: function (name, flag) {
        $("input[name='" + name + "'][type='checkbox']").attr('checked', flag);
    },

    // redirect
    redirect: function (href, options) {
        var o = options;
        if (o && (typeof o.name == 'string')) {
            var expires, value = '';
            if (o.value) {
                value = o.value;
            }
            if (typeof o.days == 'number') {
                var date = new Date();
                date.setTime(date.getTime() + (1000 * 60 * 60 * 24 * o.days));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = o.name + '=' + value + expires + '; path=/';
        }

        if (!href || (typeof href == 'undefined')) {
            href = document.location.pathname;
        }
        document.location.href = href;
    },

    // show hint in input
    showHint: function (input, value) {
        if (input.value == '') input.value = value;
    },

    // hide hint in input
    hideHint: function (input, value) {
        if (input.value == value) input.value = '';
    },

    /**
     * Случайное число
     */
    randomNumber: function (m, n) {
        m = parseInt(m);
        n = parseInt(n);
        return Math.floor(Math.random() * (n - m + 1)) + m;
    },

    /**
     * Выполнение функций в очереди
     */
    timeout: function (id, func, timeout) {
        if (a1.timers[id]) clearTimeout(a1.timers[id]);
        a1.timers[id] = setTimeout(function () {
            func.call();
        }, timeout || 800);
    },

    /**
     * Заполение элементов списка
     */
    select: function (selector, options, value) {
        $(selector).find('option').remove();
        for (i in options) {
            $(selector).append('<option value="' + i + '">' + options[i] + '</option>');
        }
        $(selector).val(value);
    },

    // make img grayscale
    grayscaleImage: function (imgObj) {
        var canvas = document.createElement('canvas');
        var canvasContext = canvas.getContext('2d');

        var imgW = imgObj.width;
        var imgH = imgObj.height;
        canvas.width = imgW;
        canvas.height = imgH;

        canvasContext.drawImage(imgObj, 0, 0);
        var imgPixels = canvasContext.getImageData(0, 0, imgW, imgH);

        for (var y = 0; y < imgPixels.height; y++) {
            for (var x = 0; x < imgPixels.width; x++) {
                var i = (y * 4) * imgPixels.width + x * 4;
                var avg = (imgPixels.data[i] + imgPixels.data[i + 1] + imgPixels.data[i + 2]) / 3;
                imgPixels.data[i] = avg;
                imgPixels.data[i + 1] = avg;
                imgPixels.data[i + 2] = avg;
            }
        }

        canvasContext.putImageData(imgPixels, 0, 0, 0, 0, imgPixels.width, imgPixels.height);
        return canvas.toDataURL();
    },

    /**
     * Центирование елемена на экране
     */
    center: function (el, position) {
        if (position == undefined || position == 'absolute') {
            var y = $(window).attr('scrollY') || 0;
        } else {
            var y = 0;
        }

        el.css("position", position || "absolute");
        el.css("left", ($(window).width() - el.width()) / 2 + "px");
        el.css("top", ($(window).height() - el.height()) / 2 + y + "px");

    },

    /**
     * Размер обьекта
     */
    sizeOf: function (obj) {
        var n = 0;
        for (var i in obj) {
            n++;
        }
        return n;
    },

    /**
     * Генерация случайного ключа
     */
    generateGuid: function () {
        var result, i, j;
        result = '';
        for (j = 0; j < 32; j++) {
            if (j == 8 || j == 12 || j == 16 || j == 20)
                result = result + '-';
            i = Math.floor(Math.random() * 16).toString(16).toUpperCase();
            result = result + i;
        }
        return result;
    }
};

/**
 * Работа с куки
 */
a1.cookie = {
    create: function (name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else {
            var expires = "";
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    },

    read: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) == 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }
};

/**
 * Отображение сообщений
 */
a1.message = {
    //success, errors, notice, warning
    success: function (message, text, timeout) {
        a1.message.show(message, text, 'success', timeout);
    },
    error: function (message, text, timeout) {
        a1.message.show(message, text, 'error', timeout);
    },
    info: function (message, text, timeout) {
        a1.message.show(message, text, 'info', timeout);
    },
    warning: function (message, text, timeout) {
        a1.message.show(message, text, 'warning', timeout);
    },
    notification: function (message, text) {
        $.jGrowl(text, {header: message});
    },
    show: function (message, text, type, timeout) {
        if (typeof toastr != 'undefined') {
            //plugin jquery
            toastr[type].call(this, message, text);
        } else {
            //свой вывод
            text = text || '';
            var message = message ? '<h4 class="alert-heading">' + message + '</h4>' : '';
            var html = $('<div id="alert" class="alert ' + (('alert-' + type) || 'alert-success') + ' fade in"><a class="close" data-dismiss="alert" href="//">×</a>' + message + text + '</div>').hide();
            $(document.body).append(html);
            var height = html.height();
            html.css('top', '-' + height + 'px');
            html.show().animate({'top': '10px'}, 250);
            setTimeout(function () {
                html.fadeOut(250, function () {
                    $(this).remove();
                });
            }, timeout || 2000);
        }
    }
};

/**
 * Попап (обычный диалог, окно оранжевое, черное - {cls : 'popup'}, {cls : 'popup-black'}, {cls : 'popup-orange'})
 * @example
 * a1.popup.show('dialog', '/order/dialog/',{modal : true, keyboard : true, onShow : 'function(){alert("show")}'});
 */
a1.popup = {
    show: function (id, url, config) {
        a1.block();
        config = config || {};
        $.ajax({
            url: url + (config.params ? '/key/' + Request.encode(config.params) : ''),
            type: 'GET',
            success: function (response) {
                a1.popup.create(id, response, config);
                if (config.onShow) {
                    config.onShow.call(window, $('//' + id));
                }
                a1.unblock();
            },
            error: function (response) {
                response = jQuery.parseJSON(response.responseText);
                a1.message.error(response.errors);
                a1.unblock();
            }
        });
    },

    create: function (id, html, config) {
        $('//' + id).remove();
        $(document.body).append('<div id="' + id + '" class="modal hide fade" style="left:50%; top:50%;">' + html + '</div>');

        $('//' + id).on('show shown resize', function () {
            $('//' + id).find('.modal-body').css({
                'max-height': ($(window).height() - 180) + 'px',
                'max-width': ($(window).width() - 200) + 'px'
            });
            $('//' + id).css('margin-left', function () {
                return -($(this).width() / 2) + 'px';
            });
            $('//' + id).css('margin-top', function () {
                return -($(this).height() / 2) + 'px';
            });
        });

        $('//' + id).modal({backdrop: config.modal || false, keyboard: config.keyboard || true});

        $('//' + id).on('hidden', function () {
            ///$('//'+id).remove(); баг в ботстрапе, генерируеться событие, если в попапе есть толтип
        });
    },

    win: function (url, name, attrs) {
        if (!attrs) attrs = "height=550,width=1000,status=no,left=100,top=1,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no";
        window.open(url, name, attrs);
    },

    close: function (id) {
        $('//' + id).modal('hide');
    }
};


/**
 * Блокировщик
 */
a1 = $.extend(a1, {
    block: function (settings) {
        try {
            settings = $.extend({focus: window}, settings);
            var id = $('//loading-mask');
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
            $('//loading-mask').css({opacity: 0.0}).show().fadeTo('medium', 0.9);
        } catch (e) {

        }
        return this;
    },
    unblock: function () {
        $('//loading-mask').remove();
        return this;
    }
});


/**
 * Отправка форм ajax
 */
a1 = $.extend(a1, {
    formPost: function (form, options) {
        if (form.validateForm()) {
            a1.block();

            $form = $(form);
            options = $.extend({}, options);
            $.post(
                options.action || $form.attr('action'),
                $form.serialize(),
                function (json) {
                    a1.unblock();

                    if (!json.success) {
                        a1.message.error(json.error || 'Ошибка сохранения данных');
                        if (options.callbackError) {
                            options.callbackError(json);
                        }
                        return false;
                    }
                    if (options.callback) {
                        options.callback(json);
                    }
                },
                'json'
            );
        }
    }
});

/**
 * Таймер
 */
a1.timer = function (difference, id, callback, hard) {
    var cookie = a1.cookie.read('difference');
    var timer = null;
    if (parseInt(cookie) && !hard) {
        this.difference = cookie;
    } else {
        this.difference = difference;
    }

    this.countainer = document.getElementById(id);
    this.callback = callback;

    this.addLeadingZero = function (value) {
        return value < 10 ? ("0" + value) : value;
    }

    this.stop = function () {
        clearTimeout(timer);
    }

    this.updateCounter = function () {
        var difference = this.difference;
        this.hours = 0;
        this.minutes = 0;
        this.seconds = 0;

        this.hours = Math.floor(difference / 3600);//hours
        difference = difference % 3600;

        this.minutes = Math.floor(difference / 60);//minutes
        difference = difference % 60;

        this.seconds = Math.floor(difference);//seconds

        //save cookie
        a1.cookie.create('difference', this.difference);

        //callback
        if (this.callback && this.callback.call && this.difference == 0) {
            this.callback.call();
        }

        //html
        this.countainer.innerHTML =
            " <strong>" + this.hours + "</strong> <small>час</small>" +
                " <strong>" + this.addLeadingZero(this.minutes) + "</strong> <small>мин</small>" +
                " <strong>" + this.addLeadingZero(this.seconds) + "</strong> <small>сек</small>"

        if (this.difference > 0) {
            var self = this;
            timer = setTimeout(function () {
                self.updateCounter();
            }, 1000);
        }

        this.difference = this.difference - 1;
    }

    this.updateCounter();
};