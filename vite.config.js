import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        // 使用具体IP而非[::1]
        host: 'localhost',
        // 或者使用所有网络接口
        // host: '0.0.0.0',
        hmr: {
            host: 'localhost'
        }
    }
});
