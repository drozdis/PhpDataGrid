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
