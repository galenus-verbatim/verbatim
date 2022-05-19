/**
 * Do something with bâle chartier data
 */
const image = document.getElementById('image');
const div = document.getElementById('viewcont');
if (div) {
    var pageViewer = new Viewer(div, {
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
        title: function(image) {
            return image.alt;
        },
        viewed() {
            // default zoom on load, image width
            let cwidth = div.offsetWidth;
            let iwidth = pageViewer.imageData.naturalWidth;
            let zoom = cwidth / iwidth;
            pageViewer.zoomTo(zoom);
            pageViewer.moveTo(0, 0);
        },
    });
}
(function() {
    let first = true;
    // const ed1 set, use data 
    if (typeof imgkuhn !== 'undefined') wear(".pb", imgkuhn);
    if (typeof imgbale !== 'undefined') wear(".ed1page", imgbale);
    if (typeof imgchartier !== 'undefined') wear(".ed2page", imgchartier);

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
            // has been seen, but no more used
            // const pos = p.indexOf(".");
            let pno;
            let text = '[';
            if (els[i].classList.contains('page1') || els[i].classList.contains('pbde')) {
                text += '…';
            }
            let pdiff = dat['pdiff'];
            if (dat['pholes']) {
                for (const prop in dat['pholes']) {
                    if (p >= prop) {
                        pdiff = dat['pholes'][prop];
                    } else {
                        break;
                    }
                }
            }

            text += dat['vol'] + '.' + p + ' ' + dat['abbr'];
            // pad page number for biusante 
            pno = pad(parseInt(p) + parseInt(pdiff), 4);
            url = dat['url'].replace('%%', pno);
            text += ']';
            span.innerText = text;
            // els[i].parentNode.insertBefore(span, els[i].nextSibling);
            els[i].appendChild(span);


            span.onclick = function() {
                if (pageViewer.spanLast) pageViewer.spanLast.classList.remove("selected");
                this.classList.add("selected");
                pageViewer.spanLast = this;
                image.src = url;
                if (dat.title) image.alt = text + ' source : ' + dat.title.replace('%%', pno);
                pageViewer.update();
                pageViewer.resize();
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