import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
export default {
    // Memasukkan standar desain Filament
    presets: [preset], 
    
    // Lokasi file yang mengandung class Tailwind agar di-scan oleh Vite
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    
    theme: {
        extend: {},
    },
    plugins: [],
}