/*global $*/
/*jslint browser: true */
$(document).ready(function () {
    'use strict';
    $('#info_amounts').addClass('event');
    $('#info_amounts').html($('.membership span[title] input:checked').parent().attr('title'));
    $('.membership span[title] input').change(function() {
        if (this.checked) {
            $('#info_amounts').html($(this).parent().attr('title'));
        }
    });
});
