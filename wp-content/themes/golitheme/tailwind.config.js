/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './assets/scripts/**/*.js',
    './components/**/*.php',
    './templates/**/*.php',
    './inc/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        // Primary gradient lavender matte
        'lavender': {
          50: '#F8F4FF',
          100: '#F0E8FF',
          200: '#E1D1FF',
          300: '#C8A2C8',
          400: '#B08FB0',
          500: '#9A7B9A',
          600: '#8A6B8A',
          700: '#7A5B7A',
          800: '#6A4B6A',
          900: '#5A3B5A',
        },
        'lavender-gradient': {
          from: '#C8A2C8',
          to: '#EBDDF9',
          hover: '#B08FB0'
        },
        // Accent gold/beige
        'gold': {
          50: '#FFFDF5',
          100: '#FFF9E6',
          200: '#FFF2CC',
          300: '#FFE699',
          400: '#FFD966',
          500: '#D4AF37',
          600: '#B8952D',
          700: '#9C7B23',
          800: '#806119',
          900: '#64470F',
        },
        'beige': {
          50: '#FEFEFE',
          100: '#FDFDFD',
          200: '#FBFBFB',
          300: '#F8F8F8',
          400: '#F5F5DC',
          500: '#F0F0E6',
          600: '#E6E6D1',
          700: '#DCDCC7',
          800: '#D2D2BD',
          900: '#C8C8B3',
        },
        // Neutrals
        'soft-black': '#2C2C2C',
        'pure-white': '#FFFFFF'
      },
      fontFamily: {
        'fa': ['Doran', 'Tahoma', 'Arial', 'sans-serif'],
        'en': ['Crimson Pro', 'Georgia', 'serif'],
      },
      borderRadius: {
        '3xl': '1.5rem',
        'full': '50%',
      },
      boxShadow: {
        'soft': '0 4px 20px rgba(200, 162, 200, 0.1)',
        'soft-lg': '0 8px 30px rgba(200, 162, 200, 0.15)',
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-in-out',
        'slide-in': 'slideIn 0.3s ease-out',
        'scale-up': 'scaleUp 0.2s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideIn: {
          '0%': { transform: 'translateX(100%)' },
          '100%': { transform: 'translateX(0)' },
        },
        scaleUp: {
          '0%': { transform: 'scale(0.95)' },
          '100%': { transform: 'scale(1)' },
        },
      },
    },
  },
  plugins: [
    // RTL support plugin
    function({ addUtilities }) {
      const newUtilities = {
        '.rtl': {
          direction: 'rtl',
        },
        '.ltr': {
          direction: 'ltr',
        },
        // Logical properties for RTL/LTR compatibility
        '.ms-auto': {
          'margin-inline-start': 'auto',
        },
        '.me-auto': {
          'margin-inline-end': 'auto',
        },
        '.ps-4': {
          'padding-inline-start': '1rem',
        },
        '.pe-4': {
          'padding-inline-end': '1rem',
        },
        '.border-s': {
          'border-inline-start': '1px solid',
        },
        '.border-e': {
          'border-inline-end': '1px solid',
        },
        '.rounded-s': {
          'border-start-start-radius': '0.25rem',
          'border-end-start-radius': '0.25rem',
        },
        '.rounded-e': {
          'border-start-end-radius': '0.25rem',
          'border-end-end-radius': '0.25rem',
        },
      }
      addUtilities(newUtilities)
    }
  ],
}
