var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var uglify = require('gulp-uglify');
var buffer = require('vinyl-buffer');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var concatCss = require('gulp-concat-css');
var cleanCSS = require('gulp-clean-css');
var gzip = require('gulp-gzip');
var concat = require('gulp-concat');
var rename = require('gulp-rename');

const vendors = [
  'jquery',
  //'backbone',
  'underscore',
  'twig',
  'gsap/TweenMax',
  //'gsap/AttrPlugin',
  'gsap/ScrollToPlugin',
  //'@vimeo/player',
  //'bootstrap',
  //'bootstrap-select',
  'smooth-scrolling',
  'slick-carousel'
];

//Vendor
gulp.task('pre_vendor', () => {

  const b = browserify();

  vendors.forEach(lib => {
    b.require(lib);
  });

  return b.bundle()
    .pipe(source('./js/vendor/vendor1.js'))
    .pipe(buffer())
    .pipe(uglify().on('error', function(e){
            console.log(e);
         }))
    .pipe(gulp.dest(''));
});


gulp.task('vendor', ['pre_vendor'], function() {
  return gulp.src([
    './js/vendor/vendor1.js',
    './js/vendor/modernizr/modernizr-custom.js',
    './js/vendor/greensock/easing/CustomEase.js',
    './js/vendor/validationEngine/jquery.validationEngine.js',
    './js/vendor/validationEngine/languages/jquery.validationEngine-ca.js'
  ])
  .pipe(concat('./dist/vendor.js'))
  .pipe(gulp.dest(''))
  .pipe(gzip())
  .pipe(gulp.dest(''));
});



var app_tasks = ['main_app'];

// App.js
gulp.task('main_app', function () {
  return browserify('./js/app.js')
    .external(vendors)
    .bundle()
    .pipe(source('./dist/bundle.js'))
    .pipe(buffer())
    .pipe(uglify())
    .pipe(gulp.dest(''));
});

// views scripts

var views_scripts = [
  'home_view',
  'gallery_view',
  'about_view',
  'news_view',
  'product_category_view',
  'contact_form'
];


gulp.Gulp.prototype.__runTask = gulp.Gulp.prototype._runTask;
gulp.Gulp.prototype._runTask = function(task) {
  this.currentTask = task;
  this.__runTask(task);
}

for(var i= 0; i < views_scripts.length; i++){

  var task_name = views_scripts[i];
  app_tasks.push(task_name);

  gulp.task(task_name, function () {

      return browserify('./js/views/'+this.currentTask.name+'.js')
        .external(vendors)
        .bundle()
        .pipe(source('./dist/'+this.currentTask.name+'.js'))
        .pipe(buffer())
        .pipe(uglify())
        .pipe(gulp.dest(''));
    });

}

// app task
gulp.task('app',app_tasks);



// Sass + concat + minify
gulp.task('styles', function () {

  return gulp.src('./css/app.scss')
    .pipe(sass({outputStyle: 'compressed'}))
    .pipe(autoprefixer('last 2 version', 'ie 8', 'ie 9'))
    .pipe(concatCss("bundle.css"))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('./dist'))

});


// Admin styles
gulp.task('admin_styles', function () {

  return gulp.src('./css/admin.scss')
    .pipe(sass({outputStyle: 'compressed'}))
    .pipe(autoprefixer('last 2 version', 'ie 8', 'ie 9'))
    .pipe(concatCss("admin.css"))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('./dist'))

});

// Admin login
gulp.task('admin_login', function () {

  return gulp.src('./css/admin_login.scss')
    .pipe(sass({outputStyle: 'compressed'}))
    .pipe(autoprefixer('last 2 version', 'ie 8', 'ie 9'))
    .pipe(concatCss("admin_login.css"))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('./dist'))

});

// admin_editor
gulp.task('admin_editor', function () {

  return gulp.src('./css/admin_editor.scss')
    .pipe(sass({outputStyle: 'compressed'}))
    .pipe(autoprefixer('last 2 version', 'ie 8', 'ie 9'))
    .pipe(concatCss("admin_editor.css"))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('./dist'))

});

// Admin task
gulp.task('admin', [
  'admin_styles',
  'admin_editor',
  'admin_login'
]);


// Watch
gulp.task('watch', function () {
  gulp.watch(['./js/**/*.js'], ['app']);
  gulp.watch(['./css/**/*.scss'], ['styles']);
  gulp.watch(['./css/**/*.scss'], ['admin']);
  return;
});


// Run and watch
gulp.task('default', [
  'app',
  'styles',
  'admin',
  'watch'
]);

// Run + Vendor and watch
gulp.task('full', [
  'vendor',
  'default'
]);
