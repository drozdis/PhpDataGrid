var UrlHelper = function() {
    return {
        parse: function(url) {
            var chunks = url.split('?');
            if (chunks[1]) {
                chunks = chunks[1].split('&');
            }

            var result = [];
            $.each(chunks, function(i, chunk) {
                var value = chunk.split('=');
                result[value[0]] = value[1];
            });

            return result;
        },

        gather: function(chunks) {
            var result = [];
            $.each(chunks, function(i, chunk) {
                result.push(i+'='+chunk);
            });

            return result.join('&');
        }
    }
}();

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

    },
    block: function() {

    },
    unblock: function() {

    }
}

/**
 * @var Object config = {url, baseUrl, autoLoad, uriDelimeter, replaceUrl}
 */
Widget.Grid = function (config) {
    $.extend(this, Widget.GridDefaults, config);

    var self = this;
    var chunks = UrlHelper.parse(self.url);

    this.init = function () {
        if (this.autoLoad) {
            $(function () {
                self.load();
            });
        }
        $('#' + self.id).data('grid', self);
    };

    this.load = function (url, params) {
        url = url || this.url;

        //event before load
        if (this.onBeforeLoad(this, url) === false) {
            return false;
        }

        self.block();
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
                    $('#' + self.id).data('grid', self);
                }
                self.unblock();

                self.url = url;

                //event load
                self.onLoad(self, url);
            },
            error: function () {
                self.unblock();
            }
        });
    };

    this.getFilterUrl = function () {
        var filters = this.getFilters();
        var parts = jQuery.extend({}, chunks);

        parts['filter'] = !jQuery.isEmptyObject(filters) ? Request.encode(filters) : '';
        parts['page'] = 1;

        return this.baseUrl+'?'+UrlHelper.gather(parts);
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
        if ($.uniform) {
            $.uniform.update($('#' + self.id).find("[data-role=\"selection\"]"));
        }
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

    this.init();
}