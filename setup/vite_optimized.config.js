import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/sass/app.scss',
                'resources/theme/app.css',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Optimize build performance
        target: 'es2015',
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['bootstrap', 'jquery', 'axios'],
                    charts: ['chart.js'],
                    datatables: ['datatables.net'],
                },
            },
        },
        // Asset optimization
        assetsInlineLimit: 4096,
        cssCodeSplit: true,
        sourcemap: false,
        // Compression
        reportCompressedSize: true,
        chunkSizeWarningLimit: 1000,
    },
    server: {
        // Development server optimization
        hmr: {
            host: 'localhost',
        },
        cors: true,
        origin: 'http://localhost:5173',
    },
    optimizeDeps: {
        // Pre-bundle dependencies
        include: [
            'bootstrap',
            'jquery',
            'axios',
            'chart.js',
            'datatables.net',
        ],
    },
});
