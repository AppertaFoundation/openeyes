/*
This example spec shows some common examples.
 */

// A basic test suite.
describe('My fancy module', function() {

	describe('A fancy feature', function() {

		// Run once before any tests in this block.
		before(function() {});

		// Run before all tests in this block.
		beforeEach(function() {});

		// Run once after all tests in this block have run.
		after(function() {})

		// Run after each test is run.
		afterEach(function() {});

		// Describe your test
		it('should calculate something', function() {
			// write your assertions here
			expect(1).to.equal(1);
		});
	});
});

// Some examples.
describe('Example tests', function() {

	describe('Chai assertions', function() {

		var var1 = 1;
		var var2 = [];
		var tea = {
			flavors: [1,2,3]
		};

		describe('BDD', function() {
			it('should pass basic tests using BDD style syntax', function() {
				expect(var1).to.equal(1);
				expect(var2 instanceof Array).to.be.true;
				expect(tea).to.have.property('flavors').with.length(3);
			});
		});

		describe('Assert', function() {
			it('should pass basic tests using assert style syntax', function() {
				assert.equal(var1, 1);
				assert.isTrue(var2 instanceof Array);
				assert.property(tea, 'flavors');
				assert.lengthOf(tea.flavors, 3);
			});
		});
	});

	describe('Sinon Spies & Mocking', function(){
		describe('Spies', function(){

			// For when we want to check if a method has been called (and allows
			// the original method to be called)
			it('should call the function with specific params', function () {

				var obj = {
					alert: function(a) {
						// This method will be executed.
						console.log(a);
					}
				};

				var spy = sinon.spy(obj, 'alert');
				var context = {};

				obj.alert.call(context, 'foo');
				obj.alert.call(context, 'foo');

				expect(spy.calledWith('foo')).to.be.ok;
				expect(spy.callCount).to.equal(2);
				expect(spy.calledOn(context)).to.be.ok;
			});
		});

		// Pre-programmed expectations
		describe('Mocks', function(){
			it('should mock the object so we can add expectations', function () {
				var obj = {
					alert: function(a) {
						// As we've mocked the method, this method will never be called.
					}
				};
				var mock = sinon.mock(obj);
				mock.expects('alert').once().withArgs('foo');
				obj.alert('foo');
				mock.verify();
			});
		});

		describe('Stubs', function() {

			function testAjaxRequest(id, callback) {
				$.ajax({
					type: 'POST',
					data: 'foo=bar',
					url: "/test/"+id
				});
			}

			// When wrapping an existing function with a stub, the original function is not called.
			it('should return with a specified value', function() {
				var stub = sinon.stub().returns(42);
				expect(stub()).to.equal(42);
			});
			after(function () {
				$.ajax.restore();
			});

			// Note, the actual ajax method is not called as it's been stubbed.
			it('should stub the jquery ajax method', function() {
				sinon.stub(jQuery, "ajax");
				testAjaxRequest(42);
				expect(jQuery.ajax.calledWithMatch({ url: "/test/42" })).to.be.true;
			});
		});
	});

	describe('Mocha async', function(val) {

		// Here we're testing that the asynchronous tests are run in series.

		it('should pass the first async test', function(done) {
			setTimeout(function() {
				expect(val).to.be.undefined;
				val = 42;
				done();
			}, 100);
		});

		it('should pass the second async test', function(done) {
			setTimeout(function() {
				expect(val).to.equal(42);
				done();
			}, 50);
		})
	});
});

// Can haz AJAX?
describe('AJAX', function() {

	describe('Getting fixtures with AJAX', function() {

		it('should get the test JSON file', function(done) {

			$.ajax({
				type: 'GET',
				url: '../fixtures/ajax_test.json',
				dataType: 'JSON'
			})
			.success(function() {
				done(null);
			})
			.error(function() {
				done(
					'Unable to load the JSON file via ajax. ' +
					'This could be due to protocol mis-match. ' +
					'Is the test runner being served using the file protocol?'
				);
			});

			// write your assertions here
			expect(1).to.equal(1);
		});
	});
});