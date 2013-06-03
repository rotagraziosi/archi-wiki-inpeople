/*jslint browser: true*/
var initLogin = function () {
    "use strict";
    var login, connect;
    login = function (assertion) {
        var query;
        if (assertion) {
            if (window.location.search.indexOf('?') === -1) {
                query = "?";
            } else {
                query = "&";
            }
            document.location = window.location.search + query + "archiAction=validAuthentification&archiActionPrecedente=&assertion=" + assertion;
        }
    };
    connect = function (e) {
        e.preventDefault();
        navigator.id.get(login);
    };
    document.getElementById("browserid").addEventListener("click", connect, true);
    if (document.getElementById("browserid2")) {
        document.getElementById("browserid2").addEventListener("click", connect, true);
    }
};
if (window.addEventListener) {
    window.addEventListener("load", initLogin, false);
}
