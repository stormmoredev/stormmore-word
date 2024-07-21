const typographyPlugin = require('@tailwindcss/typography')
const forms = require('@tailwindcss/forms')
const typographyStyles = require('./typography')

module.exports = {
    content: ["./src/app/backend/templates/**/*.php",
              "./server/themes/**/*.php"],
    plugins: [],
    theme: { }
}