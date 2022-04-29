const mix = require('laravel-mix');
const theme = process.env.WP_THEME;

require('dotenv').config();

mix.setResourceRoot('../');
mix.setPublicPath(`public/themes/${theme}/assets`);

mix.js('resources/js/app.js', 'js')
   .postCss('resources/css/app.css', 'css');

mix.browserSync({
  proxy: process.env.APP_URL,
  files: [
    `public/themes/${theme}/**/*.(php|js|css)`
  ],
});

if (mix.inProduction()) {
  mix.version();
}
