import { defineConfig } from 'vite';
import 'dotenv/config'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => ({
  base: mode === 'production' ? process.env.PUBLIC_BASE_PATH : '/',
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
  server: {
    cors: true,
  },
  plugins: [
    tailwindcss(),
    {
      name: 'php',
      handleHotUpdate({ file, server }) {
        if (file.endsWith('.php')) {
          server.ws.send({ type: 'full-reload', path: '*' });
        }
      },
    },
  ],
}));
