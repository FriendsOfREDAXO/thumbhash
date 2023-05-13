let allThumbs = document.querySelectorAll('[data-thumbhash]');
let allThumbImages = document.querySelectorAll('[data-thumbhashimg]');

// Set Thumbhash-Image from ThumbHash
allThumbs.forEach((timage) => {
    let b64thumbhash = timage.getAttribute('data-thumbhash');
    if (b64thumbhash !== null) {
        let urldata = thumbHashToDataURL(Uint8Array.from(atob(b64thumbhash), c => c.charCodeAt(0)));
        timage.src = urldata;
    }
});

// Set Thumbhash-Image
allThumbImages.forEach((timage) => {
    let thumbhashimage = timage.getAttribute('data-thumbhashimg');
    if (thumbhashimage !== null) {
        timage.src = thumbhashimage;
    }
});

// Set original Image for all ThumbHashes
allThumbs.forEach((timage) => {
    let b64thumbhash = timage.getAttribute('data-thumbhash');
    if (b64thumbhash !== null) {
        let originalSrc = timage.getAttribute('data-thumbhashsrc');
        if (originalSrc !== null) {
            timage.addEventListener('load', () => {
                setTimeout(() => timage.src = originalSrc, 0)
            }, { once: true })
        }
    }
});

// Set original Image for all ThumbHashe-Images
allThumbImages.forEach((timage) => {
    let thumbhashimage = timage.getAttribute('data-thumbhashimg');
    if (thumbhashimage !== null) {
        let originalSrc = timage.getAttribute('data-thumbhashsrc');
        if (originalSrc !== null) {
            timage.addEventListener('load', () => {
                setTimeout(() => timage.src = originalSrc, 0)
            }, { once: true })
        }
    }
});
