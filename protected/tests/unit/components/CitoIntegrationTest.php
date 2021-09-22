<?php

class CitoIntegrationTest extends CTestCase
{
    private function callMethod($object, string $method, array $parameters = [])
    {
        try {
            $className = get_class($object);
            $reflection = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new \Exception($e->getMessage());
        }

        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testGenerateCitoUrl()
    {
        $url = "http://example.com/test";
        $hosNum = "00100100";
        $otp = "Pass Word";
        $userName = "admin";
        $appId = "OPENEYES";
        $expected = $url . "?identifier=" . $hosNum . "&display=cito-icm-record&otp=" . urlencode($otp) . "&user=" . $userName . "&domain=" . $appId;

        /** @var \CitoIntegration $instance */
        $this->citoIntegration =  $this->getMockBuilder(CitoIntegration::class)
            ->setMethods(["getSetting"])
            ->getMock();

        $ret_map = [
            ["cito_base_url", $url],
            ["cito_otp_url", $url],
            ["cito_sign_url", $url],
            ["cito_access_token_url", $url],
            ["cito_application_id", $appId],
            ["cito_grant_type", "test"],
            ["cito_client_id", "test"],
            ["cito_client_secret", "secret"],
        ];
        $this->citoIntegration->method("getSetting")->will($this->returnValueMap($ret_map));
        $instance = $this->citoIntegration;
        $instance->cito_base_url = $url;
        $instance->cito_application_id = $appId;

        $this->assertEquals($expected, $this->callMethod($instance, "getUrl", ["hos_num" => $hosNum, "username" => $userName, "otp" => $otp]));
    }
}
