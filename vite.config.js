import { defineConfig } from 'vite';

require('dotenv').config();

export default defineConfig(() => ({
  publicDir: 'resources/static',
  build: {
    emptyOutDir: true,
    manifest: true,
    outDir: `public/themes/${process.env.WP_DEFAULT_THEME}/build/`,
    assetsDir: 'assets',
    rollupOptions: {
      input: [
        'resources/js/app.js',
        'resources/css/app.css',
      ],
    },
  },
}));
