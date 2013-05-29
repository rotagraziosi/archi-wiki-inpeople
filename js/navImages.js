/*jslint browser: true*/

var imgList;

var navImages = function (e) {
    'use strict';
    var dest, img, next, key, i;
    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
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
    } else {
        img = document.getElementById('imageAfficheeID');
        for (i = 0; i < imgList.length; i += 1) {
            if (imgList[i][0] === img.dataset.id) {
                key = i;
                break;
            }
        }
        switch (e.keyCode) {
        case 37:
            next = imgList[key - 1];
            break;
        case 39:
            next = imgList[key + 1];
            break;
        }
        if (next) {
            img.src = img.src.replace(img.dataset.id, next[0]).replace(img.dataset.date, next[1]);
            img.dataset.id = next.[0];
        }
    }
};

var fullscreen = function () {
    'use strict';
    var elem = document.getElementById('fullscreenWrapper');
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    }
};

var exitFullscreen = function () {
    'use strict';
    var img = document.getElementById('imageAfficheeID');
    if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
        img.src = img.src.replace('originaux', 'grand');
    } else {
        img.src = img.src.replace('grand', 'originaux');
    }
};

var initNavImages = function () {
    'use strict';
    imgList = JSON.parse(decodeURIComponent(document.getElementById('imageAfficheeID').dataset.list));
    window.addEventListener('keydown', navImages);
    document.addEventListener('mozfullscreenchange', exitFullscreen);
    document.addEventListener('webkitfullscreenchange', exitFullscreen);
    document.addEventListener('fullscreenchange', exitFullscreen);
    document.getElementById('imageAfficheeID').addEventListener('click', fullscreen);
};
window.addEventListener('load', initNavImages);
