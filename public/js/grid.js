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


        //????????? ?????? ? data
        $('#' + self.id).data('grid', self);
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
                    $('#' + self.id).parent().replaceWith(response.grid);

                    //????????? ?????? ? data
                    $('#' + self.id).data('grid', self);
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
        return json.form($('#' + this.id + ' .filter'));
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
        //?????? ????
        $("input[name='" + name + "'][type='checkbox']").attr('checked', input.checked);

        var selected = $('input[name="selected[]"]:checked').size();
        var count = $(input).val();

        //?-?? ?????????
        $('[data-role="selected"]').text(selected);

        $('[data-role="selection-message"]').remove();
        $('[data-role="check-all"]').data('all', 0);
        $("input[name='" + name + "'][type='checkbox']").click(function () {
            $('[data-role="selection-message"]').remove();
            $('[data-role="check-all"]').data('all', 0);
        });

        if (selected > 0 && count > selected) {
            var message = $('<tr data-role="selection-message" class="info"><td colspan="1000"><div><span>??? <b>' + selected + '</b> ??????? ?? ???? ???????? ???????.</span> <a href="javascript:void(0)" role="link">??????? ??? <b>' + count + '</b> ??????</a></span></div></td></tr>')
            message.find('a').click(function () {
                message.find('td').html('??????? ??? <b>' + count + '</b> ??????');
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
        var grid = $('#' + this.id);
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
        var grid = $('#' + this.id), self = this;
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
        var grid = $('#' + this.id);

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

                var question = data.question || "???????, ??? ?????? ????????? ???????? \"" + action.text() + "\"?",
                    success = data.success || '???????? "' + action.text() + '" ??????? ?????????',
                    errors = data.errors || '?????????? ????????? ???????? "' + action.text() + '"',
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
                a1.message.error('??? ????????? ????????');
            }
        } else {
            a1.message.error('??? ????????? ?????????');
        }
    };

    this.initSelection = function () {
        $('input[name="selected[]"]').enableCheckboxRangeSelection();
        $('input[name="selected[]"]').change(function () {
            $('[data-role="selected"]').text($('input[name="selected[]"]:checked').size());
        });
    }

    this.initDelete = function () {
        var grid = $('#' + this.id);
        grid.find('.icon-delete').parent().click(function () {
            return confirm('?? ???????, ??? ?????? ??????? ???????');
        });
    }

    this.initSort = function () {
        if (!$.sortable) {
            return false;
        }
        var gridCt = $('#' + this.id);
        var originalPosition;

        gridCt.find("tbody").sortable({
            handle: '.sorting_handle',
            cursor: 'move',
            helper: function (e, tr) {
                // ?????? ??? ???????? ?????? ?????? ??????? ??? ????????, ????? ??? "????????????"
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

                // ?????????? ??????? ?????????? ? ????????
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

    //?????????????
    this.init();
}