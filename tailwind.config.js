/** @type {import('tailwindcss').Config} */
export default {
  // Tambahkan ini agar semua class tailwind harus diawali "tw-"
  prefix: 'tw-',

  content: [
    "./resources/views/*.blade.php",
    "./resources/views/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        dabelyu: {
          dark: '#0f5132',
          primary: '#198754',
          light: '#d1e7dd',
          accent: '#0d9488',
        } 
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'serif'],
        sans: ['Inter', 'sans-serif'],
      },
      boxShadow: {
        'soft': '0 20px 40px -15px rgba(0, 0, 0, 0.1)',
      }
    },
  },
  corePlugins: {
    preflight: false, // Tetap matikan ini demi Bootstrap
  },
  plugins: [],
}   