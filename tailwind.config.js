import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
        colors: {
            primary: {
                DEFAULT: '#1E3A8A',
                50: '#E8EAF6',
                100: '#C5CAE9',
                200: '#9FA8DA',
                300: '#7986CB',
                400: '#5C6BC0',
                500: '#3F51B5',
                600: '#394AAE',
                700: '#3140A4',
                800: '#29379A',
                900: '#1D2789',
            },
            secondary: {
                DEFAULT: '#FFA000',
                50: '#FFF8E1',
                100: '#FFECB3',
                200: '#FFE083',
                300: '#FFD452',
                400: '#FFCC2A',
                500: '#FFC400',
                600: '#FFC000',
                700: '#FFB800',
                800: '#FFB000',
                900: '#FFA300',
            },
            gray: {
                50: '#FAFAFA',
                100: '#F5F5F5',
                200: '#EEEEEE',
                300: '#E0E0E0',
                400: '#BDBDBD',
                500: '#9E9E9E',
                600: '#757575',
                700: '#616161',
                800: '#424242',
                900: '#212121',
            },
            symph: {
                50:'#f7f7f7',
                100:'#eaeaea',
                200:'#d4d4d4',
                300:'#b3b3b3',
                400:'#808080',
                500:'#2c2c2c',
                600:'#1f1f1f',
                700:'#1a1a1a',
                800:'#151515',
                900:'#0f0f0f',
            },
            white: '#FFFFFF',
            black: '#000000',
        },
    },

    plugins: [forms, typography],
};
