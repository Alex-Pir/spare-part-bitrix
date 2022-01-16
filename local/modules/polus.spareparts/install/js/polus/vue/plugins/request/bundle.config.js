const rollupVue = require('rollup-plugin-vue2');
const babelMinify = require('rollup-plugin-babel-minify');

module.exports = {
	input: './src/request.js',
	output: './dist/main.bundle.js',
	namespace: 'BX.Polus.Plugins.Request',
	plugins: {
		resolve: true,
		custom: [
			rollupVue(),
			babelMinify({
				comments: false,
			})
		]
	}
};
