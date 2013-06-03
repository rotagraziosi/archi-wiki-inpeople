/*jslint browser: true*/

var initNavImages = function () {
    'use strict';
    var imgList,
        CurrentImage = function () {
            this.elem = document.getElementById('imageAfficheeID');
            this.update = function () {
                if (this.elem.dataset) {
                    this.id = JSON.parse(decodeURIComponent(this.elem.dataset.id));
                    this.idHistoriqueImage = this.id[0];
                    this.idImage = this.id[1];
                }
            };
            this.update();
            if (this.elem.dataset) {
                switch (this.elem.dataset.format) {
                case 'petit':
                    this.format = 'grand';
                    break;
                case 'original':
                    this.format = 'originaux';
                    break;
                default:
                    this.format = this.elem.dataset.format;
                }
            }
        },
        img = new CurrentImage(),
        navImages = function (e) {
            var dest, next, key, i;
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
                for (i = 0; i < imgList.length; i += 1) {
                    if (imgList[i][0] === img.idHistoriqueImage) {
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
                    img.elem.src = img.elem.src.replace(img.idHistoriqueImage, next[0]).replace(img.elem.dataset.date, next[1]);
                    img.elem.dataset.id = encodeURIComponent(JSON.stringify([next[0], next[3]]));
                    img.elem.dataset.date = next[1];
                    img.update();
                    document.getElementById('fullscreenDesc').innerHTML = next[2];
                }
            }
        },
        fullscreen = function () {
            var elem = document.getElementById('fullscreenWrapper');
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            }
        },
        exitFullscreen = function () {
            if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
                img.elem.src = img.elem.src.replace('originaux', img.format);
                document.getElementById('fullscreenDesc').style.display = 'none';
                document.getElementById('fullscreenWrapper').style.lineHeight = 'normal';
                var newURL = window.location.href.replace(img.elem.dataset.orgid, img.idImage);
                if (newURL !== window.location.href) {
                    if (!window.location.hash) {
                        newURL += '#divImage';
                    }
                    window.location = newURL;
                }
            } else {
                img.elem.src = img.elem.src.replace(img.format, 'originaux');
                document.getElementById('fullscreenDesc').style.display = 'block';
                document.getElementById('fullscreenWrapper').style.lineHeight = window.screen.height + 'px';
            }
        };
    if (img.elem.dataset) {
        imgList = JSON.parse(decodeURIComponent(img.elem.dataset.list));
    }
    window.addEventListener('keydown', navImages);
    document.addEventListener('mozfullscreenchange', exitFullscreen);
    document.addEventListener('webkitfullscreenchange', exitFullscreen);
    document.addEventListener('fullscreenchange', exitFullscreen);
    document.getElementById('imageAfficheeID').addEventListener('click', fullscreen);
};
if (window.addEventListener) {
    window.addEventListener('load', initNavImages);
}
