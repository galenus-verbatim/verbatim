/**
 * Do something with bâle chartier data
 */
const image = document.getElementById('image');
const pagimage = document.getElementById('viewcont');
const viewer = new Viewer(pagimage, {
    transition: false,
    inline: true,
    navbar: 0,
    // minWidth: '100%', 
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
    let first = true;
    // const ed1 set, use data 
    wear(".pb", ed);
    wear(".ed1page", ed1);
    wear(".ed2page", ed2);

    // https://www.biusante.parisdescartes.fr/iiif/2/bibnum:00039x04:0038/full/max/0/default.jpg
    function wear(css, dat) {
        if (!dat) return;
        let els = document.querySelectorAll(css);
        for (let i = 0; i < els.length; ++i) {

            var span = document.createElement("span");
            span.className = "pageview";

            let p = els[i].dataset.n;
            if (!p) p = els[i].dataset.page;
            let url;
            // specific Kühn
            const pos = p.indexOf(".");
            let pno;
            let text = '[';
            if (els[i].classList.contains('page1') || els[i].classList.contains('pbde')) {
                text += '…';
            }
            if (pos !== -1) {
                text += p + ' ' + dat['name'];
                // pad page number for biusante 
                pno = pad(parseInt(p.substring(pos + 1)) + parseInt(dat['p1']), 4);
                const volsearch = 'x' + pad(parseInt(dat['vol']), 2);
                const volreplace = 'x' + pad(p.substring(0, pos), 2);
                url = dat['url'].replace('%%', pno).replace(volsearch, volreplace);
            } else {
                text += dat['vol'] + '.' + p + ' ' + dat['name'];
                // pad page number for biusante 
                pno = pad(parseInt(p) + parseInt(dat['p1']), 4);
                url = dat['url'].replace('%%', pno);
            }
            text += ']';
            span.innerText = text;
            // els[i].parentNode.insertBefore(span, els[i].nextSibling);
            els[i].appendChild(span);


            span.onclick = function() {
                if (viewer.spanLast) viewer.spanLast.classList.remove("selected");
                this.classList.add("selected");
                viewer.spanLast = this;
                image.src = url;
                if (dat.title) image.alt = dat.title.replace('%%', pno);
                viewer.update();
                viewer.resize();
            }
            if (first) {
                span.click();
                first = false;
            }
        }
    }

    function pad(num, width) {
        var s = "000000000" + num;
        return s.substring(s.length - width);
    }
})();