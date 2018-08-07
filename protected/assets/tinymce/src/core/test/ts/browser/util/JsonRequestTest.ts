import { Pipeline } from '@ephox/agar';
import { LegacyUnit } from '@ephox/mcagar';
import JSONRequest from 'tinymce/core/api/util/JSONRequest';
import { UnitTest } from '@ephox/bedrock';

UnitTest.asynctest('browser.tinymce.core.util.JsonRequestTest', function () {
  const success = arguments[arguments.length - 2];
  const failure = arguments[arguments.length - 1];
  const suite = LegacyUnit.createSuite();

  suite.asyncTest('Successful request - send method', function (editor, done) {
    new JSONRequest({}).send({
      type: 'GET',
      url: '/custom/json_rpc_ok',
      success (data) {
        LegacyUnit.equal(data, 'Hello JSON-RPC');
        done();
      }
    });
  });

  suite.asyncTest('Successful request - sendRPC static method', function (editor, done) {
    JSONRequest.sendRPC({
      type: 'GET',
      url: '/custom/json_rpc_ok',
      success (data) {
        LegacyUnit.equal(data, 'Hello JSON-RPC');
        done();
      }
    });
  });

  suite.asyncTest('Error request - send method', function (editor, done) {
    new JSONRequest({}).send({
      type: 'GET',
      url: '/custom/json_rpc_fail',
      error (error) {
        LegacyUnit.equal(error.code, 42);
        done();
      }
    });
  });

  suite.asyncTest('Error request - sendRPC static method', function (editor, done) {
    JSONRequest.sendRPC({
      type: 'GET',
      url: '/custom/json_rpc_fail',
      error (error) {
        LegacyUnit.equal(error.code, 42);
        done();
      }
    });
  });

  Pipeline.async({}, suite.toSteps({}), function () {
    success();
  }, failure);
});
