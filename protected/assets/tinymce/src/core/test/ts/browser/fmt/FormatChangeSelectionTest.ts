import { Pipeline } from '@ephox/agar';
import { TinyApis, TinyLoader } from '@ephox/mcagar';
import ModernTheme from 'tinymce/themes/modern/Theme';
import { UnitTest } from '@ephox/bedrock';

UnitTest.asynctest('browser.tinymce.core.fmt.FormatChangeSelectionTest', function () {
  const success = arguments[arguments.length - 2];
  const failure = arguments[arguments.length - 1];

  ModernTheme();

  TinyLoader.setup(function (editor, onSuccess, onFailure) {
    const tinyApis = TinyApis(editor);

    Pipeline.async({}, [
      tinyApis.sSetContent('<p><em><strong>a </strong>b<strong> c</strong></em></p>'),
      tinyApis.sSetSelection([0, 0, 1], 0, [0, 0, 2], 0),
      tinyApis.sExecCommand('italic'),
      tinyApis.sAssertContent('<p><em><strong>a </strong></em>b<em><strong> c</strong></em></p>'),
      tinyApis.sAssertSelection([0, 1], 0, [0, 2], 0)
    ], onSuccess, onFailure);
  }, {
    plugins: '',
    toolbar: '',
    skin_url: '/project/js/tinymce/skins/lightgray'
  }, success, failure);
});
