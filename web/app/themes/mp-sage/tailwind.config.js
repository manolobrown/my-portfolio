/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './app/View/**/*.php',
  ],
  theme: {
    extend: {
      // Add custom theme extensions here
      // Colors, fonts, spacing, etc. will be available in theme.json
    },
  },
  plugins: [
    // Add Tailwind plugins here if needed
    // Example: require('@tailwindcss/forms'),
  ],
}
