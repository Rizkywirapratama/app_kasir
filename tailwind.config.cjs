/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./application/views/**/*.php",
		"./assets/js/**/*.js",
		"./assets/css/**/*.css",
	],
	theme: {
		extend: {
			fontFamily: {
				sans: ["Plus Jakarta Sans", "ui-sans-serif", "system-ui"],
			},
			colors: {
				ndc: {
					DEFAULT: "#a78bfa",
				},
			},
		},
	},
	plugins: [],
};
