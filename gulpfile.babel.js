import gulp from 'gulp';
import fs from 'fs';
import sass from 'gulp-sass';
import uglify from 'gulp-uglify';
import composer from 'gulp-composer';
import rsync from 'gulp-rsync';
import shell from 'gulp-shell';
import autoprefixer from 'gulp-autoprefixer';
import sourcemaps from 'gulp-sourcemaps';
import moduleImporter from 'sass-module-importer';
import babel from 'gulp-babel';

//************ JS ************//

gulp.task('compile-js:dev', () => {
    gulp.src('./src/resources/js/**/*.js')
        .pipe(sourcemaps.init())
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./src/web/static/js/'));
});

gulp.task('compile-js', () => {
    gulp.src('./src/resources/js/**/*.js')
        .pipe(babel({
            presets: ['es2015']
        }))
        .pipe(uglify())
        .pipe(gulp.dest('./src/web/static/js/'));
});

//************ CSS ************//

gulp.task('compile-css:dev', () => {
    gulp.src('./src/resources/css/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({importer: moduleImporter()}).on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./src/web/static/css/'));
});

gulp.task('compile-css', () => {
    gulp.src('./src/resources/css/**/*.scss')
        .pipe(sass({outputStyle: 'compressed', importer: moduleImporter()}).on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(gulp.dest('./src/web/static/css/'));
});

gulp.task("composer", () => {
    fs.writeFileSync('auth.json', JSON.stringify({
        'github-oauth': {
            'github.com': process.env.GITHUB_TOKEN
        }
    }));
    composer('install --no-dev', {async: false, "working-dir": "./"});
});

gulp.task('push-to-server', () => {
    gulp.src('./src/')
        .pipe(rsync({
            root: './src/',
            hostname: process.env.SSH_IP,
            username: process.env.SSH_USERNAME,
            destination: process.env.SSH_DESTINATION,
            recursive: true,
            emptyDirectories: true,
            clean: true,
            silent: true,
            exclude: [
                '.babelrc',
                '.git',
                '.env',
                '.env-example',
                '.bower.json',
                'composer.*',
                'gulpfile.js',
                'package.json',
                'README.MD',
                '*.scss',
                './**/.cass-cache',
                'node_modules/**',
                'web/static/uploads/*',
                'web/static/uploads/**/*',
                'web/static/user_files/*',
                'web/static/user_files/**/*'
            ]
        }));
});

gulp.task('migrate', shell.task([
    'ssh ' + process.env.SSH_USERNAME + '@' + process.env.SSH_IP + ' "cd ' + process.env.SSH_DESTINATION + ' && php yii migrate --interactive=0"'
]));

gulp.task('migrate-docker', shell.task([
    'ssh ' + process.env.SSH_USERNAME + '@' + process.env.SSH_IP + ' "cd ' + process.env.DOCKER_DESTINATION + ' && docker exec ' + process.env.DOCKER_PHP_NAME + ' php yii migrate --interactive=0"'
]));

gulp.task('backup-database', shell.task([
    'ssh ' + process.env.SSH_USERNAME + '@' + process.env.SSH_IP + ' "backup perform -t database"'
]));

gulp.task('backup-files', shell.task([
    'ssh ' + process.env.SSH_USERNAME + '@' + process.env.SSH_IP + ' "backup perform -t files"'
]));

gulp.task('backup', ['backup-files', 'backup-database']);

gulp.task('build', [
    'compile-js',
    'compile-css',
    'composer'
]);

gulp.task('watch', ['compile-css:dev', 'compile-js:dev'], () => {
    gulp.watch('src/resources/js/**/*.js', ['compile-js:dev']);
    gulp.watch('src/resources/css/**/*.scss', ['compile-css:dev']);
});
