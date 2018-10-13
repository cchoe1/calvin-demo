let gulp = require('gulp');
let sass = require('gulp-sass');

function swallowError (error) {

  console.log(error.toString())

  this.emit('end')
}

gulp.task('sass', function(){
  return gulp.src('scss/calvin_theme.scss')
    .pipe(sass()) // Converts Sass to CSS with gulp-sass
    .on('error', swallowError)
    .pipe(gulp.dest('css/'))
});

gulp.task('watch', function(){
  gulp.watch('scss/**/*.scss', ['sass']);
});

