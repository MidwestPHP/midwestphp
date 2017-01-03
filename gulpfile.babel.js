'use strict';

import plugins from "gulp-load-plugins";
import yargs from "yargs";
import browser from "browser-sync";
import gulp from "gulp";
import panini from "panini";
import rimraf from "rimraf";
import sherpa from "style-sherpa";
import yaml from "js-yaml";
import fs from "fs";

// Load all Gulp plugins into one variable
const $ = plugins();

// Check for --production flag
const PRODUCTION = !!(yargs.argv.production);

// Load settings from settings.yml
const {COMPATIBILITY, PORT, UNCSS_OPTIONS, PATHS} = loadConfig();

function loadConfig() {
    let ymlFile = fs.readFileSync('config.yml', 'utf8');
    return yaml.load(ymlFile);
}

// Build the "dist" folder by running all of the below tasks
gulp.task('build',
    gulp.series(clean, gulp.parallel(pages, sass, javascript, images, fonts, copy)));

// Build the site, run the server, and watch for file changes
gulp.task('default',
    gulp.series('build', server, watch));

// Delete the "dist" folder
// This happens every time a build starts
function clean(done) {
    rimraf(PATHS.dist, done);
}

// Copy files out of the assets folder
// This task skips over the "img", "js", and "scss" folders, which are parsed separately
function copy() {
    return gulp.src(PATHS.assets)
        .pipe(gulp.dest(PATHS.dist + '/assets'));
}

// Copy page templates into finished HTML files
function pages() {
    // speakers();
    panini.refresh();
    return gulp.src('src/pages/**/*.{html,php,hbs,handlebars}')
        .pipe(panini({
            root: 'src/pages/',
            layouts: 'src/layouts/',
            partials: 'src/partials/',
            data: 'src/data/**/',
            helpers: 'src/helpers/'
        }))
        .pipe(gulp.dest(PATHS.dist));
}

function speakers() {
    let ymlFile = fs.readFileSync('src/data/speakers.yml', 'utf8');
    let yamlData = yaml.load(ymlFile);
    rmDir('src/data/speakers', false);
    yamlData.forEach(function (data) {
        // let buffer = new Buffer(data);
        fs.open("src/data/speakers/" + data.speaker.replace(" ", "-").toLowerCase() + '.json', 'wx', (err, fd) => {
            if (err) {
                if (err.code === "EEXIST") {
                    console.error('myfile already exists');
                    return;
                } else {
                    throw err;
                }
            }
            fs.write(fd, JSON.stringify(data), function(err) {
                if (err) throw 'error writing file: ' + err;
                fs.close(fd, function() {
                    // console.log('file written');
                })
            });
        });
    });

    // return gulp.src('src/pages/speakers/*.{html,php,hbs,handlebars}')
    //     .pipe(panini({
    //         root: 'src/pages/speakers/',
    //         layouts: 'src/layouts/',
    //         partials: 'src/partials/',
    //         data: 'src/speakers/',
    //         helpers: 'src/helpers/'
    //     }))
    //     .pipe(gulp.dest(PATHS.dist));
}

// Load updated HTML templates and partials into Panini
function resetPages(done) {
    panini.refresh();
    done();
}

// Generate a style guide from the Markdown content and HTML template in styleguide/
function styleGuide(done) {
    sherpa('src/styleguide/index.md', {
        output: PATHS.dist + '/styleguide.html',
        template: 'src/styleguide/template.html'
    }, done);
}

// Compile Sass into CSS
// In production, the CSS is compressed
function sass() {
    return gulp.src('src/assets/scss/app.scss')
        .pipe($.sourcemaps.init())
        .pipe($.sass({
            includePaths: PATHS.sass
        })
            .on('error', $.sass.logError))
        .pipe($.autoprefixer({
            browsers: COMPATIBILITY
        }))
        // Comment in the pipe below to run UnCSS in production
        // .pipe($.if(PRODUCTION, $.uncss(UNCSS_OPTIONS)))
        .pipe($.if(PRODUCTION, $.cssnano()))
        .pipe($.if(!PRODUCTION, $.sourcemaps.write()))
        .pipe(gulp.dest(PATHS.dist + '/assets/css'))
        .pipe(browser.reload({stream: true}));
}

// Combine JavaScript into one file
// In production, the file is minified
function javascript() {
    return gulp.src(PATHS.javascript)
        .pipe($.sourcemaps.init())
        .pipe($.babel())
        .pipe($.concat('app.js'))
        .pipe($.if(PRODUCTION, $.uglify()
            .on('error', e => {
                console.log(e);
            })
        ))
        .pipe($.if(!PRODUCTION, $.sourcemaps.write()))
        .pipe(gulp.dest(PATHS.dist + '/assets/js'));
}

// Copy images to the "dist" folder
// In production, the images are compressed
function images() {
    return gulp.src('src/assets/img/**/*')
        .pipe($.if(PRODUCTION, $.imagemin({
            progressive: true
        })))
        .pipe(gulp.dest(PATHS.dist + '/assets/img'));
}

// Copy images to the "dist" folder
// In production, the images are compressed
function fonts() {
    return gulp.src('src/assets/fonts/**/*')
        .pipe(gulp.dest(PATHS.dist + '/assets/fonts'));
}

// Start a server with BrowserSync to preview the site in
function server(done) {
    browser.init({
        server: PATHS.dist, port: PORT
    });
    done();
}

// Reload the browser with BrowserSync
function reload(done) {
    browser.reload();
    done();
}

// Watch for changes to static assets, pages, Sass, and JavaScript
function watch() {
    gulp.watch(PATHS.assets, copy);
    gulp.watch('src/data/**').on('all', gulp.series(pages, browser.reload));
    gulp.watch('src/pages/**/*.html').on('all', gulp.series(pages, browser.reload));
    gulp.watch('src/{layouts,partials}/**/*.html').on('all', gulp.series(resetPages, pages, browser.reload));
    gulp.watch('src/assets/scss/**/*.scss').on('all', gulp.series(sass, browser.reload));
    gulp.watch('src/assets/js/**/*.js').on('all', gulp.series(javascript, browser.reload));
    gulp.watch('src/assets/img/**/*').on('all', gulp.series(images, browser.reload));
    gulp.watch('src/assets/fonts/**/*').on('all', gulp.series(fonts, browser.reload));
    // gulp.watch('src/styleguide/**').on('all', gulp.series(styleGuide, browser.reload));
}

let rmDir = function(dirPath, removeSelf) {
    if (removeSelf === undefined)
        removeSelf = true;
    try { var files = fs.readdirSync(dirPath); }
    catch(e) { return; }
    if (files.length > 0)
        for (var i = 0; i < files.length; i++) {
            var filePath = dirPath + '/' + files[i];
            if (fs.statSync(filePath).isFile())
                fs.unlinkSync(filePath);
            else
                rmDir(filePath);
        }
    if (removeSelf)
        fs.rmdirSync(dirPath);
};
