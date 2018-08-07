import { Singleton } from '@ephox/katamari';
import { Class } from '@ephox/sugar';
import AndroidEvents from './AndroidEvents';
import AndroidSetup from './AndroidSetup';
import PlatformEditor from '../../ios/core/PlatformEditor';
import Thor from '../../util/Thor';
import Styles from '../../style/Styles';
import MetaViewport from '../../touch/view/MetaViewport';

const create = function (platform, mask) {

  const meta = MetaViewport.tag();
  const androidApi = Singleton.api();

  const androidEvents = Singleton.api();

  const enter = function () {
    mask.hide();

    Class.add(platform.container, Styles.resolve('fullscreen-maximized'));
    Class.add(platform.container, Styles.resolve('android-maximized'));
    meta.maximize();

    /// TM-48 Prevent browser refresh by swipe/scroll on android devices
    Class.add(platform.body, Styles.resolve('android-scroll-reload'));

    androidApi.set(
      AndroidSetup.setup(platform.win, PlatformEditor.getWin(platform.editor).getOrDie('no'))
    );

    PlatformEditor.getActiveApi(platform.editor).each(function (editorApi) {
      Thor.clobberStyles(platform.container, editorApi.body());
      androidEvents.set(
        AndroidEvents.initEvents(editorApi, platform.toolstrip, platform.alloy)
      );
    });
  };

  const exit = function () {
    meta.restore();
    mask.show();
    Class.remove(platform.container, Styles.resolve('fullscreen-maximized'));
    Class.remove(platform.container, Styles.resolve('android-maximized'));
    Thor.restoreStyles();

    /// TM-48 re-enable swipe/scroll browser refresh on android
    Class.remove(platform.body, Styles.resolve('android-scroll-reload'));

    androidEvents.clear();

    androidApi.clear();
  };

  return {
    enter,
    exit
  };
};

export default {
  create
};