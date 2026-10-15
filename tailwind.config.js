import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
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
                // Klubová barva Sokola Částkov – ve views se používá jako bg-brand-700, text-brand-600 apod.
                // Pro přesný odstín stačí nahradit paletu vlastními hodnotami { 50: '#...', ..., 900: '#...' }.
                brand: colors.green,
            },
        },
    },

    plugins: [forms],
};
