/// <reference types="cypress" />

declare namespace Cypress {
    interface Chainable<Subject> {
        /**
         * Use backend model factories to create a user that can be logged in with
         *
         * @param authItems list of RBAC items to assign to the user
         * @param attributes user model attributes
         * @param username
         * @param password
         */
        createUser(authitems: ?array, attributes: ?object, username: ?string, password: ?string): Chainable<any>
        /**
         * Login to OpenEyes. Defaults to logging in as the admin user
         *
         * @param username
         * @param password
         */
        login(username: ?string, password: ?string): Chainable<any>
        createPatient(states: ?array, attributes: ?object): Chainable<any>
        /**
         * Retrieve the URL to create an event for the patient identified by patientId in the given module
         *
         * @param patientId
         * @param moduleName
         * @param firmId
         */
        getEventCreationUrl(patientId: Number, moduleName: string, firmId: Number): Chainable<any>
        /**
         * Convenience wrapper to go straight to the event creation page for the given patient and module
         *
         * @param patientId
         * @param moduleName
         */
        visitEventCreationUrl(patientId: Number, moduleName: string): Chainable<any>
        /**
         * Use backend model factory to find or create an instance of the given class that matches
         * all the provided attributes
         *
         * @param className
         * @param attributes
         */
        getModelByAttributes(className: string, attributes: ?object): Chainable<any>
        /**
         * Use backend model factory to create one or more model instances with the given parameters
         *
         * @param className
         * @param states
         * @param attributes
         * @param count
         * @example
         * cy.createModels('Disorder', [['forOphthalmology'], ['withICD10']], ['term' => 'A Test Disorder'], 3)
         */
        createModels(className: string, states: array[], attributes: array, count: Number): Chainable<any>
        /**
         * Run a seeder class in the backend
         * @param moduleName
         * @param className
         * @param additionalData
         */
        runSeeder(moduleName: string, className: string, additionalData: object): Chainable<any>
        /**
         * Use backend event factory to create an event of the given eventType
         * @param eventType
         * @param states
         * @param attributes
         * @param count
         */
        createEvent(eventType: string, states: array[], attributes: object, count: Number): Chainable<any>
        /**
         * Set a system configuration value in the backend
         * @param settingKey
         * @param settingValue
         */
        setSystemSettingValue(settingKey: string, settingValue: string): Chainable<any>
        /**
         * Retrieve the current system setting value from the backend
         * @param settingKey
         */
        getSystemSettingValue(settingKey: string): Chainable<any>
        /**
         * Reset the given setting system to default by removing any saved value
         * @param settingKey
         */
        resetSystemSettingValue(settingKey: string): Chainable<any>

        /**
         * Removes all elements from an event. Useful for resetting an event to a known state
         * @param exceptElementNames name(s) of any elements to KEEP
         * @param force if true will remove the elements even if they are mandatory, dirty or disabled
         */
        removeElements(exceptElementNames: string | string[], force: boolean): Chainable<any>

        /**
         * Removes a single element with the given name
         * @param elementName name of the element to remove
         * @param force if true will remove the element even if it is mandatory, dirty or disabled
         */
        removeElement(elementName: string, force: boolean): Chainable<any>

        /**
         * Add the elements to the event draft
         * @param draftId
         * @param elements
         */
        addElementsToDraftExamination(draftId: int, elements: array): Chainable<any>
    }
  }