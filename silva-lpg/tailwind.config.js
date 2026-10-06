import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Primary brand — dark brownish red (maroon)
                maroon: {
                    50: '#fbeeee',
                    100: '#f6d9d9',
                    200: '#e9b3b3',
                    300: '#d98282',
                    400: '#c24f4f',
                    500: '#9e2b2b',
                    600: '#7a1f24', // primary buttons / links
                    700: '#5e181c',
                    800: '#481216',
                    900: '#330c10',
                },
                // Status indicators: green = success/complete, yellow = pending/delayed, red = error/cancelled
                status: {
                    success: '#16a34a',
                    'success-bg': '#dcfce7',
                    warning: '#b45309',
                    'warning-bg': '#fef3c7',
                    danger: '#dc2626',
                    'danger-bg': '#fee2e2',
                },
            },
        },
    },

    plugins: [forms],
};
