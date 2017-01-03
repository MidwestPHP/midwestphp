module.exports = function (options) {
    let session = options.fn(this);
    return '/sessions.html#' + session.split(' ').join('-').toLowerCase();
};
