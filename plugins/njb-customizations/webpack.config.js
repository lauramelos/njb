const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: {
    main: './src/js/main.js',
    style: './src/css/style.scss'
  },
  output: {
    path: path.resolve(__dirname, 'build'),
    filename: '[name].js', // genera main.js y style.js (aunque style.js no lo vamos a usar)
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: 'babel-loader',
        exclude: /node_modules/,
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader'
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css', // genera style.css
    }),
  ],
  mode: 'development'
};