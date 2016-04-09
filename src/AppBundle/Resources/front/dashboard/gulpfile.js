var gulp = require("gulp"),
	cfg = require("./hrd.config"),

	rename = require("gulp-rename"),
	notify = require("gulp-notify"),

	compileLess = require("gulp-less"),
	autoprefixer = require("gulp-autoprefixer"),
	minifyCSS = require("gulp-minify-css"),
	concatCSS = require("gulp-concat-css"),

	exec = require("child_process").exec,
	glob = require("glob"),

	imagemin = require("gulp-imagemin"),
	pngquant = require("imagemin-pngquant");


gulp.task("js", function(){
	var files = glob.sync("assets/js/app/*.js"),
		fileName, bundled;

	files.map(function(entryFile){
		fileName = entryFile.match(/\w+(?=\.js)/gi);
		bundled = exec("jspm bundle-sfx "+ entryFile +" "+ cfg.buildPath + "js/" + fileName +".bundle.min.js --minify --skip-source-maps");
	});
});

gulp.task("fonts", function(){

	gulp.src(cfg.fontPath + "**/*.*")
		.pipe(gulp.dest(cfg.buildPath + "fonts"))
		.pipe(notify({message: "fonts done", onLast: true}));
});

gulp.task("css", function(){

	var files = glob.sync(cfg.cssPath + "*.less"),
		fileName;

	files.map(function(entryFile){

		fileName = entryFile.match(/\w+(?=\.less)/)[0];

		return gulp.src(entryFile)
			.pipe(compileLess())
			.pipe(concatCSS(fileName + ".less"))
			.pipe(autoprefixer({
				configbrowsers: ["last 4 versions"],
				cascade: true,
				remove: true,
				add: true
			}))
			.pipe(minifyCSS({processImport: false}))
			.pipe(rename({
				basename: fileName,
				extname: ".bundle.min.css"
			}))
			.pipe(gulp.dest(cfg.buildPath + "css"))
			.pipe(notify({message: "css done", onLast: true}));
	});

});

gulp.task("images", function(){

	gulp.src(cfg.imagePath + "**/*.*")
		.pipe(imagemin({
			optimizationLevel: 4,
			progressive: true,
			svgoPlugins: [{removeViewBox: false}],
			use: [ pngquant() ]
		}))
		.pipe(gulp.dest(cfg.buildPath + "images"));
});

gulp.task("watcher", function(){
	gulp.watch(cfg.jsPath + "**/*.js", ["js"]);
	gulp.watch(cfg.cssPath + "**/*.less", ["css"]);
	gulp.watch(cfg.imagePath + "**/*.*", ["images"]);
});

gulp.task("default", ["fonts", "css", "images", "js"]);
