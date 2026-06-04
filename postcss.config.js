import fluidType from "./postcss/js/functions/fluidType.js";
import pxToVw from "./postcss/js/functions/pxToVw.js";

export default {
    plugins: {
        'postcss-mixins': {},
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
        cssnano: {
            preset: ['default', {calc: false}],
        },
    },
};