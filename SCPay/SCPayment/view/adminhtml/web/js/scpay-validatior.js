require(["jquery", "mage/translate", "jquery/validate"], function ($) {
    "use strict";
    $.extend(true, $, {
        mage: {
            /**
             * Check if string is empty with trim
             * @param {string} value
             */
            isEmpty: function (value) {
                return (
                    value === "" ||
                    value === undefined ||
                    value == null ||
                    value.length === 0 ||
                    /^\s+$/.test(value)
                );
            },
            /**
             * Checks if {value} is between numbers {from} and {to}
             * @param {string} value
             * @param {string} from
             * @param {string} to
             * @returns {boolean}
             */
            isBetween: function (value, from, to) {
                return (
                    ($.mage.isEmpty(from) ||
                        value >= $.mage.parseNumber(from)) &&
                    ($.mage.isEmpty(to) || value <= $.mage.parseNumber(to))
                );
            },
            /**
             * Check if string is empty no trim
             * @param {string} value
             */
            isEmptyNoTrim: function (value) {
                return value === "" || value == null || value.length === 0;
            },
        },
    });

    var isEnabledPlgin = false;

    var rules = {
        "scpay-enable-plugin": [
            function (value) {
                isEnabledPlgin = parseNumber(value);
                return !$.mage.isEmpty(value);
            },
        ],
        "scpay-required-entry": [
            function (value) {
                if (!isEnabledPlgin) return true;

                return !$.mage.isEmpty(value);
            },
            $.mage.__("This is a required field."),
        ],
        "scpay-validate-digits": [
            function (v) {
                if (!isEnabledPlgin) return true;

                return $.mage.isEmptyNoTrim(v) || !/[^\d]/.test(v);
            },
            $.mage.__("Please enter a valid number in this field."),
        ],
    };

    $.each(rules, function (i, rule) {
        rule.unshift(i);
        $.validator.addMethod.apply($.validator, rule);
    });
});
