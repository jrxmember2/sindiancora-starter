import forms from '@tailwindcss/forms';

export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.jsx',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        brand: {
          50: '#eff6ff',
          100: '#dbeafe',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          950: '#172554',
        },
        ink: '#0f172a',
        mist: '#f8fafc',
      },
      boxShadow: {
        soft: '0 24px 80px rgba(15, 23, 42, 0.08)',
        line: '0 0 0 1px rgba(148, 163, 184, 0.18)',
      },
      borderRadius: {
        '3xl': '1.65rem',
        '4xl': '2rem',
      },
    },
  },
  plugins: [forms],
};
