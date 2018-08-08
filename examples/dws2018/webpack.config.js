var path = require('path')
var webpack = require('webpack')
const ExtractTextPlugin = require("extract-text-webpack-plugin");

if (process.env.NODE_ENV == 'development') {
  var hostPublicPath = '/dws';
} else {
  var hostPublicPath = '';
}

var buildPublicPath = hostPublicPath + '/wp-content/themes/dws2018/dist/';


var sassExtract = [
  {
    loader: 'css-loader', options: { importLoaders: 1 }, // translates CSS into CommonJS modules
  }, {
    loader: 'sass-loader' // compiles Sass to CSS
  },
  {
    loader: 'sass-resources-loader',
    options: {
      resources: path.resolve(__dirname, './src/css/settings/*.scss')
    }
  }];


var rules = {
    
    babel : {
      test: /\.js$/,
      loader: 'babel-loader',
      exclude: /node_modules/
    },

    files : {
      test: /\.(png|jpg|gif|svg|otf|eot|woff|ttf|eot?|woff2)$/,
      loader: 'file-loader',
      options: {
        name: '[name].[ext]?[hash]'
      }
    },


    sass : {
      test: /\.s[a|c]ss$/,
      use: ExtractTextPlugin.extract({
        use: sassExtract
      })
    },


    vue : {
      test: /\.vue$/,
      loader: 'vue-loader',
      options: {
        loaders: {
          scss: ExtractTextPlugin.extract({
            use: sassExtract,
            fallback: 'vue-style-loader'
          })
        }
      }
    }

}


module.exports = [
  {
    name: 'app',
    entry: './src/js/main.js',
    output: {
      path: path.resolve(__dirname, './dist'),
      publicPath: buildPublicPath,
      filename: 'build.js'
    },
    module: {
      rules: [
        rules.vue,
        rules.babel,
        rules.files,
        rules.sass
      ]
    },
    devServer: {
      historyApiFallback: true,
      noInfo: true
    },
    devtool: '#eval-source-map',
    plugins: [
      new ExtractTextPlugin({
        filename: "build.css"
      })
    ]
  },
  {
    name:'admin',
    entry: './src/js/admin/admin.js',
		output: {
			path: path.resolve(__dirname, './dist'),
			publicPath: buildPublicPath,
      filename: 'admin.js'
    },
    module: {
      rules: [
        rules.babel,
        rules.files,
        rules.sass
      ]
    },
    devServer: {
      historyApiFallback: true,
      noInfo: true
    },
    devtool: '#eval-source-map',
    plugins: [
      new ExtractTextPlugin({
        filename: "admin.css"
      })
    ]
  },
  {
    name:'admin_editor',
    entry: './src/js/admin/admin_editor.js',
		output: {
			path: path.resolve(__dirname, './dist'),
			publicPath: buildPublicPath,
      filename: 'admin_editor.js'
    },
    module: {
      rules: [
        rules.sass,
        rules.files
      ]
    },
    plugins: [
      new ExtractTextPlugin({
        filename: "admin_editor.css"
      })
    ]
  },
  {
    name:'admin_login',
    entry: './src/js/admin/admin_login.js',
		output: {
			path: path.resolve(__dirname, './dist'),
			publicPath: buildPublicPath,
      filename: 'admin_login.js'
    },
    module: {
      rules: [
        rules.sass,
        rules.files
      ]
    },
    plugins: [
      new ExtractTextPlugin({
        filename: "admin_login.css"
      })
    ]
  }
]




if (process.env.NODE_ENV === 'production') {

  for(var i=0;i < module.exports.length;i++) {

    module.exports[i].devtool = '#source-map';
    module.exports[i].plugins = (module.exports[i].plugins || []).concat([
      new webpack.DefinePlugin({
        'process.env': {
          NODE_ENV: '"production"'
        }
      }),
      new webpack.optimize.UglifyJsPlugin({
        compress: {
          warnings: false
        }
      }),
      new webpack.LoaderOptionsPlugin({
        minimize: true
      })
    ])

  }


}
