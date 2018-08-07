/**
 * Plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

import Actions from './Actions';

const setup = function (editor, imageUploadTimerState, lastSelectedImageState) {
  editor.on('NodeChange', function (e) {
    const lastSelectedImage = lastSelectedImageState.get();

    // If the last node we selected was an image
    // And had a source that doesn't match the current blob url
    // We need to attempt to upload it
    if (lastSelectedImage && lastSelectedImage.src !== e.element.src) {
      Actions.cancelTimedUpload(imageUploadTimerState);
      editor.editorUpload.uploadImagesAuto();
      lastSelectedImageState.set(null);
    }

    // Set up the lastSelectedImage
    if (Actions.isEditableImage(editor, e.element)) {
      lastSelectedImageState.set(e.element);
    }
  });
};

export default {
  setup
};