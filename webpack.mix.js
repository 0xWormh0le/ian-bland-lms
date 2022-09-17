let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass('resources/assets/sass/login.scss', 'public/css/login.css');

mix.styles([
  'public/css/custom.css',
  'public/css/login.css'
], 'public/css/build.min.css');

mix.js('resources/assets/js/scripts/dashboard/client.js', 'public/scripts/dashboard')
  .js('resources/assets/js/scripts/dashboard/learner.js', 'public/scripts/dashboard')
  .js('resources/assets/js/scripts/roles/index.js', 'public/scripts/roles')
  .js('resources/assets/js/scripts/teams/index.js', 'public/scripts/teams')
  .js('resources/assets/js/scripts/users/index.js', 'public/scripts/users')
  .js('resources/assets/js/scripts/users/import.js', 'public/scripts/users')
  .js('resources/assets/js/scripts/users/course_details.js', 'public/scripts/users')
  .js('resources/assets/js/scripts/email-setup/index.js', 'public/scripts/email-setup')
  .js('resources/assets/js/scripts/sysconfig/smtp-account.js', 'public/scripts/sysconfig')
  .js('resources/assets/js/scripts/my-courses/index.js', 'public/scripts/my-courses')
  .js('resources/assets/js/scripts/my-courses/details.js', 'public/scripts/my-courses')
  .js('resources/assets/js/scripts/reports/index.js', 'public/scripts/reports')
  .js('resources/assets/js/scripts/reports/log.js', 'public/scripts/reports')
  .js('resources/assets/js/scripts/tickets/index.js', 'public/scripts/tickets')
  .js('resources/assets/js/scripts/tickets/chat.js', 'public/scripts/tickets')
  .js('resources/assets/js/scripts/courses/index.js', 'public/scripts/courses')
  .js('resources/assets/js/scripts/reports/report.js', 'public/scripts/reports')
  .version();
