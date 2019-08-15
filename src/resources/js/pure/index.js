window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    window.Popper = require('popper.js').default;

    require('bootstrap');

    require('jquery-ui');

    window.Cropper = require('cropperjs').default;

    window.Dropzone = require('dropzone');

    require('./file-manager');

    require('./stand-alone-button');
} catch (e) {}
