const FaviconsWebpackPlugin = require('favicons-webpack-plugin');
module.exports = function() {
  return {
    plugins: [
      new FaviconsWebpackPlugin({
        logo: './resources/assets/default/source/components/_defaults/favicon.png',
      }),
    ],
  };
};
