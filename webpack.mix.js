let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .extract(['vue', 'element-ui', 'vue2-filters', 'axios', 'v-viewer', 'vue-moment', 'vue-carousel'
   	       ,'vue-mq', 'lodash', 'vue-social-sharing', '@casl/ability', '@casl/vue', '@fortawesome/fontawesome-svg-core',
   	       '@fortawesome/free-solid-svg-icons', '@fortawesome/free-brands-svg-icons', '@fortawesome/free-regular-svg-icons',
   	       '@fortawesome/vue-fontawesome'])
   .sass('resources/assets/sass/app.scss', 'public/css');
