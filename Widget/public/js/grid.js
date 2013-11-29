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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
(function ($) {
    $.fn.enableCheckboxRangeSelection = function () {
        var lastCheckbox = null;
        var $spec = this;
        $spec.unbind("click.checkboxrange");
        $spec.bind("click.checkboxrange", function (e) {
            if (lastCheckbox != null && (e.shiftKey || e.metaKey)) {
                $spec.slice(
                    Math.min($spec.index(lastCheckbox), $spec.index(e.target)),
                    Math.max($spec.index(lastCheckbox), $spec.index(e.target)) + 1
                ).attr({checked: e.target.checked ? "checked" : ""});
            }
            lastCheckbox = e.target;
        });
    };

})(jQuery);

$(document).ajaxComplete(function (event, request, settings) {
    try {
        var json = jQuery.parseJSON(request.responseText);
        if (jQuery.isPlainObject(json.errors) && !jQuery.isEmptyObject(json.errors) || json.errors && typeof json.errors == 'string') {
            if (json.success == false) {
                a1.message.error(json.errors);
            }
            a1.unblock();
        }
    } catch (e) {
    }
});

var Widget = {};

Widget.GridDefaults = {
    url: document.location.href,
    baseUrl: document.location.href,
    autoLoad: false,
    uriDelimeter: '/',
    replaceUrl: true,
    onBeforeLoad: function (grid, url) {
    },
    onLoad: function (grid, url) {
    },
    onFilter: function (grid, filters) {
    }
}

/**
 * @var Object config = {url, baseUrl, autoLoad, uriDelimeter, replaceUrl}
 */
Widget.Grid = function (config) {
    $.extend(this, Widget.GridDefaults, config);

    var self = this;
    this.init = function () {
        if (this.autoLoad) {
            $(function () {
                self.load();
            });
        }
        this.initDelete();
        this.initSelection();

        $(function () {
            self.initSort();
        });


        //сохраняем объект в data
        $('//' + self.id).data('grid', self);
    };

    this.load = function (url, params) {
        url = url || this.url;

        //event before load
        if (this.onBeforeLoad(this, url) === false) {
            return false;
        }

        a1.block();
        $.ajax({
            url: url,
            type: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                if (window.history.replaceState && self.replaceUrl === true && url != document.location.pathname) {
                    window.history.replaceState({}, window.title, url);
                }

                if (response.success == true) {
                    $('//' + self.id).parent().replaceWith(response.grid);

                    //сохраняем объект в data
                    $('//' + self.id).data('grid', self);
                }
                a1.unblock();

                self.url = url;

                //event load
                self.onLoad(self, url);

                self.initSelection();
                self.initDelete();
                self.initSort();
            },
            error: function () {
                a1.unblock();
            }
        });
    };

    this.replaceUrlPart = function (part, value) {
        var url = this.url;
        if (part == 'filter') {
            url = url.replace(/(\/|\&|\\?)filter(\/|\=)[\d\w\=]+(\/|&)?/, '$1');
        } else if (part == 'baseparams') {
            url = url.replace(/(\/|\&|\\?)params(\/|\=)[\d\w\=]+(\/|&)?/, '$1');
        } else if (part == 'order') {
            url = url.replace(/(\/|\&|\\?)order(\/|\=)[\d\w\=]+(\/|&)?/, '$1');
        }

        if (this.uriDelimeter == '/') {
            var arr = url.split('?');
            var url = arr[0].replace(/\/$/, '') + (value ? '/' + part + '/' + value : '') + (arr[1] ? '?' + arr[1] : '');
        } else {
            var url = url.replace(/\&$/, '') + (value ? (this.url.indexOf('?') == -1 ? '?' : '&') + part + '=' + value : '');
        }
        return url;
    };

    this.getFilterUrl = function () {
        var filters = this.getFilters();
        return this.replaceUrlPart('filter', !jQuery.isEmptyObject(filters) ? Request.encode(filters) : '');
    };

    this.getFilters = function () {
        return json.form($('//' + this.id + ' .filter'));
    };

    this.doFilter = function () {
        //event filter
        if (this.onFilter(this, this.getFilters()) === false) {
            return false;

        }
        //load
        this.load(this.getFilterUrl());
    };

    this.doFilterEnter = function (e) {
        if (e.which == 13) {
            this.doFilter();
        }
    };

    this.checkAll = function (name, input) {
        //ставим флаг
        $("input[name='" + name + "'][type='checkbox']").attr('checked', input.checked);

        var selected = $('input[name="selected[]"]:checked').size();
        var count = $(input).val();

        //к-во выбранных
        $('[data-role="selected"]').text(selected);

        $('[data-role="selection-message"]').remove();
        $('[data-role="check-all"]').data('all', 0);
        $("input[name='" + name + "'][type='checkbox']").click(function () {
            $('[data-role="selection-message"]').remove();
            $('[data-role="check-all"]').data('all', 0);
        });

        if (selected > 0 && count > selected) {
            var message = $('<tr data-role="selection-message" class="info"><td colspan="1000"><div><span>Все <b>' + selected + '</b> записей на этой странице выбрано.</span> <a href="javascript:void(0)" role="link">Выбрать все <b>' + count + '</b> записи</a></span></div></td></tr>')
            message.find('a').click(function () {
                message.find('td').html('Выбрано все <b>' + count + '</b> записи');
                message.removeClass('info').addClass('success');
                $('[data-role="check-all"]').data('all', 1);
            });

            message.insertAfter('tr.headings');
        }
    };

    this.getSelected = function () {
        var isAll = $('[data-role="check-all"]').data('all');

        if (isAll) {
            var selected = true;
        } else {
            var selected = [];
            $('input[name="selected[]"]:checked').each(function () {
                selected.push($(this).val());
            });
        }
        return selected;
    };


    this.getData = function (name, item_id) {
        var grid = $('//' + this.id);
        var col = grid.find('tr.headings').find('th[data-name="' + name + '"]').index();
        var row = grid.find('tr[data-identifier="' + item_id + '"]').index();
        if (col != -1 && row != -1) {
            var td = grid.find('tr:eq(' + (row) + ')').find('td:eq(' + col + ')');
            if (td.find('input').size() > 0) {
                return td.find('input').val();
            } else {
                return td.text();
            }
        } else {
            return false;
        }
    };

    this.getRows = function () {
        var grid = $('//' + this.id), self = this;
        var result = [];
        grid.find('tr').each(function () {
            var id = $(this).attr('data-identifier');
            if (id) {
                result.push(self.getRow(id));
            }
        });
        return result;
    };

    this.getRow = function (item_id) {
        var grid = $('//' + this.id);

        var result = {};
        grid.find('tr.headings').find('th').each(function (col, th) {
            var row = grid.find('tr[data-identifier="' + item_id + '"]').index();
            if (col != -1 && row != -1 && $(this).attr('data-name')) {
                var td = grid.find('tr:eq(' + (row) + ')').find('td:eq(' + col + ')');
                if (td.find('input').size() > 0) {
                    result[$(this).attr('data-name')] = td.find('input').val();
                } else {
                    result[$(this).attr('data-name')] = td.text();
                }
            }
        });
        return result;
    };

    this.apply = function (select) {
        var action = $('select[name="' + select + '_action"] option:selected'),
            selected = this.getSelected();

        if (selected) {
            if (action.size() > 0 && action.attr('value')) {
                var data = action.data('json');

                var question = data.question || "Уверены, что хотите выполнить действие \"" + action.text() + "\"?",
                    success = data.success || 'Действие "' + action.text() + '" успешно выполнено',
                    errors = data.errors || 'Невозможно выполнить действие "' + action.text() + '"',
                    handler = data.handler,
                    iframe = data.iframe || false,
                    url = data['href'] ? data['href'] : this.baseUrl + action.attr('value'),
                    filters = this.getFilters();

                if (selected === true) {
                    selected = {all: true, filters: filters};
                }

                if (question == 'no' || (question != 'no' && confirm(question))) {
                    if (handler) {
                        eval(handler);
                    } else {
                        a1.block();
                        if (iframe) {
                            var iframe = $('<iframe id="iframe" src="' + url + '/key/' + Request.encode({selected: selected}) + '" width="1px" height="1px" frameborder="0"></iframe>');
                            iframe.ready(function () {
                                a1.unblock();
                            });
                            $(document.body).append(iframe);
                        } else {
                            $.ajax({
                                url: url,
                                data: {key: Request.encode({selected: selected})},
                                type: 'POST',
                                dataType: 'json',
                                success: function (response) {
                                    a1.unblock();
                                    if (response.success) {
                                        a1.message.success(success);
                                        self.load();
                                    } else {
                                        a1.message.error(errors);
                                    }
                                },
                                error: function () {
                                    a1.unblock();
                                }
                            });
                        }
                    }
                }
            } else {
                a1.message.error('Нет выбранных действий');
            }
        } else {
            a1.message.error('Нет выбранных элементов');
        }
    };

    this.initSelection = function () {
        $('input[name="selected[]"]').enableCheckboxRangeSelection();
        $('input[name="selected[]"]').change(function () {
            $('[data-role="selected"]').text($('input[name="selected[]"]:checked').size());
        });
    }

    this.initDelete = function () {
        var grid = $('//' + this.id);
        grid.find('.icon-delete').parent().click(function () {
            return confirm('Вы уверены, что хотите удалить запись?');
        });
    }

    this.initSort = function () {
        if (!$.sortable) {
            return false;
        }
        var gridCt = $('//' + this.id);
        var originalPosition;

        gridCt.find("tbody").sortable({
            handle: '.sorting_handle',
            cursor: 'move',
            helper: function (e, tr) {
                // хелпер для фиксации ширины строки таблицы при переносе, иначе она "схлопывается"
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },
            start: function (e, ui) {
                originalPosition = ui.item.index();
            },
            stop: function (e, ui) {
                if (ui.item.index() == originalPosition) {
                    return;
                }
                var grid = gridCt.data('grid'),
                    id = ui.item.data('identifier'),
                    $handle = ui.item.find('.sorting_handle'),
                    sort_key = $handle.data('colname'),
                    model = $handle.data('model'),
                    parent_key = $handle.data('parentcol');

                // определяем позицию сортировки и родителя
                var $nextRow = ui.item.next(),
                    $pos_handle,
                    sort_val,
                    parent_id;

                if ($nextRow.length) {
                    $pos_handle = $nextRow.find('.sorting_handle');
                    sort_val = $pos_handle.data('value');
                    parent_id = $pos_handle.data('parent');
                } else {
                    $pos_handle = ui.item.prev().find('.sorting_handle');
                    sort_val = $pos_handle.data('value') - 1;
                    parent_id = $pos_handle.data('parent');
                }
                $.post("/core/sort/", {id: id, key: sort_key, model: model, value: sort_val, parent_key: parent_key, parent_id: parent_id}, function () {
                    grid.load();
                });
            }
        });
    }

    //инициализация
    this.init();
}
var json = {
    /**
     * json.form($(form));
     */
    formFields: function (elements) {
        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push": /^$/,
                "fixed": /^\d+$/,
                "named": /^[a-zA-Z0-9_]+$/
            };

        this.build = function (base, key, value) {
            base[key] = value;
            return base;
        };

        this.push_counter = function (key) {
            if (push_counters[key] === undefined) {
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        elements.each(function () {
            var name = $(this).attr('name');
            if (!name) {
                return;
            }
            // skip invalid keys
            if (!patterns.validate.test(name)) {
                return;
            }

            var k,
                keys = name.match(patterns.key),
                merge = $(this).val(),
                reverse_key = name;

            if (merge !== '') {
                while ((k = keys.pop()) !== undefined) {
                    if (k == undefined) {
                        continue;
                    }
                    // adjust reverse_key
                    reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                    // push
                    if (k.match(patterns.push)) {
                        merge = self.build([], self.push_counter(reverse_key), merge);
                    }
                    // fixed
                    else if (k.match(patterns.fixed)) {
                        merge = self.build([], k, merge);
                    }
                    // named
                    else if (k.match(patterns.named)) {
                        merge = self.build({}, k, merge);
                    }
                }

                json = $.extend(true, json, merge);
            }
        });
        return json;
    },

    /**
     * Сериализация формы в JSON
     */
    form: function (form) {
        return json.formFields($(form).find("input[type=\"text\"], input[type=\"hidden\"], select:enabled, textarea:enabled, input:checked"));
    }
};

var Request = {};

/**
 * @param Array params
 */
Request.encode = function (params) {
    var arr = {};
    $.each(params, function (key, value) {
        if (typeof value == 'string') {
            arr[encodeURIComponent(key)] = encodeURIComponent(value);
        } else {
            arr[encodeURIComponent(key)] = value;
        }
    });
    return Request.encode_base64($.toJSON(arr));
};

/**
 * @param String what
 */
Request.encode_base64 = function (what) {
    var base64_encodetable = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var result = "";
    var len = what.length;
    var x, y;
    var ptr = 0;

    while (len-- > 0) {
        x = what.charCodeAt(ptr++);
        result += base64_encodetable.charAt(( x >> 2 ) & 63);

        if (len-- <= 0) {
            result += base64_encodetable.charAt(( x << 4 ) & 63);
            result += "==";
            break;
        }

        y = what.charCodeAt(ptr++);
        result += base64_encodetable.charAt(( ( x << 4 ) | ( ( y >> 4 ) & 15 ) ) & 63);

        if (len-- <= 0) {
            result += base64_encodetable.charAt(( y << 2 ) & 63);
            result += "=";
            break;
        }

        x = what.charCodeAt(ptr++);
        result += base64_encodetable.charAt(( ( y << 2 ) | ( ( x >> 6 ) & 3 ) ) & 63);
        result += base64_encodetable.charAt(x & 63);

    }

    return result;
};

/**
 * @param String what
 */
Request.decode_base64 = function (what) {
    var base64_decodetable = new Array(
        255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255,
        255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255,
        255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 62, 255, 255, 255, 63,
        52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 255, 255, 255, 255, 255, 255,
        255, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
        15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 255, 255, 255, 255, 255,
        255, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
        41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 255, 255, 255, 255, 255
    );
    var result = "";
    var len = what.length;
    var x, y;
    var ptr = 0;

    while (!isNaN(x = what.charCodeAt(ptr++))) {
        if (x == 13 || x == 10)
            continue;

        if (( x > 127 ) || (( x = base64_decodetable[x] ) == 255))
            return false;
        if (( isNaN(y = what.charCodeAt(ptr++)) ) || (( y = base64_decodetable[y] ) == 255))
            return false;

        result += String.fromCharCode((x << 2) | (y >> 4));

        if ((x = what.charCodeAt(ptr++)) == 61) {
            if ((what.charCodeAt(ptr++) != 61) || (!isNaN(what.charCodeAt(ptr)) ))
                return false;
        }
        else {
            if (( x > 127 ) || (( x = base64_decodetable[x] ) == 255))
                return false;
            result += String.fromCharCode((y << 4) | (x >> 2));
            if ((y = what.charCodeAt(ptr++)) == 61) {
                if (!isNaN(what.charCodeAt(ptr)))
                    return false;
            }
            else {
                if ((y > 127) || ((y = base64_decodetable[y]) == 255))
                    return false;
                result += String.fromCharCode((x << 6) | y);
            }
        }
    }
    return result;
};