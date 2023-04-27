# gulp-dependencies

> A gulp plugin for handling dependencies between files

Scans files in the stream and keeps a map of which files are included by others. Then works out which files needs to be rebuilt taking this into account. Kind of like a more involved version of gulp-changed. Useful when watching files.

Can also be used for other things!

### **Install**
```
$ npm install --save-dev gulp-dependencies
```

### **Usage**
```
var dependencies = require('gulp-dependencies');
...
.pipe dependencies( [ options ] )
```
**javascript**
```js
var gulp = require('gulp');
var sass = require("gulp-sass");
var dependencies = require('gulp-dependencies');

gulp.task('watch', function() {
  return gulp.watch("*.scss", [ 'build' ]);
});

gulp.task('build', function() {

  return gulp.src("*.scss")

    // work out which files need rebuilding

    .pipe(dependencies({

      // extract 'imports' and append '.scss'
      match  : /@import\s+'(.+)'/g,
      replace: function(f) { return f + ".scss"; },

      // destination and extension for output files
      dest   : "css",
      ext    : ".css"
    }))

  // files are now filtered so build them

  .pipe(sass())
  .pipe(gulp.dest("css"));

});
```
**coffeescript**

```coffeescript
gulp            = require 'gulp'
sass            = require "gulp-sass"
dependencies    = require 'gulp-dependencies'

gulp.task 'watch', -> gulp.watch "*.scss", [ 'build' ]

gulp.task 'build', ->

  gulp.src "*.scss"

    # work out which files need rebuilding
    .pipe dependencies

      # extract 'imports' and append '.scss'
      match     : /@import\s+'(.+)'/g
      replace   : (f) -> "#{f}.scss"

      # destination and extension for output files
      dest      : "css"
      ext       : ".css"

    # files are now filtered so build them
    .pipe sass()
    .pipe gulp.dest "css"
```

### **Caveats**

Files need to have been processed at least once before they are included in the dependency map.

This is work in progress :)

### **Options**

Lots off (too many?) options ...

#### Basic options
- *match [ regex ]*

  A javascript regular expression used to extract included files. The first capture group is used to extract the filename

  ```
  /@require\s+(.+)\b/g
  ```

- *replace [ function : optional, default null ]*

   If exists, can be used to further process the captured string. For example, add file extensions where they are inferred by the file format.

   ```
   function(f) { return f + ".scss"; }
   ```

- *dest [ string or function ]*

   The destination directory for the output files. This is used to compare source and target file modification times to see if they need rebuilding. In function form, the current file is passed in and expects the directory as a string in return.

   ```
   function(f) { return "output/dir"; }
   ```

- *ext [ string or function ]*

   The destination file extension for the output files. This is used to compare source and target file modification times to see if they need rebuilding. In function form, the current file is passed in and expects the file extension as a string in return.

   ```
   function(f) { return ".ext"; }
   ```

#### Directories
- *dependencies_file [ string : default './dependencies.json' ]*

   Dependencies are stored in a json file, by default in the current working directory. You can override this if required.

- *basepath [ string : default 'current working directory' ]*

   Dependencies are stored relative to a base directory. By default this is the current working directory, however you can override this. If you want to store absolute paths you can use '/' as the base path.

#### Custom Control
- *override [ object ]*

   You can provide custom handling for given file types. For any given extension, you can specify custom match and replace options, which will override the default values.

 Additionally you can add a "remove" property that will remove files of that type from the stream. Useful in the case of required files that don't in themselves produce an output file (such as ruby files included by haml files, header files in C++ etc).

   ```
   override:
   {
      ".h":
      {
        match: /#include\s+"(.+)"/g,
        replace: null,
        remove: true
      }
   }
   ```

- *insert_dependents [ bool : default true ]*

   Dependent files will be added to the stream if required (because an included file has changed). This is normally only necessary if also using with another plugin such as gulp-changed that has previously filtered the file list.

- *insert_included [ bool : default false ]*

   Add included files into the file stream. For example, if you wanted to concatenate them all together - e.g. with gulp-concat.

- *order_dependencies [ bool : default true ]*

   By default the files will be ordered such that "parent" files are before "children". If the order of the files in the stream is important, this can be turned off - any additional files added will be at the end of the stream.

- *recursive [ bool : default true ]*

   By default the plugin recursively searches all included files to update the dependency map. This can be turned off. However, note that adding 'insert_included' implies recursion.

#### Advanced
- *clean [ bool : default false ]*

   If true the plugin will create a 'fresh' version of the dependency map and forget any previous values.

- *save [ bool : default true ]*

   If false the dependency map will not be saved

- *debug [ bool : default false ]*

   Turns on debug ouput

- *update [ bool : default true ]*

   If false, files will not be scanned for updated dependencies

- *error : [ function ]*

   Override the default error handling, e.g.

   ```
   function(message) { throw message; }
   ```

- *warn : [ function ]*

   Override the default warning (non-fatal error) handler, e.g.

   ```
   function(message) { throw message; }
   ```

- *print : [ function ]*

   Override the debug output handler (when debug option is set to true).

   ```
   function(message) { console.log( message ); }
   ```

- *xfs: [ object ]*

   Override the file I/O. The plugin requires three file I/O functions, which can be redirected if required (for example, using a mock interface for the test suite). Default implementation below.

   ```
   xfs: {
         read: function(path) { return fs.readFileSync(path); },
         write: function(path, data) { return fs.writeFileSync(path, data); },
         modified: function(path) { return fs.statSync(path).mtime; }
   }
   ```

### Examples
- See examples folder

### TODO

 - Add support for file globs, so it is easier to include multiple files in one go

### Release History

* 0.1.0 First version

