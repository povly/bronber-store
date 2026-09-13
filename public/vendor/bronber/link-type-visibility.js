/**
 * Conditional visibility for two-type link fields
 * ({label, type: page|custom, page, url}) inside Json rows of
 * flexible-layout blocks (Настройки → Шапка/Подвал, колонки ссылок).
 *
 * WHY THIS EXISTS: MoonShine's native showWhen() does not reach fields
 * inside povly/moonshine-flexible-layouts blocks — the FlexibleLayouts
 * field does not implement HasFieldsContract, so FormBuilder never
 * collects nested conditions into the form's Alpine data. This shim is
 * structural instead: for every select whose name ends with "[type]" it
 * shows/hides the sibling "[page]" / "[url]" field wrappers of the same
 * row. Rows are matched by full name prefix, so nested and reindexed
 * (add/remove/reorder) rows resolve correctly.
 */
(function () {
    'use strict';

    var TYPE_SUFFIX = '[type]';

    function endsWith(haystack, suffix) {
        return haystack.indexOf(suffix, haystack.length - suffix.length) !== -1;
    }

    function inputByName(root, name) {
        return root ? root.querySelector('[name="' + name + '"]') : null;
    }

    function rowOf(select) {
        var name = select.getAttribute('name') || '';

        if (! endsWith(name, TYPE_SUFFIX)) {
            return null;
        }

        var prefix = name.slice(0, -TYPE_SUFFIX.length);
        var node = select.parentNode;

        while (node && node !== document.documentElement) {
            if (inputByName(node, prefix + '[page]') || inputByName(node, prefix + '[url]')) {
                return { root: node, prefix: prefix };
            }

            node = node.parentNode;
        }

        return null;
    }

    /**
     * The field's own wrapper (label + hint + input): the topmost
     * ancestor of the input that does not contain the sibling field.
     */
    function wrapperOf(input, siblingName) {
        var node = input;

        while (node.parentNode && ! inputByName(node.parentNode, siblingName)) {
            node = node.parentNode;
        }

        return node;
    }

    function apply(select) {
        var row = rowOf(select);

        if (! row) {
            return;
        }

        var isPage = select.value === 'page';
        var pageInput = inputByName(row.root, row.prefix + '[page]');
        var urlInput = inputByName(row.root, row.prefix + '[url]');

        if (pageInput) {
            wrapperOf(pageInput, row.prefix + '[url]').style.display = isPage ? '' : 'none';
        }

        if (urlInput) {
            wrapperOf(urlInput, row.prefix + '[page]').style.display = isPage ? 'none' : '';
        }
    }

    function init(select) {
        if (select.getAttribute('data-link-type-bound')) {
            return;
        }

        select.setAttribute('data-link-type-bound', '1');
        select.addEventListener('change', function () {
            apply(select);
        });
        apply(select);
    }

    function scan(root) {
        var selects = (root || document).querySelectorAll('select[name$="[type]"]');
        var i;

        for (i = 0; i < selects.length; i++) {
            init(selects[i]);
        }
    }

    scan(document);

    // Flexible layouts add blocks via AJAX and Json rows get reindexed —
    // watch the DOM and (re)bind new selects. Idempotent: already-bound
    // selects are skipped by the data-link-type-bound guard.
    if (window.MutationObserver) {
        new MutationObserver(function () {
            scan(document);
        }).observe(document.documentElement, { childList: true, subtree: true });
    }
})();
