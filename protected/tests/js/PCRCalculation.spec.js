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
      alphareceptorblocker: "sdf",
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

  it('should return a numeric for a complete and valid object', function () {
    var valid = {
      abletolieflat: "Y",
      age: "1",
      alphareceptorblocker: "NK",
      axiallength: "1",
      brunescentwhitecataract: "NK",
      diabetic: "NK",
      doctorgrade: "1.00",
      fundalview: "NK",
      gender: "Male",
      glaucoma: "NK",
      pupilsize: "Medium",
      pxf: "NK"
    };
    var value = calculateORValue(valid);
    expect(!isNaN(value) && isFinite(value)).to.equal(true);
  });

  it('should return the correct PCR risk', function () {
    var valid = {
      abletolieflat: "Y",
      age: "1",
      alphareceptorblocker: "N",
      axiallength: "1",
      brunescentwhitecataract: "N",
      diabetic: "N",
      doctorgrade: "3.73",
      fundalview: "N",
      gender: "Female",
      glaucoma: "Y",
      pupilsize: "Large",
      pxf: "N"
    };
    //Age * Gender * Glaucoma * Diabetes * Cataract * Fundal * PXF * Pupil * Axial * Alpha * Lie Flat * Surgeon
    var factors = 1 * 1 * 1.3 * 1 * 1 * 1 * 1 * 1 * 1 * 1 * 1 * 3.73;
    var pcr = (factors * (0.00736 / (1 - 0.00736))) / (1 + (factors * 0.00736 / (1 - 0.00736))) * 100;
    var factorsActual = calculateORValue(valid);
    var pcrActual = calculatePcrValue(factors);

    expect(factorsActual).to.equal(factors);
    expect(pcrActual.pcrRisk).to.equal(pcr.toFixed(2));
    expect(pcrActual.excessRisk).to.equal((pcr / 1.92).toFixed(2));
    expect(pcrActual.pcrColour).to.equal('orange');
  });
});
