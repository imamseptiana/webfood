import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: true, // Izinkan akses dari IP luar
        hmr: {
            host: 'd02c-114-124-212-51.ngrok-free.app', // Host sesuai ngrok
            protocol: 'wss', // Pakai 'wss' karena ngrok pakai HTTPS
        },
        // optional:
        // port: 5173, // Jika kamu mau tentukan port Vite secara manual
    },
});
