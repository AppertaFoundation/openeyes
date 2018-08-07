import { ApproxStructure, Assertions, Pipeline, Step, Waiter } from '@ephox/agar';
import { UnitTest } from '@ephox/bedrock';
import { TinyApis, TinyLoader, TinyUi } from '@ephox/mcagar';
import { Element } from '@ephox/sugar';

import Plugin from 'tinymce/plugins/media/Plugin';
import Theme from 'tinymce/themes/modern/Theme';

import Utils from '../module/test/Utils';

UnitTest.asynctest('browser.core.EphoxEmbedTest', function () {
  const success = arguments[arguments.length - 2];
  const failure = arguments[arguments.length - 1];

  Plugin();
  Theme();

  const ephoxEmbedStructure = ApproxStructure.build(function (s, str/*, arr*/) {
    return s.element('p', {
      children: [
        s.element('div', {
          children: [
            s.element('iframe', {
              attrs: {
                src: str.is('about:blank')
              }
            })
          ],
          attrs: {
            'data-ephox-embed-iri': str.is('embed-iri'),
            'contenteditable': str.is('false')
          }
        })
      ]
    });
  });

  const sAssertDivStructure = function (editor, expected) {
    return Step.sync(function () {
      const div = editor.dom.select('div')[0];
      const actual = div ? Element.fromHtml(div.outerHTML) : Element.FromHtml('');
      return Assertions.sAssertStructure('Should be the same structure', expected, actual);
    });
  };

  TinyLoader.setup(function (editor, onSuccess, onFailure) {
    const ui = TinyUi(editor);
    const apis = TinyApis(editor);

    Pipeline.async({}, [
      apis.sFocus,
      apis.sSetContent('<div contenteditable="false" data-ephox-embed-iri="embed-iri"><iframe src="about:blank"></iframe></div>'),
      sAssertDivStructure(editor, ephoxEmbedStructure),
      apis.sSelect('div', []),
      Utils.sOpenDialog(ui),
      Utils.sAssertSourceValue(ui, 'embed-iri'),
      Utils.sAssertEmbedContent(ui,
        '<div contenteditable="false" data-ephox-embed-iri="embed-iri">' +
        '<iframe src="about:blank"></iframe>' +
        '</div>'
      ),
      Utils.sSubmitDialog(ui),
      Waiter.sTryUntil('wait for div struture', sAssertDivStructure(editor, ephoxEmbedStructure), 100, 3000)
    ], onSuccess, onFailure);
  }, {
    plugins: 'media',
    toolbar: 'media',
    media_url_resolver (data, resolve) {
      resolve({
        html: '<video width="300" height="150" ' +
          'controls="controls">\n<source src="' + data.url + '" />\n</video>'
      });
    },
    skin_url: '/project/js/tinymce/skins/lightgray'
  }, success, failure);
});
