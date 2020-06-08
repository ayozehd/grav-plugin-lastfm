'use strict'
const VueLoaderPlugin = require('vue-loader/lib/plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const ASSET_PATH = process.env.ASSET_PATH || '../dist/';

module.exports = {
  mode: process.env.NODE_ENV ? process.env.NODE_ENV : 'production',
  entry: [
    './src/app.js'
  ],
  output: {
    publicPath: ASSET_PATH,
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        use: 'vue-loader'
      },
      {
        test: /\.js$/,
        use: 'babel-loader'
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader'
        ],
      },
    ]
  },
  optimization: {
    minimizer: [new OptimizeCSSAssetsPlugin()],
  },
  plugins: [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin({
      filename: 'style.css'
    })
  ]
}