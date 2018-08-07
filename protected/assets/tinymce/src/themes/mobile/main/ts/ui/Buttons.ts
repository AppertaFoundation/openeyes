import { Behaviour, Button, Toggling, Unselecting, Sketcher } from '@ephox/alloy';
import { Merger } from '@ephox/katamari';

import Receivers from '../channels/Receivers';
import Styles from '../style/Styles';
import * as UiDomFactory from '../util/UiDomFactory';

const forToolbarCommand = function (editor, command) {
  return forToolbar(command, function () {
    editor.execCommand(command);
  }, { });
};

const getToggleBehaviours = function (command) {
  return Behaviour.derive([
    Toggling.config({
      toggleClass: Styles.resolve('toolbar-button-selected'),
      toggleOnExecute: false,
      aria: {
        mode: 'pressed'
      }
    }),
    Receivers.format(command, function (button, status) {
      const toggle = status ? Toggling.on : Toggling.off;
      toggle(button);
    })
  ]);
};

const forToolbarStateCommand = function (editor, command) {
  const extraBehaviours = getToggleBehaviours(command);

  return forToolbar(command, function () {
    editor.execCommand(command);
  }, extraBehaviours);
};

// The action is not just executing the same command
const forToolbarStateAction = function (editor, clazz, command, action) {
  const extraBehaviours = getToggleBehaviours(command);
  return forToolbar(clazz, action, extraBehaviours);
};

const forToolbar = function (clazz, action, extraBehaviours): Sketcher.SketchSpec {
  return Button.sketch({
    dom: UiDomFactory.dom('<span class="${prefix}-toolbar-button ${prefix}-icon-' + clazz + ' ${prefix}-icon"></span>'),
    action,

    buttonBehaviours: Merger.deepMerge(
      Behaviour.derive([
        Unselecting.config({ })
      ]),
      extraBehaviours
    )
  });
};

export default {
  forToolbar,
  forToolbarCommand,
  forToolbarStateAction,
  forToolbarStateCommand
};