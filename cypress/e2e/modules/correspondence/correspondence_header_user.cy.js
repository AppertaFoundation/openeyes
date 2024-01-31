describe("Correspondence Header User Test", () => {
    const SETTING_NAME = 'require_pin_for_correspondence';

    before(function () {
      // Log in and run the seeder to get data for testing.
      cy.login()
        .then(() => {
          return cy.setSystemSettingValue(SETTING_NAME, 'no')
        })
        .then(() => {
          return cy.runSeeder('OphCoCorrespondence', 'CorrespondenceHeaderUserSeeder');
        })
        .as('seederData')
    });


    it("Ensure the consistency of the user's title and name in both the letter header and correspondence event for the two different user", function () {
      cy.login(this.seederData.user[0].username, this.seederData.user[0].password).then(() => {
        cy.getEventCreationUrl(this.seederData.patient_id, 'OphCoCorrespondence')
        .then((url) => {
          cy.visit(url);

          cy.intercept('/docman/ajaxGetMacroTargets*').as('docman-macro');
          cy.intercept('/OphCoCorrespondence/Default/getMacroData*').as('correspondence-macro');
          cy.intercept('/OphCoCorrespondence/default/getAddress*').as('correspondence-address');

          // Ensure mandatory fields are correctly set to prevent input errors.
          chooseOption('[data-test="letter-type"]', 1); // Select the second letter type option.
          chooseOption('select[data-test="letter-template"]', 1); // Select the second letter template option.
          cy.getBySel('letter-to_0').select(`Patient${this.seederData.patient_id}`); // Select the recipient.
          cy.wait('@docman-macro');
          cy.wait('@correspondence-macro');
          cy.wait('@correspondence-address');


          // Hit save button.
          cy.getBySel('event-action-save-draft').first().click();

          // Wait for the next page to load.
          cy.location('pathname', { timeout: 20000 }).should('include', '/view');

          let newUrl = '';
          cy.url().then((url) => {
            newUrl = url.replace('view', 'print');
          });

          //Log in with user 1 and check the user title and name show as user 1 in the letter header.
          cy.login(this.seederData.user[0].username, this.seederData.user[0].password).then(() => {
            cy.request(newUrl).its('body').then((html) => {

              // Remove <script> tags from the HTML to prevent JavaScript execution, i.e., to avoid print pop-up.
              html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");

              // Rewriting the HTML that does not have the script tags.
              cy.document().invoke({ log: false }, "write", html);
              cy.get('header.print-header').find('span[data-substitution="user_title"] span').should('have.text', this.seederData.user[0].title);
            });
          });

            //Log in with user 2 and check the user title and name still show as user 1 and not user 2 in the letter header.
            cy.login(this.seederData.user[1].username, this.seederData.user[1].password).then(() => {
            cy.request(newUrl).its('body').then((html) => {

                // Remove <script> tags from the HTML to prevent JavaScript execution, i.e., to avoid print pop-up.
                html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "");
                cy.document().then((document) => { document.documentElement.innerHTML = html });

                cy.get('header.print-header').find('span[data-substitution="user_title"] span').should('have.text', this.seederData.user[0].title);
            });
            });
        });
      });
    });

    /**
     * Select the specified option in a dropdown by its index.
     * @param {string} selector - The selector for the dropdown element.
     * @param {number} selectedOption - The index of the option to select.
     */
    function chooseOption(selector, selectedOption) {
      cy.get(selector).find('option').eq(selectedOption).then(($option) => {
        const text = $option.text();
        cy.get(selector).select(text);
      });
    }
  });
