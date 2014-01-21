var chai = require('chai');
var expect = chai.expect;

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
