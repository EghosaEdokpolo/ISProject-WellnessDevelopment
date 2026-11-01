import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css','resources/css/HR_overview.css',
                'resources/css/style.css', 'resources/css/hr_view_style.css',
                'resources/css/HR_impact.css',
                
                
                'resources/js/app.js', 'resources/js/hr_impact_filter.js', 
                'resources/js/HR_overview.js',
                
                ],
            refresh: true,
        }),
    ],
});
