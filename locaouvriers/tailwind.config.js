import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        brand: {
          dark: '#03071e',
          navy: '#0a1128',
          royal: '#002855',
          yellow: '#ffb703',
          orange: '#fb8500',
        }
      }
    },
  },
  plugins: [forms],
};
