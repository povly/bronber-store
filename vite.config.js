import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import browserslist from 'browserslist';
import {browserslistToTargets} from 'lightningcss';
import {babel} from '@rollup/plugin-babel';

export default defineConfig({
    css: {
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
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
                        targets: {
                            ie: '11',
                            ios: '9',
                        },
                        modules: false,
                        useBuiltIns: 'entry',
                        corejs: 3,
                    },
                ],
            ],
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        cssMinify: 'lightningcss',
        minify: true,
        target: 'es2015',
    },
});