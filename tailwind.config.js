/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                'wl-navy':     '#161a33',
                'wl-indigo':   '#5b5fc7',
                'wl-gold':     '#c99a2e',
                'wl-cream':    '#f6f4ee',
                'wl-lavender': '#e4e2fb',
                'wl-mint':     '#dcf3e4',
                'wl-lilac':    '#f1e4fa',
            },
            fontFamily: {
                sans: ['Lato', 'sans-serif'],
            }
        },
    },
    plugins: [],
}