const fs = require('fs');
const path = require('path')
const HtmlWebpackPlugin = require('html-webpack-plugin');
const VueLoaderPlugin = require('vue-loader');

module.exports = {
  devServer: {
    host: 'viewer.dweet.net',
    allowedHosts: [
      '192.168.1.201,viewer.dweet.net,127.0.0.1'
    ],
	  port:8000,
    devMiddleware: {
      publicPath: '/'
    },
    headers: {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Max-Age': '1000',
      'Access-Control-Allow-Headers':'X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding',
      'Access-Control-Allow-Methods': 'POST, GET, OPTIONS, PUT',
    },
  },
  publicPath: '/',
  chainWebpack: (config) => {
    if (process.env.NODE_ENV === 'production') {
      config.plugin('html').tap((opts) => {
        opts[0].filename = './index.php';
        return opts;
      });
    }
  }
}
