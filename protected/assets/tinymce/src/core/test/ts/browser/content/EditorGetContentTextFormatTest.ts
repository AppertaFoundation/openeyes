import { Assertions, GeneralSteps, Logger, Pipeline, Step } from '@ephox/agar';
import { TinyApis, TinyLoader } from '@ephox/mcagar';
import Theme from 'tinymce/themes/modern/Theme';
import { UnitTest } from '@ephox/bedrock';
import Zwsp from 'tinymce/core/text/Zwsp';

UnitTest.asynctest('browser.tinymce.core.content.EditorGetContentTextFormatTest', (success, failure) => {
  Theme();

  TinyLoader.setup(function (editor, onSuccess, onFailure) {
    const tinyApis = TinyApis(editor);

    Pipeline.async({}, [
      Logger.t('get text format content should trim zwsp', GeneralSteps.sequence([
        tinyApis.sSetContent('<p>' + Zwsp.ZWSP + 'a</p>'),
        Step.sync(function () {
          const html = editor.getContent({ format: 'text' });
          Assertions.assertEq('Should be expected html', 'a', html);
        })
      ]))
    ], onSuccess, onFailure);
  }, {
    skin_url: '/project/js/tinymce/skins/lightgray',
  }, success, failure);
});