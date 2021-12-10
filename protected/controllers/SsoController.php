<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class SsoController extends BaseAdminController
{

    public $layout = 'admin';
    public $group = 'Core';

    public function accessRules()
    {
        return array(
            // Allow unauthenticated users to view certain pages
            array('allow',
                'actions' => array('login', 'redirectToSSOPortal'),
            ),
            array('allow',
                'actions' => array('defaultSSOPermissions','SSORolesAuthAssignment', 'editSSORoles', 'addSSORoles','deleteSSORoles'),
                'roles' => array('admin'),
            ),
        );
    }

    public function actionlogin()
    {
        $userInfo = [];
        // The user accessing through SAML Authentication
        if (Yii::app()->params['auth_source'] === 'SAML') {
            // Load the PHP-SAML settings
            $SAML_settings = Yii::app()->params['SAML_settings'];
            $cert = Yii::app()->params['sso_certificate_path'];
            // check if the docker secret file exist
            if (file_exists($cert)) {
                // Settings for OneLogin PHP-SAML toolkit and import the certificate from docker secret
                $SAML_settings['idp']['x509cert'] = rtrim(file_get_contents($cert));
            } elseif ($SAML_settings['idp']['x509cert'] === '' || $SAML_settings['idp']['x509cert'] === null) {
                throw new Exception("SAML Certificate not found.");
            }
            // Initialize the SP SAML instance
            $auth = new OneLogin\Saml2\Auth($SAML_settings);
            // Process SAMLResponse
            $auth->processResponse(null);
            $userInfo = $auth->getAttributes();

            if ($auth->getErrors()) {
                $error = $auth->getLastErrorReason();
                throw new Exception("Error in SAML authentication: ".$error);
            }
        }
        // The user accessing through OpenID-Connect Authorization
        elseif (Yii::app()->params['auth_source'] === 'OIDC') {
            // Get OIDC Settings
            $OIDC_settings = Yii::app()->params['OIDC_settings'];

            // Initialise OIDC-PHP instance
            $provider_url = $OIDC_settings['provider_url'];
            $client_id = $OIDC_settings['client_id'];
            $client_secret = $OIDC_settings['client_secret'];
            $issuer = $OIDC_settings['issuer'];
            $encryptionKey = $OIDC_settings['encryptionKey'];
            // SsoOpenIDConnect model that overrides certain functions of Jumbojett/OpenIDConnectClient
            $oidc = new SsoOpenIDConnect($provider_url, $client_id, $client_secret, $issuer, $encryptionKey);

            $oidc->setRedirectUrl($OIDC_settings['redirect_url']);
            $oidc->setResponseTypes($OIDC_settings['response_type']);
            $oidc->addScope($OIDC_settings['scopes']);
            $oidc->setAllowImplicitFlow($OIDC_settings['implicit_flow']);
            $oidc->addAuthParam($OIDC_settings['authParams']);
            try {
                $oidc->authenticate();
            } catch (Exception $e) {
                throw $e;
            }

            // Create an array of required user attributes (can be overidden from field_mapping)
            $requiredUserFields = [
                'username' => 'email',
                'email' => 'email',
                'first_name' => 'given_name',
                'last_name' => 'family_name'
            ];
            foreach ($requiredUserFields as $userField => $oidcField) {
                $userInfo[$userField] = $oidc->requestUserInfo($oidcField);
            }

            $token = (array)$oidc->getIdTokenPayload();
            $claimsMapping = $OIDC_settings['field_mapping'];
            foreach ($claimsMapping as $userField => $oidcField) {
                if (!isset($token[$oidcField])) continue; //Don't add to user info if it doesn't come from claims
                if ($userField === 'roles' && !is_array($token[$oidcField])) {
                    // Get the string value for roles and make it an array
                    if (strlen($token[$oidcField]) > 0) {
                        $userInfo[$userField] = explode(",", $token[$oidcField]);
                    } else {
                        $userInfo[$userField] = [];
                    }
                } else {
                    $userInfo[$userField] = $token[$oidcField];
                }
            }
        }

        // Save the new user into the OE database
        $user = new User();
        try {
            $identity = $user->setSSOUserInformation($userInfo);
        } catch (Exception $e) {
            $this->render('/sso/invalid_role', array(
                    'error' => $e->getMessage()
            ));
            Yii::app()->end();
        }

        // Set the user into the session then login
        $userIdentity = new UserIdentity($identity['username'], $identity['password']);
        if ($userIdentity->authenticate(true)) {
            if (Yii::app()->user->login($userIdentity, 0)) {
                // The login was sucessful so redirect to home page
                $this->render('/sso/login');
                return true;
            }
            throw new Exception('The user cannot be logged in');
        }
        throw new Exception('User Authentication failed');
    }

    public function actionredirectToSSOPortal()
    {
        $portalURL = Yii::app()->params['OIDC_settings']['portal_login_url'];
        if ($portalURL !== null) {
            $this->renderJSON($portalURL);
            return true;
        }
        return false;
    }

    public function actiondefaultSSOPermissions()
    {
        $ssoRights = SsoDefaultRights::model()->findByAttributes(['source' => 'SSO']);

        $request = Yii::app()->getRequest();

        if ($request->getIsPostRequest()) {
            $ssoAttributes = $request->getPost('SsoDefaultRights');

            // SAML User has id = 1
            $ssoAttributes['id'] = 1;

            try {
                $ssoRights->saveDefaultRights($ssoAttributes);
            } catch (FirmSaveException $e) {
                $ssoRights->addError('global_firm_rights', 'When no global firm rights is set, a firm must be selected');
                $errors = $ssoRights->getErrors();
            }
        }

        $this->render('/sso/defaultssopermissions', array(
            'rights' => $ssoRights,
            'errors' => @$errors
        ));
    }

    public function actionSSORolesAuthAssignment()
    {
        $ssoRoles = SsoRoles::model()->findAll();
        $this->render('/sso/ssorolesauthassignment', array(
            'ssoRoles' => $ssoRoles
        ));
    }

    public function actioneditSSORoles($id = null)
    {
        $request = Yii::app()->getRequest();

        if (!$id) {
            $ssoRoles = new SsoRoles();
        } else {
            $ssoRoles = SsoRoles::model()->findByPk($id);
        }
        if ($request->getIsPostRequest()) {
            $ssoAttributes = $request->getPost('SsoRoles');
            if (!array_key_exists('sso_roles_assignment', $ssoAttributes)) {
                $ssoAttributes['sso_roles_assignment'] = '';
            }
            try {
                $ssoRoles->saveRolesAuthAssignment($ssoAttributes['name'], $ssoAttributes['sso_roles_assignment'], $id);
                $this->redirect('/sso/ssorolesauthassignment');
            } catch (Exception $e) {
                $ssoRoles->addError('name', 'SSO Role "'.$ssoRoles->name.'" already exists');
                $errors = $ssoRoles->getErrors();
            }
        }

        $this->render('/sso/editSSORoles', array(
            'ssoRoles' => $ssoRoles,
            'errors' => @$errors
        ));
    }

    public function actiondeleteSSORoles($id = null)
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            SsoRolesAuthAssignment::model()->deleteAll('sso_role_id = :id', [':id' => $id]);
            $ssoRole = SsoRoles::model()->findByPk($id);
            $ssoRole->delete();

            $transaction->commit();
            Yii::app()->user->setFlash('Success', 'SSO Role successfully deleted');
            Audit::add('SSO', 'SSO-role-modified', 'SSO Role was was deleted: '. $ssoRole->name);
            $this->redirect('/sso/ssorolesauthassignment');
        } catch (Exception $e) {
            $transaction->rollback();
        }
    }

    public function actionaddSSORoles()
    {
        return $this->actioneditSSORoles();
    }
}
