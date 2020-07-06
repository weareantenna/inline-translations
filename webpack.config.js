
const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = {
    entry: {
        'main': './resources/js/index.js',
        'list': './resources/js/list.js'
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, './resources/dist/'),
        publicPath: process.env.NODE_ENV === 'development' ? '//localhost:8080/' : '/'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.s[ac]ss|\.css$/i,
                use: [
                    'style-loader',
                    'vue-style-loader',
                    {loader: 'css-loader', options: { importLoaders: 1 }},
                    'sass-loader'
                ]
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        babelrc: false,
                        presets: [
                            ['@babel/preset-env', {
                                useBuiltIns: 'usage',
                            }]
                        ]
                    }
                }
            }
        ]
    },
    resolve: {
        alias: {
            'vue$': process.env.NODE_ENV === 'development' ? 'vue/dist/vue.runtime.js' : 'vue/dist/vue.runtime.min.js',
        },
        extensions: ['*', '.js', '.vue', '.json']
    },
    plugins: [
        new CleanWebpackPlugin(),
        new VueLoaderPlugin(),
        new ManifestPlugin({
            writeToFileEmit: true
        })
    ]
}
