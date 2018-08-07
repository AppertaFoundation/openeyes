import { Pipeline } from '@ephox/agar';
import { LegacyUnit } from '@ephox/mcagar';
import EventUtils from 'tinymce/core/api/dom/EventUtils';
import UiUtils from '../module/test/UiUtils';
import ViewBlock from '../module/test/ViewBlock';
import Api from 'tinymce/ui/Api';
import Factory from 'tinymce/core/api/ui/Factory';
import Tools from 'tinymce/core/api/util/Tools';
import { UnitTest } from '@ephox/bedrock';

UnitTest.asynctest('browser.tinymce.ui.TabPanelTest', function () {
  const success = arguments[arguments.length - 2];
  const failure = arguments[arguments.length - 1];
  const suite = LegacyUnit.createSuite();
  const viewBlock = ViewBlock();

  // Registers ui widgets to factory
  Api.registerToFactory();

  const createTabPanel = function (settings) {
    EventUtils.Event.clean(viewBlock.get());
    viewBlock.update('');

    return Factory.create(Tools.extend({
      type: 'tabpanel',
      items: [
        { title: 'a', type: 'spacer', classes: 'red' },
        { title: 'b', type: 'spacer', classes: 'green' },
        { title: 'c', type: 'spacer', classes: 'blue' }
      ]
    }, settings)).renderTo(viewBlock.get()).reflow();
  };

  suite.test('panel width: 100, height: 100', function () {
    const panel = createTabPanel({
      width: 100,
      height: 100,
      layout: 'fit'
    });

    LegacyUnit.equal(UiUtils.rect(viewBlock, panel), [0, 0, 100, 100]);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[0]), [0, 31, 100, 69], 4);
  });

  suite.test('panel width: 100, height: 100, border: 1', function () {
    const panel = createTabPanel({
      width: 100,
      height: 100,
      border: 1,
      layout: 'fit'
    });

    LegacyUnit.equal(UiUtils.rect(viewBlock, panel), [0, 0, 100, 100]);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[0]), [0, 31, 100, 69], 4);
  });

  suite.test('panel width: 100, height: 100, activeTab: 1', function () {
    const panel = createTabPanel({
      width: 100,
      height: 100,
      activeTab: 1,
      layout: 'fit'
    });

    LegacyUnit.equal(UiUtils.rect(viewBlock, panel), [0, 0, 100, 100]);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[1]), [0, 31, 100, 69], 4);
  });

  suite.test('panel width: auto, height: auto, mixed sized widgets', function () {
    const panel = createTabPanel({
      items: [
        { title: 'a', type: 'spacer', classes: 'red', style: 'width: 100px; height: 100px' },
        { title: 'b', type: 'spacer', classes: 'green', style: 'width: 70px; height: 70px' },
        { title: 'c', type: 'spacer', classes: 'blue', style: 'width: 120px; height: 120px' }
      ]
    });

    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel), [0, 0, 120, 151], 4);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[0]), [0, 31, 120, 120], 4);

    panel.activateTab(1);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[1]), [0, 31, 120, 120], 4);

    panel.activateTab(2);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[2]), [0, 31, 120, 120], 4);
  });

  suite.test('panel width: auto, height: auto, mixed sized containers', function () {
    const panel = createTabPanel({
      items: [
        {
          title: 'a',
          type: 'panel',
          layout: 'flex',
          align: 'stretch',
          items: {
            type: 'spacer',
            classes: 'red',
            flex: 1,
            minWidth: 100,
            minHeight: 100
          }
        },

        {
          title: 'b',
          type: 'panel',
          layout: 'flex',
          align: 'stretch',
          items: {
            type: 'spacer',
            flex: 1,
            classes: 'green',
            minWidth: 70,
            minHeight: 70
          }
        },

        {
          title: 'c',
          type: 'panel',
          layout: 'flex',
          align: 'stretch',
          items: {
            type: 'spacer',
            classes: 'blue',
            flex: 1,
            minWidth: 120,
            minHeight: 120
          }
        }
      ]
    });

    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel), [0, 0, 120, 151], 4);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[0]), [0, 31, 120, 120], 4);

    panel.activateTab(1);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[1]), [0, 31, 120, 120], 4);

    panel.activateTab(2);
    UiUtils.nearlyEqualRects(UiUtils.rect(viewBlock, panel.items()[2]), [0, 31, 120, 120], 4);
  });

  UiUtils.loadSkinAndOverride(viewBlock, function () {
    Pipeline.async({}, suite.toSteps({}), function () {
      EventUtils.Event.clean(viewBlock.get());
      viewBlock.detach();
      success();
    }, failure);
  });
});
