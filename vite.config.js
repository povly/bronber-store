import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import browserslist from 'browserslist';
import {browserslistToTargets} from 'lightningcss';
import {babel} from '@rollup/plugin-babel';
import {globSync} from 'glob';
import {createRequire} from 'module';

const require = createRequire(import.meta.url);
const coreJsVersion = require('core-js/package.json').version;

const blockStyles = globSync('resources/css/blocks/**/style.css');
const blockScripts = globSync('resources/js/blocks/**/index.js');

const babelTargets = {ie: '11', ios: '9'};

export default defineConfig({
    ss: {
        lightningcss: {
            targets: browserslistToTargets(
                browserslist([
                    '> 0.5%',
                    'last 2 versions',
                    'Firefox ESR',
                    'not dead',
                    'IE 11',
                    'android 4.4',
                    'ios 9',
                ])
            ),
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/lazyload.js',
                'resources/js/app.js',
                ...blockStyles,
                ...blockScripts,
            ],
            refresh: true,
        }),
        babel({
            babelHelpers: 'bundled', // Важно! Это решит ошибку 'addHelper'
            exclude: 'node_modules/**',
            extensions: ['.js', '.jsx', '.es6', '.es', '.mjs'],
            presets: [
                [
                    '@babel/preset-env',
                    {
                        targets: babelTargets,
                        modules: false,
                    },
                ],
            ],
            plugins: [
                [
                    'babel-plugin-polyfill-corejs3',
                    {
                        method: 'usage-global',
                        targets: babelTargets,
                        version: coreJsVersion,
                    },
                ],
            ],
        }),
    ],
    corePlugins: {
        preflight: false,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        cssMinify: 'lightningcss',
        minify: true,
        target: 'es2017',
    },
});