import fluidType from "./postcss/js/functions/fluidType.js";
import pxToVw from "./postcss/js/functions/pxToVw.js";

export default {
    plugins: {
        'postcss-mixins': {
            mixinsFiles: 'resources/css/mixins/*.css',
        },
        'postcss-functions': {
            functions: {
                'fluid-type': fluidType,
                'px-to-vw': pxToVw,
            }
        },
        'postcss-nested-import': {},
        'postcss-nested': {},
        'postcss-simple-vars': {},
        autoprefixer: {},
        'css-declaration-sorter': { order: 'smacss' },
        cssnano: {
            preset: ['default', {calc: false}],
        },
    },
};
