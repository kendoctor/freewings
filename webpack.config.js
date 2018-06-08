var Encore = require('@symfony/webpack-encore');

var FilewatcherWebpackPlugin = require('filewatcher-webpack-plugin');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
  //  .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/css/admin.scss')
    .addStyleEntry('css/frontend', './assets/css/frontend.scss')
    .addStyleEntry('css/login', './assets/css/login.scss')
    // .addPlugin( new FilewatcherWebpackPlugin({
    //     watchFileRegex: ['./templates/**/*.html.twig'],
    //     usePolling: false
    // }))
    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(sassOptions){}, {
        resolveUrlLoader: false
    })
     // uncomment for legacy applications that require $/jQuery as a global variable
    //.autoProvidejQuery()

;

module.exports = Encore.getWebpackConfig();
