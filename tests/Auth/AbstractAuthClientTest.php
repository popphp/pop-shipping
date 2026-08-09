<?php

namespace Pop\Shipping\Test\Auth;

use Pop\Shipping\Test\TestAsset\TestAuthClient;
use PHPUnit\Framework\TestCase;

class AbstractAuthClientTest extends TestCase
{

    public function testSaveTokenDataToFileWithoutPriorTokenDataTriggersNoWarnings()
    {
        $tokenFile  = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');
        $authClient = new TestAuthClient();

        $warnings = [];
        set_error_handler(function (int $errno, string $errstr) use (&$warnings): bool {
            $warnings[] = $errstr;
            return true;
        }, E_WARNING);

        try {
            $authClient->saveTokenDataToFile($tokenFile);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);

        unlink($tokenFile);
    }

}
