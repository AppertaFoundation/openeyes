import { AlloyEvents, Button, Memento, NativeEvents, Sketcher } from '@ephox/alloy';
import { BlobConversions } from '@ephox/imagetools';
import { Id, Option } from '@ephox/katamari';

import * as UiDomFactory from '../util/UiDomFactory';

const addImage = function (editor, blob) {
  BlobConversions.blobToBase64(blob).then(function (base64) {
    editor.undoManager.transact(function () {
      const cache = editor.editorUpload.blobCache;
      const info = cache.create(
        Id.generate('mceu'), blob, base64
      );
      cache.add(info);
      const img = editor.dom.createHTML('img', {
        src: info.blobUri()
      });
      editor.insertContent(img);
    });
  });
};

const extractBlob = function (simulatedEvent) {
  const event = simulatedEvent.event();
  const files = event.raw().target.files || event.raw().dataTransfer.files;
  return Option.from(files[0]);
};

const sketch = function (editor): Sketcher.SketchSpec {
  const pickerDom = {
    tag: 'input',
    attributes: { accept: 'image/*', type: 'file', title: '' },
     // Visibility hidden so that it cannot be seen, and position absolute so that it doesn't
    // disrupt the layout
    styles: { visibility: 'hidden', position: 'absolute' }
  };

  const memPicker = Memento.record({
    dom: pickerDom,
    events: AlloyEvents.derive([
      // Stop the event firing again at the button level
      AlloyEvents.cutter(NativeEvents.click()),

      AlloyEvents.run(NativeEvents.change(), function (picker, simulatedEvent) {
        extractBlob(simulatedEvent).each(function (blob) {
          addImage(editor, blob);
        });
      })
    ])
  });

  return Button.sketch({
    dom: UiDomFactory.dom('<span class="${prefix}-toolbar-button ${prefix}-icon-image ${prefix}-icon"></span>'),
    components: [
      memPicker.asSpec()
    ],
    action (button) {
      const picker = memPicker.get(button);
      // Trigger a dom click for the file input
      picker.element().dom().click();
    }
  });
};

export {
  sketch
};