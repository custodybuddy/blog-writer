/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./**/*.php', './assets/**/*.{js,css}'],
  theme: {
    extend: {
      fontFamily: {
        raleway: ['Raleway', 'sans-serif'],
        prata: ['Prata', 'serif'],
      },
    },
  },
  plugins: [],
};
