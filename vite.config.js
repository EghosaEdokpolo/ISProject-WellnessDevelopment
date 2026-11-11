import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css','resources/css/HR_overview.css',
                'resources/css/style.css', 'resources/css/hr_view_style.css',
                'resources/css/HR_impact.css', 'resources/css/HR_departmental.css',
                'resources/css/HR_retention.css', 'resources/css/hr_settings.css',
                'resources/css/hr_events_edit.css', 'resources/css/hr_eventmanage.css',
                
                
                'resources/js/app.js', 'resources/js/hr_impact_filter.js', 
                'resources/js/HR_overview.js', 'resources/js/hr_settings.js', 
                'resources/js/hr_retention_filter.js', 'resources/js/hr_eventmanage.js',
                
                ],
            refresh: true,
        }),
    ],
});
