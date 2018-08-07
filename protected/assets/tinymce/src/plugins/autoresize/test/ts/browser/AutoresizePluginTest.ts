import { Assertions, GeneralSteps, Logger, Pipeline, RawAssertions, Step, Waiter } from '@ephox/agar';
import { Arr } from '@ephox/katamari';
import { TinyApis, TinyLoader } from '@ephox/mcagar';
import AutoresizePlugin from 'tinymce/plugins/autoresize/Plugin';
import FullscreenPlugin from 'tinymce/plugins/fullscreen/Plugin';
import ModernTheme from 'tinymce/themes/modern/Theme';
import { UnitTest } from '@ephox/bedrock';

UnitTest.asynctest('browser.tinymce.plugins.autoresize.AutoresizePluginTest', function () {
  const success = arguments[arguments.length - 2];
  const failure = arguments[arguments.length - 1];

  AutoresizePlugin();
  FullscreenPlugin();
  ModernTheme();

  const sAssertEditorHeightAbove = function (editor, minHeight) {
    return Step.sync(function () {
      const editorHeight = editor.getContainer().clientHeight;
      RawAssertions.assertEq('should be above: ' + editorHeight + '>=' + minHeight, true, editorHeight >= minHeight);
    });
  };

  const sAssertEditorHeightBelow = function (editor, minHeight) {
    return Step.sync(function () {
      const editorHeight = editor.getContainer().clientHeight;
      RawAssertions.assertEq('should be below: ' + editorHeight + '<=' + minHeight, true, editorHeight <= minHeight);
    });
  };

  const sAssertScroll = function (editor, state) {
    return Step.sync(function () {
      const body = editor.getBody();
      Assertions.assertEq('', !state, body.style.overflowY === 'hidden');
    });
  };

  TinyLoader.setup(function (editor, onSuccess, onFailure) {
    const tinyApis = TinyApis(editor);

    Pipeline.async({}, Arr.flatten([
      [
        Logger.t('Fullscreen toggle scroll state', GeneralSteps.sequence([
          tinyApis.sExecCommand('mceFullScreen'),
          sAssertScroll(editor, true),
          tinyApis.sExecCommand('mceFullScreen'),
          sAssertScroll(editor, false)
        ])),
        Logger.t('Editor size increase based on content size', GeneralSteps.sequence([
          tinyApis.sSetContent('<div style="height: 5000px;">a</div>'),
          Waiter.sTryUntil('wait for editor height', sAssertEditorHeightAbove(editor, 5000), 10, 3000)
        ])),
        Logger.t('Editor size decrease based on content size', GeneralSteps.sequence([
          tinyApis.sSetContent('<div style="height: 1000px;">a</div>'),
          Waiter.sTryUntil('wait for editor height', sAssertEditorHeightBelow(editor, 2000), 10, 3000)
        ]))
      ],

      // These tests doesn't work on phantom since measuring things seems broken there
      navigator.userAgent.indexOf('PhantomJS') === -1 ? [
        Logger.t('Editor size decrease content to 1000 based and restrict by max height', GeneralSteps.sequence([
          tinyApis.sSetSetting('autoresize_max_height', 200),
          tinyApis.sSetContent('<div style="height: 1000px;">a</div>'),
          Waiter.sTryUntil('wait for editor height', sAssertEditorHeightBelow(editor, 500), 10, 3000),
          tinyApis.sSetSetting('autoresize_max_height', 0)
        ])),
        Logger.t('Editor size decrease content to 10 and set min height to 500', GeneralSteps.sequence([
          tinyApis.sSetSetting('autoresize_min_height', 500),
          tinyApis.sSetContent('<div style="height: 10px;">a</div>'),
          Waiter.sTryUntil('wait for editor height', sAssertEditorHeightAbove(editor, 300), 10, 3000),
          tinyApis.sSetSetting('autoresize_min_height', 0)
        ]))
      ] : []
    ]), onSuccess, onFailure);
  }, {
    plugins: 'autoresize fullscreen',
    toolbar: 'autoresize',
    skin_url: '/project/js/tinymce/skins/lightgray'
  }, success, failure);
});
