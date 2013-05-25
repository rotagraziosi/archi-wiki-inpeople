/*jslint browser: true*/
var navImages = function (e) {
    'use strict';
    var dest;
    switch (e.keyCode) {
    case 37:
        dest = document.getElementById('prevPic');
        break;
    case 39:
        dest = document.getElementById('nextPic');
        break;
    }
    if (dest) {
        window.location = dest.getAttribute('href');
    }
};
var initNavImages = function () {
    'use strict';
    window.addEventListener('keydown', navImages);
};
window.addEventListener('load', initNavImages);
