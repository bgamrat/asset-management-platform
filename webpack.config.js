var Encore = require('@symfony/webpack-encore');

Encore
        // the project directory where compiled assets will be stored
        .setOutputPath('public/build/')
        // the public path used by the web server to access the previous directory
        .setPublicPath('/build')
        .cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
        .addEntry('js/app', './assets/js/app.js')
        // uncomment to create hashed filenames (e.g. app.abc123.css)
        //.enableVersioning(Encore.isProduction())
        .enableVueLoader()
        //.enableSassLoader()
        ;

module.exports = Encore.getWebpackConfig(
        {
            devtool: 'inline-source-map',
            devServer: {
                contentBase: './dist'
            },
            module: {
                rules: [
                    {
                        test: /\.css$/,
                        use: [
                            'style-loader',
                            'css-loader'
                        ]
                    }
                ]

            }
        });