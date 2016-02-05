/*jshint expr: true*/

describe('OpenEyes.CO.SiteSecretary', function () {
    describe('Namespace', function () {
        it('should create a "SiteSecretary" namespace on the "CO" namespace', function () {
            expect(typeof OpenEyes.CO.SiteSecretary).to.equal('object');
        });
    });

    describe('SiteSecretary', function () {

        it('init should exist in the namespace', function () {
            expect(typeof OpenEyes.CO.SiteSecretary.init).to.equal('function');
        });

        describe('init', function () {
            var $skeletonAdd = $('#editSecretaryForm'),
                initialRows = $('#editSecretaryForm table tbody tr').length; //number of rows in the tbody

            before(function () {
                OpenEyes.CO.SiteSecretary.init();
            });

            it('should remove the main form controls row', function () {
                expect($('#editSecretaryForm').find('tbody tr').length).to.equal(initialRows - 1);
            });

            it('should add a save button to all existing rows', function () {
                expect($skeletonAdd.find('.addButton').length).to.equal($('.secretaryFormRow').length);
            });

            it('should make the last button say Add', function () {
                expect($skeletonAdd.find('.addButton:last').text()).to.equal('Add');
            });
        });

        describe('add', function () {
            var sucessObject = {
                    "siteSecretaries": [{
                        "last_modified_user_id": "1",
                        "last_modified_date": "2015-03-23 13:22:34",
                        "created_user_id": "1",
                        "created_date": "2015-03-23 13:22:34",
                        "firm_id": "2",
                        "site_id": "1",
                        "direct_line": "12356",
                        "fax": "32432523",
                        "id": "44"
                    }, {
                        "last_modified_user_id": "1",
                        "last_modified_date": "1900-01-01 00:00:00",
                        "created_user_id": "1",
                        "created_date": "1900-01-01 00:00:00",
                        "id": null,
                        "firm_id": null,
                        "site_id": null,
                        "direct_line": null,
                        "fax": null
                    }], "errors": [], "success": true
                },
                $addRow = $('.secretaryFormRow:last'),
                beforeRows; //number of rows in the tbody

            before(function () {
                beforeRows = $('#editSecretaryForm table tbody tr').length;
                sinon.stub($, 'ajax').yieldsTo('success', sucessObject);
                $addRow.find(':input').each(function () {
                    $(this).val(12);
                });
                $addRow.find('.addButton').trigger('click');
            });

            after(function () {
                $.ajax.restore();
            });

            it('should call ajax to add the contact', function () {
                expect($.ajax.calledOnce).to.be.true;
            });

            it('should set the ID to be the returned value', function () {
                expect($addRow.find(':input[name$=\\[id\\]]').val()).to.equal(sucessObject.siteSecretaries[0].id);
            });

            it('should add a new row', function () {
                expect($('#editSecretaryForm table tbody tr').length).to.equal(beforeRows + 1);
            });

            it('should replace the Add text for Save', function () {
                expect($addRow.find('.addButton').text()).to.equal('Save');
                expect($addRow.find('.addButton').text()).to.not.equal('Add');
            });

            it('should add a delete button', function () {
                expect($addRow.find('button[form="deleteSecretaryForm"]').length).to.equal(1);
            });

            it('should assign a value to the delete button', function () {
                expect($addRow.find('button[form="deleteSecretaryForm"]').val()).to.equal(sucessObject.siteSecretaries[0].id);
            });

        });

        describe('update', function () {
            var sucessObject = {
                    "siteSecretaries": [{
                        "last_modified_user_id": "1",
                        "last_modified_date": "2015-03-23 13:22:34",
                        "created_user_id": "1",
                        "created_date": "2015-03-23 13:22:34",
                        "firm_id": "2",
                        "site_id": "1",
                        "direct_line": "12356",
                        "fax": "32432523",
                        "id": "44"
                    }, {
                        "last_modified_user_id": "1",
                        "last_modified_date": "1900-01-01 00:00:00",
                        "created_user_id": "1",
                        "created_date": "1900-01-01 00:00:00",
                        "id": null,
                        "firm_id": null,
                        "site_id": null,
                        "direct_line": null,
                        "fax": null
                    }], "errors": [], "success": true
                },
                $updateRow = $('.secretaryFormRow:first'),
                beforeRows; //number of rows in the tbody

            before(function () {
                beforeRows = $('#editSecretaryForm table tbody tr').length;
                sinon.stub($, 'ajax').yieldsTo('success', sucessObject);
                $updateRow.find('.addButton').trigger('click');
            });

            after(function () {
                $.ajax.restore();
            });

            it('should call ajax to remove the contact', function () {
                expect($.ajax.calledOnce).to.be.true;
            });

            it('should still have a Save button', function () {
                expect($updateRow.find('.addButton').text()).to.equal('Save');
                expect($updateRow.find('.addButton').text()).to.not.equal('Add');
            });

            it('should not add any new rows', function () {
                expect($('#editSecretaryForm table tbody tr').length).to.equal(beforeRows);
            });
        });

        describe('delete', function () {
            var sucessObject = {},
                $deleteRow = $('.secretaryFormRow:first'),
                beforeRows; //number of rows in the tbody

            before(function () {
                beforeRows = $('#editSecretaryForm table tbody tr').length;
                sinon.stub($, 'ajax').yieldsTo('success', sucessObject);
                $deleteRow.find('button[form="deleteSecretaryForm"]').trigger('click');
            });

            after(function () {
                $.ajax.restore();
            });

            it('should call ajax to remove the contact', function () {
                expect($.ajax.calledOnce).to.be.true;
            });

            it('should remove the row from the table', function () {
                expect($('#editSecretaryForm table tbody tr').length).to.equal(beforeRows - 1);
            });
        });
    });
});