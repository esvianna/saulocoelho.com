module.exports = {
  content: ["./**/*.php", "./src/**/*.js"],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        "primary": "#C5A059",
        "primary-light": "#D4AF37",
        "primary-dark": "#A6894A",
        "background-light": "#f6f7f8",
        "background-dark": "#050A14",
        "background-dark-alt": "#0A0E1A"
      },
      fontFamily: {
        "display": ["Playfair Display", "Georgia", "serif"],
        "sans": ["Inter", "sans-serif"]
      },
      borderRadius: {
        "DEFAULT": "0.125rem",
        "lg": "0.25rem",
        "xl": "0.5rem",
        "full": "0.75rem"
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/container-queries'),
  ],
}
