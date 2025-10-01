import { defineConfig } from 'vite';
import tailwind from '@tailwindcss/postcss';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    css: {
        postcss: {
            plugins: [
                tailwind(),
                autoprefixer(),
            ],
        },
    },
    build: {
        manifest: true,
        rollupOptions: {
            input: [
                'resources/sass/app.scss',
                'resources/css/tw.css',
                'resources/js/app.js',
            ],
        },
    },
});
