import fluidType from './functions/fluidType.js';
import pxToVw from './functions/pxToVw.js';

/**
 * Locate the next fluid-type/pxToVw call.
 * nameStart = first char of name, parenEnd = closing ')' index.
 *
 * @param {string} css
 * @param {number} from
 * @returns {{name: string, nameStart: number, parenEnd: number} | null}
 */
function findNextCall(css, from) {
    const ftIdx = css.indexOf('fluid-type(', from);
    const pvIdx = css.indexOf('pxToVw(', from);

    let name;
    let idx;

    if (ftIdx === -1 && pvIdx === -1) return null;
    if (ftIdx === -1) {
        name = 'pxToVw';
        idx = pvIdx;
    } else if (pvIdx === -1) {
        name = 'fluid-type';
        idx = ftIdx;
    } else if (ftIdx < pvIdx) {
        name = 'fluid-type';
        idx = ftIdx;
    } else {
        name = 'pxToVw';
        idx = pvIdx;
    }

    let depth = 0;
    let i = idx + name.length;
    for (; i < css.length; i++) {
        if (css[i] === '(') depth++;
        else if (css[i] === ')') {
            depth--;
            if (depth === 0) break;
        }
    }

    return { name, nameStart: idx, parenEnd: i };
}

/**
 * Splits a comma-separated argument list, respecting nested parens and quotes.
 *
 * @param {string} args
 * @returns {string[]}
 */
function splitArgs(args) {
    const result = [];
    let depth = 0;
    let current = '';
    let inQuotes = null;

    for (let i = 0; i < args.length; i++) {
        const ch = args[i];

        if (inQuotes) {
            current += ch;
            if (ch === inQuotes && args[i - 1] !== '\\') inQuotes = null;
            continue;
        }

        if (ch === '"' || ch === "'") {
            inQuotes = ch;
            current += ch;
            continue;
        }

        if (ch === '(') depth++;
        else if (ch === ')') depth--;

        if (ch === ',' && depth === 0) {
            result.push(current.trim());
            current = '';
            continue;
        }

        current += ch;
    }

    if (current.trim()) result.push(current.trim());

    return result;
}

/**
 * @param {string} css
 * @returns {string}
 */
function transformCss(css) {
    let result = css;
    let searchFrom = 0;

    while (true) {
        const found = findNextCall(result, searchFrom);
        if (!found) break;

        const { name, nameStart, parenEnd } = found;
        const argsStr = result.slice(nameStart + name.length + 1, parenEnd);
        const parts = splitArgs(argsStr);

        let replacement = '';

        if (name === 'fluid-type' && parts.length >= 4) {
            try {
                const negative = parts[4]?.trim() === 'true';
                replacement = fluidType(parts[0], parts[1], parts[2], parts[3], negative);
            } catch {
            }
        } else if (name === 'pxToVw' && parts.length >= 1) {
            try {
                const layoutWidth = parts[1] ? parseFloat(parts[1]) : 1920;
                replacement = pxToVw(parts[0], layoutWidth);
            } catch {
            }
        }

        if (replacement) {
            result = result.slice(0, nameStart) + replacement + result.slice(parenEnd + 1);
            searchFrom = nameStart + replacement.length;
        } else {
            // Advance past unparseable call — otherwise the loop never terminates
            searchFrom = parenEnd + 1;
        }
    }

    return result;
}

/**
 * Vite plugin that transforms custom CSS functions `fluid-type()` and `pxToVw()`
 * into valid CSS expressions.
 *
 * THREE hooks:
 * - `transform` (enforce: 'pre') — processes each CSS file at load time,
 *   BEFORE the postcss chain reads it.
 * - `handleHotUpdate` — forces full page reload when ANY .css file changes.
 *   Covers files that load outside Vite's module graph.
 * - `generateBundle` — fallback safety net for stragglers in final output.
 *
 * @returns {import('vite').Plugin}
 */
export function cssFunctions() {
    return {
        name: 'vite-plugin-css-functions',
        enforce: 'pre',
        transform(code, id) {
            if (id.includes('node_modules')) return null;
            if (!code.includes('fluid-type(') && !code.includes('pxToVw(')) {
                return null;
            }
            return transformCss(code);
        },
        handleHotUpdate({ file, server }) {
            if (!file.endsWith('.css')) return;
            server.ws.send({ type: 'full-reload' });
        },
        generateBundle(_options, bundle) {
            for (const [, asset] of Object.entries(bundle)) {
                if (asset.type !== 'asset') continue;
                if (!asset.fileName.endsWith('.css')) continue;
                const source = typeof asset.source === 'string' ? asset.source : '';
                if (
                    !source.includes('fluid-type(') &&
                    !source.includes('pxToVw(')
                ) {
                    continue;
                }
                asset.source = transformCss(source);
            }
        },
    };
}
