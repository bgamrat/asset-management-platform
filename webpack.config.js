var Encore = require('@symfony/webpack-encore');

Encore
        // the project directory where compiled assets will be stored
        .setOutputPath('public/build/')
        // the public path used by the web server to access the previous directory
        .setPublicPath('/build')
        //.cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
        .addEntry('app', './assets/app.js')
        //.addEntry('calendar', './assets/user/calendar.js')
        // uncomment to create hashed filenames (e.g. app.abc123.css)
        .enableVersioning(Encore.isProduction())
        .enableVueLoader()
        .enableSassLoader()
        ;

module.exports = Encore.getWebpackConfig(
        {
            devtool: 'inline-source-map',
            devServer: {
                contentBase: './dist'
            },
            parser: 'sugarss',
            plugins: {
                'postcss-import': {},
                'postcss-preset-env': {},
                'cssnano': {}
            },
            module: {
                rules:
                        {
                            test: /\.s?css$/,
                            use: [
                                'vue-style-loader',
                                {loader: 'css-loader', options: {modules: true, importLoaders: 1}},
                                'postcss-loader',
                                {
                                    loader: 'sass-loader',
                                    options: {
                                        indentedSyntax: true
                                    }
                                }
                            ]
                        },

            }
        });
