import { LegacyUnit } from '@ephox/mcagar';
import { Pipeline } from '@ephox/agar';
import ObservableObject from 'tinymce/ui/data/ObservableObject';
import { UnitTest } from '@ephox/bedrock';

UnitTest.asynctest('browser.tinymce.ui.data.ObservableObjectTest', function () {
  const success = arguments[arguments.length - 2];
  const failure = arguments[arguments.length - 1];
  const suite = LegacyUnit.createSuite();

  suite.test('Constructor', function () {
    let obj;

    obj = new ObservableObject();
    LegacyUnit.strictEqual(!obj.has('a'), true);

    obj = new ObservableObject({ a: 1, b: 2 });
    LegacyUnit.strictEqual(obj.get('a'), 1);
    LegacyUnit.strictEqual(obj.get('b'), 2);
  });

  suite.test('set/get and observe all', function () {
    const obj = new ObservableObject(), events = [];

    obj.on('change', function (e) {
      events.push(e);
    });

    obj.set('a', 'a');
    obj.set('a', 'a2');
    obj.set('a', 'a3');
    obj.set('b', 'b');
    LegacyUnit.strictEqual(obj.get('a'), 'a3');

    LegacyUnit.equal(events[0].type, 'change');
    LegacyUnit.equal(events[0].value, 'a');
    LegacyUnit.equal(events[1].type, 'change');
    LegacyUnit.equal(events[1].value, 'a2');
    LegacyUnit.equal(events[2].type, 'change');
    LegacyUnit.equal(events[2].value, 'a3');
    LegacyUnit.equal(events[3].type, 'change');
    LegacyUnit.equal(events[3].value, 'b');
  });

  suite.test('set/get and observe specific', function () {
    const obj = new ObservableObject(), events = [];

    obj.on('change:a', function (e) {
      events.push(e);
    });

    obj.set('a', 'a');
    obj.set('b', 'b');
    LegacyUnit.equal(events[0].type, 'change');
    LegacyUnit.equal(events[0].value, 'a');
    LegacyUnit.equal(events.length, 1);
  });

  Pipeline.async({}, suite.toSteps({}), function () {
    success();
  }, failure);
});
