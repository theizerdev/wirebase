import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/cliente/main.jsx',
                'resources/js/whatsapp/main.jsx',
                'resources/js/sorteo/main.jsx'
            ],
            refresh: true,
        }),
        tailwindcss(),
        react(),
    ],
});
