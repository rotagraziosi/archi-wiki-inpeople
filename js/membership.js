/*global $*/
/*jslint browser: true */
$(document).ready(function () {
    'use strict';
    $('#info_amounts').addClass('event');
    $('#info_amounts').html("Cliquez sur un montant pour obtenir plus d'informations.");
    $('.membership span[title] input').change(function() {
        if (this.checked) {
            $('#info_amounts').html($(this).parent().attr('title'));
        }
    });
});
