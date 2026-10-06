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
                // Primary brand palette — dark, brownish-red "maroon".
                // maroon-600 is the main brand color (buttons, active nav,
                // links); maroon-700 is the sidebar/darker surface;
                // maroon-50/100/200/500 are used for tints, muted text on
                // dark backgrounds, and avatar/icon fills.
                maroon: {
                    50: '#fbf2f2',
                    100: '#f4dede',
                    200: '#e8bdbd',
                    300: '#d89797',
                    400: '#b96b6b',
                    500: '#96494a',
                    600: '#7a1f24', // primary
                    700: '#5c171b', // sidebar / hover-darken
                    800: '#421114',
                    900: '#2c0b0d',
                },

                // Status colors — used as text-status-* (icon/text) and
                // bg-status-*-bg (soft background) pairs across badges,
                // alerts, and stat cards.
                'status-success': '#16a34a',
                'status-success-bg': '#dcfce7',
                'status-warning': '#b45309',
                'status-warning-bg': '#fef3c7',
                'status-danger': '#dc2626',
                'status-danger-bg': '#fee2e2',
            },
        },
    },

    plugins: [forms],
};