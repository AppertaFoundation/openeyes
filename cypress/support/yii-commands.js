Cypress.Commands.add('createUser', (authitems, attributes, username, password) => {
    let data = {
        authitems: authitems
    }
    if (attributes !== undefined) {
        data.attributes = attributes
    }
    if (username !== undefined) {
        data.username = username;
    }
    if (password !== undefined) {
        data.password = password;
    }

    return cy.request({
        method: 'POST',
        form: true,
        url: 'CypressHelper/Default/createUser',
        body: data
    })
    .its('body');
});

Cypress.Commands.add('login', (username, password) => {
    if (password == undefined) {
        password = 'admin';
    }
    if (username == undefined) {
        username = 'admin';
    }
    return cy.request({
        method: 'POST',
        form: true,
        url: 'CypressHelper/Default/login',
        body: {
            username: username,
            password: password
        }
    });
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

Cypress.Commands.add('visitEventCreationUrl', (patientId, moduleName) => {
    return cy.getEventCreationUrl(patientId, moduleName)
        .then((url) => {
            return cy.visit(url);
        });
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
});

Cypress.Commands.add('createModels', (className, states, attributes, count) => {
    if (count === undefined) {
        count = 1;
    }
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/createModels',
        form: true,
        body: {
            model_class: className,
            states: states,
            attributes: attributes,
            count: count
        }
    })
    .then((response) => {
        if ((response.body.models.length) === 1) {
            return response.body.models[0];
        }
        return response.body.models;
    });
});

Cypress.Commands.add('runSeeder', (seederModuleName, seederClassName, additionalData = null) => {
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/runSeeder',
        form: true,
        body: {
            seeder_module_name: seederModuleName,
            seeder_class_name: seederClassName,
            additional_data: additionalData,
        }
    }).its('body');
});

Cypress.Commands.add('createEvent', (eventType, states, attributes, count) => {
    if (count === undefined) {
        count = 1;
    }
    if (states === undefined) {
        states = [];
    }
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/createEvent/' + eventType,
        form: true,
        body: {
            states: states,
            attributes: attributes,
            count: count
        }
    }).its('body');
});

Cypress.Commands.add('setSystemSettingValue', (settingKey, settingValue) => {
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/setSystemSettingValue',
        form: true,
        body: {
            system_setting_key: settingKey,
            system_setting_value: settingValue
        }
    });
});

Cypress.Commands.add('getSystemSettingValue', (settingKey) => {
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/getSystemSettingValue',
        form: true,
        body: {
            system_setting_key: settingKey
        }
    });
});

Cypress.Commands.add('resetSystemSettingValue', (settingKey) => {
    return cy.request({
        method: 'POST',
        url: '/CypressHelper/Default/resetSystemSettingValue',
        form: true,
        body: {
            system_setting_key: settingKey
        }
    });
});