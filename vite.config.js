import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // hapus ini jika main.js tidak dipakai
                // 'resources/js/main.js'
            ],
            refresh: true,
        }),
    ],

    // penting: agar asset di-load dari lokasi /build/ dengan benar (baik local maupun deploy)
    base: '/build/',

    // server ini hanya berlaku di dev (npm run dev)
    server: {
        https: false, // false aja biar ga ribet di lokal
        host: 'localhost',
        port: 5173,
    },
});
