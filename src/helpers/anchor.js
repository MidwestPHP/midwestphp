module.exports = function(options) {
    let anchor = options.fn(this);
    return anchor.split(' ').join('-').toLowerCase();
};
