Cypress.Commands.add('login', (username, password) => {
    if (password == undefined) {
        password = 'admin';
    }
    if (username == undefined) {
        username = 'admin';
    }
    return cy.request({
        method: 'POST',
        url: 'CypressHelper/Default/login',
        body: {
            username: username,
            password: password
        }
    });
});

Cypress.Commands.add('createEvent', (eventType) => {
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/createEvent/' + eventType
    })
    .its('body');
});

Cypress.Commands.add('createPatient', (states, attributes) => {
    if (attributes === undefined) {
        attributes = [];
    }
    if (states === undefined) {
        states = [];
    }
    return cy.request({
        method: 'POST',
        form: true,
        url: '/CypressHelper/Default/createPatient',
        body: {
            states: states,
            attributes: attributes
        }
    })
    .its('body');
});

Cypress.Commands.add('getEventCreationUrl', (patientId, moduleName) => {
    return cy.request({
        method: 'GET',
        url: `/CypressHelper/Default/getEventCreationUrl/${patientId}/${moduleName}`
    })
    .its('body.url');
});

Cypress.Commands.add('getModelByAttributes', (className, attributes) => {
    return cy.request({
        method: 'POST',
        form: true,
        url: '/CypressHelper/Default/lookupOrCreateModel',
        body: {
            model_class: className,
            attributes: attributes
        }
    })
    .its('body.model')
})