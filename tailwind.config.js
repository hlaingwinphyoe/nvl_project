import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        screens: {
            sm: "640px",
            // => @media (min-width: 640px) { ... }

            md: "768px",
            // => @media (min-width: 768px) { ... }

            lg: "1024px",
            // => @media (min-width: 1024px) { ... }

            xl: "1280px",
            // => @media (min-width: 1280px) { ... }

            "2xl": "1536px",
            // => @media (min-width: 1536px) { ... }

            minsm: { min: "641px" },
            minmd: { min: "769px" },
            minlg: { min: "1025px" },
            minxl: { min: "1281px" },
            min2xl: { min: "1537px" },
        },
        container: {
            padding: "1rem",
        },
        extend: {
            colors: {
                primary: {
                    50: "#f3f6fc",
                    100: "#e6ecf8",
                    200: "#c7d7f0",
                    300: "#96b6e3",
                    400: "#5d90d3",
                    500: "#356bb3",
                    600: "#2858a1",
                    700: "#224782",
                    800: "#1f3e6d",
                    900: "#1f355b",
                },
            },
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
