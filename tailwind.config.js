const theme = process.env.WP_DEFAULT_THEME

module.exports = {
  content: [
    `./public/themes/${theme}/**/*.php`,
    './resources/**/*.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
