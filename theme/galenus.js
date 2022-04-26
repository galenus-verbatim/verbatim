/**
 * Do something with bâle chartier data
 */
const image = document.getElementById('image');
const viewer = new Viewer(image, {
    inline: true,
    navbar: 0,
    minWidth: '100%',
    toolbar: {
        zoomIn: 4,
        zoomOut: 4,
        oneToOne: 4,
        reset: 4,
        prev: 0,
        play: 0,
        next: 0,
        rotateLeft: 0,
        rotateRight: 0,
        flipHorizontal: 0,
        flipVertical: 0,
    },
    viewed() {
        // viewer.zoomTo(1);
    },
});
(function() {
    // const ed1 set, use data 
    wear(".ed1page", ed1);
    wear(".ed2page", ed2);

    // https://www.biusante.parisdescartes.fr/iiif/2/bibnum:00039x04:0038/full/max/0/default.jpg
    function wear(css, dat) {
        if (!dat) return;
        let els = document.querySelectorAll(css);
        for (let i = 0; i < els.length; ++i) {
            let page = pad(parseInt(els[i].dataset.n) + parseInt(dat['p1']), 4);
            let url = dat['url'].replace('$0', page);
            els[i].innerHTML = '[' + dat['name'] + '.' + dat['vol'] + '.' + els[i].dataset.n + ']';
            els[i].onclick = function() {
                image.src = url;
                viewer.update();
            }
        }
    }

    function pad(num, width) {
        var s = "000000000" + num;
        return s.substring(s.length - width);
    }
})();