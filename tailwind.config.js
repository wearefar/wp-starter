const theme = process.env.WP_THEME;

module.exports = {
  purge: [
    `./public/themes/${theme}/**/*.php`,
    './resources/**/*.js',
  ],
  darkMode: false,
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
