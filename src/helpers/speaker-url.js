module.exports = function (options) {
    let speakerName = options.fn(this);
    let pattern = /&#x27;/i
    speakerName = speakerName.replace(pattern, '');
    return '/speakers/' + speakerName.split(' ').join('-').toLowerCase() + '.html';
};
