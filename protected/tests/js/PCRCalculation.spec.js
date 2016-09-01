describe('PCRCalculation', function () {
  it('should create a function to calculate the OR value', function () {
    expect(typeof calculateORValue).to.equal('function');
  });

  it('should return 0 for an empty object', function () {
    expect(calculateORValue({})).to.equal(0);
  });

  it('should return 0 for an incomplete object', function () {
    expect(calculateORValue({age: '1'})).to.equal(0);
  });

  it('should return NaN for a complete but invalid object', function () {
    var invalid = {
      abletolieflat: "g",
      age: "8",
      alpareceptorblocker: "sdf",
      axiallength: "fsdgsd",
      brunescentwhitecataract: "asdfas",
      diabetic: "NK",
      doctorgrade: "1TESTINSKG",
      fundalview: "NK",
      gender: "Bigender",
      glaucoma: "asdfa",
      pupilsize: "Huge",
      pxf: "NK"
    };
    expect(isNaN(calculateORValue(invalid))).to.equal(true);
  });
});
