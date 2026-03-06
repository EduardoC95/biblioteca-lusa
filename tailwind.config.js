import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                display: ['Merriweather', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms, typography, daisyui],

    daisyui: {
        themes: [
            {
                woodhaven: {
                    primary: '#8b5a2b',
                    secondary: '#b08968',
                    accent: '#a67c52',
                    neutral: '#3f2f22',
                    'base-100': '#f4e6d0',
                    'base-200': '#e8d4b5',
                    'base-300': '#d9c1a0',
                    info: '#7b5e3b',
                    success: '#5b7d4a',
                    warning: '#b07930',
                    error: '#9e3d2d',
                },
            },
        ],
    },
};
