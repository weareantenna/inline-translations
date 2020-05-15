
const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
    entry: './resources/js/index.js',
    output: {
        filename: 'translations.js',
        path: path.resolve(__dirname, './resources/dist'),
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.s[ac]ss$/i,
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
        new VueLoaderPlugin()
    ]
}
