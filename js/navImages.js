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

var fullscreen = function () {
    'use strict';
    var img = document.getElementById('imageAfficheeID');
    img.src = img.src.replace('grand', 'originaux');
    if (img.requestFullscreen) {
        img.requestFullscreen();
    } else if (img.mozRequestFullScreen) {
        img.mozRequestFullScreen();
    } else if (img.webkitRequestFullscreen) {
        img.webkitRequestFullscreen();
    }
};

var exitFullscreen = function () {
    'use strict';
    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
        var img = document.getElementById('imageAfficheeID');
        img.src = img.src.replace('originaux', 'grand');
    }
};

var initNavImages = function () {
    'use strict';
    window.addEventListener('keydown', navImages);
    document.addEventListener('mozfullscreenchange', exitFullscreen);
    document.addEventListener('webkitfullscreenchange', exitFullscreen);
    document.addEventListener('fullscreenchange', exitFullscreen);
    document.getElementById('imageAfficheeID').addEventListener('click', fullscreen);
};
window.addEventListener('load', initNavImages);
