var pi; // this is necessary
pi = pi || {};
pi.wrap = pi.wrap || {};
pi.wrap.vfs = pi.wrap.vfs || {};
pi.wrap.vfs.createDirRecur = function () {

}

pi.file = pi.file || {};
pi.file.write = function (path, content, encoding) {
    localStorage[path] = content;
}

pi.file.exists = function (path) {
    return localStorage.hasOwnProperty(path);
}

pi.file.read = function (path) {
    return localStorage[path];
}

pi.log = function (s) {
    console.log(s);
}
