import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import browserslist from 'browserslist';
import {browserslistToTargets} from 'lightningcss';
import {babel} from '@rollup/plugin-babel';
import {globSync} from 'glob';
import {combineMediaQueries} from './postcss/js/viteCombineMediaQuery.js';

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
        // Объединяет одинаковые @media запросы в финальном бандле AFTER
        // lightningcss минификации + сортирует по min-width ascending
        // (mobile-first cascade correctness). Даёт cleaner gzip.
        combineMediaQueries(),

        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/moonshine.css',
                'resources/js/lazyload.js',
                'resources/js/app.js',
                ...blockStyles,
                ...blockScripts,
            ],
            refresh: true,
        }),
        babel({
            // 'inline': хелперы дублируются в каждый файл вместо общего
            // _rollupPluginBabelHelpers-чанка.
            babelHelpers: 'inline',
            exclude: 'node_modules/**',
            extensions: ['.js', '.jsx', '.es6', '.es', '.mjs'],
            presets: [
                [
                    '@babel/preset-env',
                    {
                        targets: babelTargets,
                        modules: false,
                        // Полифиллы: один глобальный import 'core-js/stable' в
                        // app.js. usage-global раскидывал импорты core-js по
                        // модулям → Rollup выносил их в разделяемые чанки.
                        useBuiltIns: 'entry',
                        corejs: 3,
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