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
        },
    },

    plugins: [forms],

    content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'wl-navy': '#161a33',
        'wl-indigo': '#5b5fc7',
        'wl-gold': '#c99a2e',
        'wl-cream': '#f6f4ee',
        'wl-lavender': '#e4e2fb',
        'wl-mint': '#dcf3e4',
        'wl-lilac': '#f1e4fa'
      },
    },
  },
  plugins: [],
};
