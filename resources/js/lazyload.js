// ESM-сборка пакета: CJS main даёт interop-рантайм, общий с app.js → лишний чанк.
import LazyLoad from "vanilla-lazyload/dist/esm/lazyload.js";
window.LazyLoad = LazyLoad;

document.addEventListener('DOMContentLoaded', function () {
    var lazyLoadInstance = new LazyLoad({});
});