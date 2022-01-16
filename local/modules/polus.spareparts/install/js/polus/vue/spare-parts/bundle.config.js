const rollupVue = require('rollup-plugin-vue2');
const postcss = require('rollup-plugin-postcss');
const postcssImport = require('postcss-import');
const sass = require('rollup-plugin-sass');
const babelMinify = require('rollup-plugin-babel-minify');

module.exports = {
	input: 'src/main.js',
	output: 'dist/main.bundle.js',
	namespace: 'BX.Polus.Vue.Parts',
	plugins: {
		resolve: true,
		babel: {
			babelrc: false,
			presets: [
				[
					"@babel/preset-env",
					{
						"targets": {
							"ie": "11"
						},
					}
				]
			]
		},
		custom: [
			rollupVue(),
			postcss({
				plugins: [postcssImport()]
			}),
			sass({
					output: './dist/main.bundle.css',
				}
			),
			babelMinify({
				comments: false,
			})
		]
	}
};
