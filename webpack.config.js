const path = require('path');
const merge = require('webpack-merge');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const pug = require('./resources/assets/default/webpack_modules/pug');
const extractCSS = require('./resources/assets/default/webpack_modules/css.extract');
const css = require('./resources/assets/default/webpack_modules/css');
const webpack = require('webpack');
const sourceMap = require('./resources/assets/default/webpack_modules/sourceMap');
const lintJS = require('./resources/assets/default/webpack_modules/js.lint');
const lintCSS = require('./resources/assets/default/webpack_modules/sass.lint');
const images = require('./resources/assets/default/webpack_modules/images');
const babel = require('./resources/assets/default/webpack_modules/babel');
const favicon = require('./resources/assets/default/webpack_modules/favicon');

const PATHS = {
  source: path.join(__dirname, '/resources'),
  build: path.join(__dirname, '/public'),
};

const config = merge([
  {
    entry: {
      'main': PATHS.source + '/assets/default/source/main.js',
    },
    output: {
      path: PATHS.build,
      filename: './js/[name].js',
    },
    watch: true,
    plugins: [
      /*new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
      }),*/
      new HtmlWebpackPlugin({
        filename: PATHS.source + '/views/index.blade.php',
        template: PATHS.source + '/assets/default/source/pages/index.pug',
        inject: true,
      }),
    ],
  },
  images(),
  pug(),
  lintJS({paths: PATHS.sources}),
  lintCSS(),
  babel(),
]);

module.exports = function (env, argv) {
  if (argv.mode === 'production') {
    config.devtool = false;
    config.watch = false;
    return merge([
      config,
      extractCSS(),
      favicon(),
    ]);
  }
  if (argv.mode === 'development') {
    return merge([
      config,
      extractCSS(),
      sourceMap(),
    ]);
  }
};
