<?php

namespace Pop\Shipping\Test\TestAsset;

use Pop\Shipping\Adapter\AbstractAdapter;

class TestAdapter extends AbstractAdapter
{

    public function fetchRates(): array
    {
        return [];
    }

    public function parseRatesResponse(): array
    {
        return [];
    }

    public function getTracking(array|string|null $trackingNumbers = null): array
    {
        return [];
    }

    public function parseTrackingResponse(): array
    {
        return [];
    }

}
