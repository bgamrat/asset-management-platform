var Encore = require('@symfony/webpack-encore');
var VueLoader = require('vue-loader')
var ExtractTextPlugin = require("extract-text-webpack-plugin")

Encore
        // the project directory where compiled assets will be stored
        .setOutputPath('public/build/')
        // the public path used by the web server to access the previous directory
        .setPublicPath('/build')
        .cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
        .addEntry('app', './assets/app.js')
        //.addEntry('calendar', './assets/user/calendar.js')
        // uncomment to create hashed filenames (e.g. app.abc123.css)
        .enableVersioning(Encore.isProduction())
        .enableVueLoader()
        .enableSassLoader()
        ;
;
module.exports = Encore.getWebpackConfig(
        {
            mode: 'development',
            devtool: 'inline-source-map',
            plugins: [
                // ... Vue Loader plugin omitted
                new ExtractTextPlugin("style.css")
            ],
            module: {
                rules: [
                    {
                        test: /\.vue$/,
                        loader: 'vue-loader',
                        options: {
                            preserveWhitespace: false,
                            loaders: {
                                css: ExtractTextPlugin.extract({
                                    use: [
                                        {
                                            loader: 'css-loader',
                                        }
                                    ],
                                    fallback: 'vue-style-loader'
                                }),
                                scss: ExtractTextPlugin.extract({
                                    use: [
                                        {
                                            loader: 'scss-loader',
                                        }
                                    ],
                                    fallback: 'vue-style-loader'
                                })
                            }
                        },
                        exclude: /node_modules/
                    },
                    // this will apply to both plain `.js` files
                    // AND `<script>` blocks in `.vue` files
                    {
                        test: /\.js$/,
                        loader: 'babel-loader'
                    }
                ]
            }

        });
