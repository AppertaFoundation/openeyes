describe('OpenEyes', function(){
	describe('Namespace', function(){
		it('should create a "OpenEyes" namespace on the global namespace', function(){
			expect(typeof window.OpenEyes).to.equal('object');
		})
	})
});
