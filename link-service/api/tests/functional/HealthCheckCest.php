<?php

class HealthCheckCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('link/health');
    }

    public function checkHealthEndpoint(\FunctionalTester $I)
    {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'status' => 'ok',
            'service' => 'link-service'
        ]);

        $response = json_decode($I->grabResponse(), true);
        $I->assertArrayHasKey('timestamp', $response, 'Response must have "timestamp" key');
        $I->assertIsInt($response['timestamp'], 'Timestamp must be integer');
    }
}