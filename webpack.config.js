var Encore = require('@symfony/webpack-encore');

Encore
        // the project directory where compiled assets will be stored
        .setOutputPath('public/build/')
        // the public path used by the web server to access the previous directory
        .setPublicPath('/build')
        .cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
// uncomment to create hashed filenames (e.g. app.abc123.css)
// .enableVersioning(Encore.isProduction())


        .addStyleEntry('css/dojo_dijit',
                ['./assets/vendor/dojo/dojo/resources/dojo.css',
                    './assets/vendor/dojo/dojo/resources/dnd.css',
                    './assets/vendor/dojo/dijit/themes/tundra/tundra.css'])
        .addStyleEntry('css/dgrid', [
            './assets/vendor/dojo/dgrid/css/dgrid.css',
            './assets/vendor/dojo/dgrid/css/skins/tundra.css'])
        .addStyleEntry('css/admin',
                [   './assets/app/css/admin/common.css',
                    './assets/app/css/admin/admin.css',
                    './assets/app/css/admin/user.css'])
        .addStyleEntry('css/admin_asset',
                './assets/app/css/admin/asset.css')
        .addStyleEntry('css/admin_client',
                ['./assets/app/css/admin/client.css',
                    './assets/app/css/common/common.css'])
        .addStyleEntry('css/admin_schedule',
                './assets/app/css/admin/schedule.css')
        .addStyleEntry('css/admin_venue_css',
                './assets/app/css/admin/venue.css')
        // User side - stock Bootstrap
        .createSharedEntry('css/bootstrap',
                ['./assets/vendor/bootstrap/dist/css/bootstrap.min.css',
                    './assets/vendor/font-awesome/css/font-awesome.min.css'])
        // User side - common CSS
        .addStyleEntry('css/main', './assets/app/css/user/common.css')
        .addStyleEntry('css/event', [
            './assets/app/css/common/common.css',
            './assets/app/css/common/event.css'
        ])


        // uncomment if you use Sass / SCSS files
        // .enableSassLoader()

        // uncomment for legacy applications that require $/jQuery as a global variable
        // .autoProvidejQuery()
        ;

module.exports = Encore.getWebpackConfig(
        {
            devtool: 'inline-source-map',
            devServer: {
                contentBase: './dist'
            }
        }
);
