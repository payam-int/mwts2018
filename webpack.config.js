var Encore = require('@symfony/webpack-encore');
Encore
// the project directory where compiled assets will be stored
    .setOutputPath('public/assets/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/assets')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    // uncomment to define the assets of the project
    // .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/login', './assets/css/login.sass')
    .addStyleEntry('css/register', './assets/css/register.sass')
    .addStyleEntry('css/base', './assets/css/base.sass')
    .addEntry('js/assets', './assets/js/assets.js')


// uncomment if you use Sass/SCSS files


// uncomment for legacy applications that require $/jQuery as a global variable
// .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
