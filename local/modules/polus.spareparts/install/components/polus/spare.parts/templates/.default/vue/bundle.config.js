const rollupVue = require('rollup-plugin-vue2');
const babelMinify = require('rollup-plugin-babel-minify');
const postcss = require("rollup-plugin-postcss");
const postcssImport = require("postcss-import");
const sass = require("rollup-plugin-sass");

module.exports = {
	input: './src/main.js',
	output: '../script.js',
	namespace: 'BX.Polus.Components',
	plugins: {
		resolve: true,
		custom: [
			rollupVue(),
			postcss({
				plugins: [postcssImport()]
			}),
			sass({
					//input: './src/style.scss',
					output: '../style.css',
				}
			),
			babelMinify({
				comments: false,
			})
		]
	}
};
