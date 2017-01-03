module.exports = function (options) {
    let speakerName = options.fn(this);
    return '/speakers/' + speakerName.split(' ').join('-').toLowerCase() + '.html';
};
