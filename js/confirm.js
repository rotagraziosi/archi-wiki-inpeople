var initConfirm = function () {
    "use strict";
    var noconfirm, goodbye;
    noconfirm = function () {
        window.onbeforeunload = null;
    };
    goodbye = function (e) {
        if (!e) {
            e = window.event;
        }

        if (e.stopPropagation) {
            e.stopPropagation();
            e.preventDefault();
        }
    };
    window.onbeforeunload = goodbye;
    document.getElementById("submitBtn").addEventListener("click", noconfirm, true);
};
window.addEventListener("load", initConfirm);
