/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Filament/**/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
      },
      colors: {
        brand: {
          50: '#eefbfc',
          100: '#d5f3f6',
          500: '#2f8ea3',
          600: '#26798d',
          700: '#1f6070',
        },
      },
    },
  },
  plugins: [],
};
