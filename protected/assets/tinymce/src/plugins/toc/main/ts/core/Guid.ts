/**
 * Guid.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

const create = function (prefix) {
  let counter = 0;

  return function () {
    const guid = new Date().getTime().toString(32);
    return prefix + guid + (counter++).toString(32);
  };
};

export default {
  create
};