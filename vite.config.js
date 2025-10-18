import { defineConfig } from 'vite';
import tailwindcss from "@tailwindcss/vite";
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/app/theme.css',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/auth/theme.css'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
