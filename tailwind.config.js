const theme = process.env.WP_THEME
const fg = require('fast-glob')

module.exports = {
  content: fg.sync([
    `./public/themes/${theme}/**/*.php`,
    './resources/**/*.js',
  ]),
  theme: {
    extend: {},
  },
  plugins: [],
}
