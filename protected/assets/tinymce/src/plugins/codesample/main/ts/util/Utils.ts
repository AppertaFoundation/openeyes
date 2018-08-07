/**
 * Utils.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

function isCodeSample(elm) {
  return elm && elm.nodeName === 'PRE' && elm.className.indexOf('language-') !== -1;
}

function trimArg(predicateFn) {
  return function (arg1, arg2) {
    return predicateFn(arg2);
  };
}

export default {
  isCodeSample,
  trimArg
};