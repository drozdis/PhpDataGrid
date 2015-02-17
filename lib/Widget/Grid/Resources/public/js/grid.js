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

                self.initDelete();
            },
            error: function () {
                a1.unblock();
            }
        });
    };

    this.replaceUrlPart = function (url, part, value) {
        url = url.replace(new RegExp('(\\?|\\&)?' + part + '\=[\\d\\w\\=]+', 'i'), '');
        url = url + (url.indexOf('?') == -1 ? '?' : '&') + part + '=' + value;

        return url;
    };

    this.getFilterUrl = function () {
        var filters = this.getFilters();
        var url = this.replaceUrlPart(this.url, 'filter', !jQuery.isEmptyObject(filters) ? Request.encode(filters) : '');
            url = this.replaceUrlPart(url, 'page', 1);

        return url;
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

    this.checkAll = function (input) {
        $('#' + self.id).find("[data-role=\"selection\"]").attr('checked', $(input).prop('checked'));
    };

    this.getSelected = function () {
        var selected = [];
        $('#' + self.id).find("[data-role=\"selection\"]:checked").each(function () {
            selected.push($(this).val());
        });
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

    this.initDelete = function () {
        var grid = $('#' + this.id);
        grid.find('.icon-delete').parent().click(function () {
            return confirm('?? ???????, ??? ?????? ??????? ???????');
        });
    }

    this.init();
}