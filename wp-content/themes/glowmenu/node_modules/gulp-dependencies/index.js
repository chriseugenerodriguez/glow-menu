var PLUGIN_NAME, PluginError, _, fs, invocation_count, path, through, util;

_ = require('lodash');

path = require('path');

util = require('gulp-util');

through = require('through2');

fs = require('fs');

PluginError = util.PluginError;

PLUGIN_NAME = 'gulp-dependencies';

invocation_count = 0;

module.exports = function(options) {
  var AddDependencies, AddDependents, AddToQueue, CompareChanged, Flush, HasChanged, Load, Output, Parse, Transform, config, defaults, dependencies, dependents, error, filemap, invocation, print, queue, relative, warn, xfs;
  defaults = {
    match: /@require\s+(.+)\b/g,
    replace: null,
    override: {},
    dest: null,
    ext: null,
    dependencies_file: "./dependencies.json",
    basepath: process.cwd(),
    insert_dependents: true,
    insert_included: false,
    order_dependencies: true,
    recursive: true,
    clean: false,
    save: true,
    debug: false,
    update: true,
    error: function(msg) {
      return util.log("[" + invocation + "] " + (util.colors.green(PLUGIN_NAME)) + " " + (util.colors.red(msg)));
    },
    warn: function(msg) {
      return util.log("[" + invocation + "] " + (util.colors.green(PLUGIN_NAME)) + " " + (util.colors.red('WARNING: ' + msg)));
    },
    print: function(msg) {
      if (config.debug) {
        return util.log("[" + invocation + "] " + (util.colors.green(PLUGIN_NAME)) + " " + msg);
      }
    },
    xfs: {
      read: function(path) {
        return fs.readFileSync(path);
      },
      write: function(path, data) {
        return fs.writeFileSync(path, data);
      },
      modified: function(path) {
        return fs.statSync(path).mtime;
      }
    }
  };
  config = _.defaults(options, defaults);
  print = config.print;
  error = config.error;
  warn = config.warn;
  xfs = config.xfs;
  relative = function(file) {
    return path.relative(config.basepath, file);
  };
  dependencies = {};
  dependents = {};
  queue = [];
  filemap = {};
  invocation = ++invocation_count;
  if (config.dest == null) {
    warn("no destination directory - assuming all files have changed");
  }
  AddToQueue = function(file, addToStream, addDependents) {
    var e;
    try {
      filemap[file.path] = {
        file: file,
        path: path.relative(config.basepath, file.path),
        included: false,
        addToStream: addToStream,
        addDependents: addDependents,
        mtime: xfs.modified(file.path)
      };
      return queue.push(file);
    } catch (_error) {
      e = _error;
      return error(e);
    }
  };
  Load = function(path, addToStream, addDependents, message, parentFile) {
    var e, file;
    if (path in filemap) {
      return;
    }
    try {
      file = new util.File({
        path: path,
        contents: new Buffer(xfs.read(path))
      });
      AddToQueue(file, addToStream, addDependents);
      return print("added " + (util.colors.yellow(relative(path))) + " " + message + " " + (util.colors.cyan(relative(parentFile.path))));
    } catch (_error) {
      e = _error;
      return error(e);
    }
  };
  CompareChanged = function(source, mtime) {
    var dependency, dependency_path, fd, i, len, ref;
    if (!(source in filemap)) {
      return true;
    }
    fd = filemap[source];
    if (fd.mtime > mtime) {
      return true;
    }
    ref = dependencies[fd.path];
    for (i = 0, len = ref.length; i < len; i++) {
      dependency = ref[i];
      dependency_path = path.resolve(path.join(config.basepath, dependency));
      if (CompareChanged(dependency_path, mtime)) {
        return true;
      }
    }
    return false;
  };
  HasChanged = function(file) {
    var dest, ext, mtime;
    if (config.dest == null) {
      return true;
    }
    mtime = null;
    dest = typeof config.dest === 'function' ? config.dest(file) : config.dest;
    dest = path.resolve(process.cwd(), dest, file.relative);
    if (config.ext != null) {
      ext = typeof config.ext === 'function' ? config.ext(file) : config.ext;
      dest = util.replaceExtension(dest, ext);
    }
    try {
      mtime = xfs.modified(dest);
    } catch (_error) {
      return true;
    }
    return CompareChanged(file.path, mtime);
  };
  Output = function(stream, file) {
    var dependency, dependency_path, fd, i, len, ref;
    fd = filemap[file.path];
    if (fd.included) {
      return;
    }
    fd.included = true;
    if (config.order_dependencies) {
      ref = dependencies[fd.path];
      for (i = 0, len = ref.length; i < len; i++) {
        dependency = ref[i];
        dependency_path = path.resolve(path.join(config.basepath, dependency));
        if (dependency_path in filemap) {
          Output(stream, filemap[dependency_path].file);
        }
      }
    }
    if (fd.addToStream) {
      if (HasChanged(file)) {
        print("output " + (util.colors.yellow(relative(file.path))));
        return stream.push(file);
      } else {
        return print("skipping " + (util.colors.yellow(relative(file.path))) + " (not changed)");
      }
    } else {
      return print("ignore " + (util.colors.yellow(relative(file.path))));
    }
  };
  AddDependents = function(file) {
    var dependent, dependent_path, fd, i, len, ref, results;
    fd = filemap[file.path];
    if (!(fd.addDependents && fd.path in dependents)) {
      return;
    }
    ref = dependents[fd.path];
    results = [];
    for (i = 0, len = ref.length; i < len; i++) {
      dependent = ref[i];
      dependent_path = path.resolve(path.join(config.basepath, dependent));
      results.push(Load(dependent_path, true, true, "dependent on", file));
    }
    return results;
  };
  AddDependencies = function(file) {
    var dependency, dependency_path, fd, i, len, ref, results;
    if (!(config.insert_included || config.recursive)) {
      return;
    }
    fd = filemap[file.path];
    ref = dependencies[fd.path];
    results = [];
    for (i = 0, len = ref.length; i < len; i++) {
      dependency = ref[i];
      dependency_path = path.resolve(path.join(config.basepath, dependency));
      results.push(Load(dependency_path, config.insert_included, false, "required by", file));
    }
    return results;
  };
  Parse = function(file) {
    var capture, custom, ext, fd, include, include_path, include_rel, match, replace, results;
    if (!config.update) {
      return;
    }
    fd = filemap[file.path];
    dependencies[fd.path] = [];
    match = config.match;
    replace = config.replace;
    ext = path.extname(file.path).toLowerCase();
    if ((config.override != null) && ext in config.override) {
      custom = config.override[ext];
      if ('match' in custom) {
        match = custom.match;
        replace = custom.replace;
      } else if ('replace' in custom) {
        replace = custom.replace;
      }
      if ('remove' in custom) {
        fd.addToStream = !custom.remove;
      }
    }
    if (match != null) {
      print("parse " + (util.colors.cyan(relative(file.path))));
      results = [];
      while (capture = match.exec(file.contents.toString('utf8'))) {
        include = capture[1];
        if (replace != null) {
          include = replace(include);
        }
        include_path = path.resolve(path.join(path.dirname(file.path), include));
        include_rel = relative(include_path);
        if (!_.contains(dependencies[fd.path], include_rel)) {
          dependencies[fd.path].push(include_rel);
          results.push(print((util.colors.cyan(relative(file.path))) + " requires " + (util.colors.cyan(include_rel))));
        } else {
          results.push(void 0);
        }
      }
      return results;
    } else {
      return print("skipping " + (relative(file.path)) + " - no match");
    }
  };
  Transform = function(file, unused, cb) {
    if (file.isNull()) {
      this.push(file);
    } else if (file.isStream()) {
      return error("Streaming not supported");
    } else {
      print("input " + util.colors.yellow(relative(file.path)));
      AddToQueue(file, true, config.insert_dependents);
    }
    return cb();
  };
  Flush = function(cb) {
    var file, i, include, includes, j, len, len1, stream;
    if (!config.clean) {
      try {
        print("read " + util.colors.cyan(config.dependencies_file));
        dependencies = JSON.parse(xfs.read(config.dependencies_file));
        for (file in dependencies) {
          includes = dependencies[file];
          for (i = 0, len = includes.length; i < len; i++) {
            include = includes[i];
            if (dependents[include] == null) {
              dependents[include] = [];
            }
            dependents[include].push(file);
          }
        }
      } catch (_error) {
        print(util.colors.red("failed to read ") + util.colors.cyan(config.dependencies_file));
      }
    }
    stream = [];
    while (queue.length > 0) {
      file = queue.pop();
      Parse(file);
      AddDependents(file);
      AddDependencies(file);
      stream.push(file);
    }
    for (j = 0, len1 = stream.length; j < len1; j++) {
      file = stream[j];
      Output(this, file);
    }
    if (config.save) {
      print("save " + util.colors.cyan(config.dependencies_file));
      xfs.write(config.dependencies_file, JSON.stringify(dependencies, null, 4));
    }
    return cb();
  };
  return through.obj(Transform, Flush);
};
