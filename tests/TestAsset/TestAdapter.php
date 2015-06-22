<?php

namespace Pop\Shipping\Test\TestAsset;

use Pop\Shipping\Adapter\AbstractAdapter;

class TestAdapter extends AbstractAdapter
{

    public function shipTo(array $shipTo)
    {
        return;
    }

    public function shipFrom(array $shipFrom)
    {
        return;
    }

    public function setDimensions(array $dimensions, $unit = null)
    {
        return;
    }

    public function setWeight($weight, $unit = null)
    {
        return;
    }

    public function send($verifyPeer = true)
    {
        if ($verifyPeer) {
            $this->response        = 'Success';
            $this->responseCode    = 1;
            $this->responseMessage = 'OK';
            $this->rates           = [
                'Express'  => '10.05',
                'Standard' => '7.50'
            ];
        } else {
            $this->response        = 'Error';
            $this->responseCode    = 2;
            $this->responseMessage = 'Not OK';
        }
    }

    public function isSuccess()
    {
        return ($this->responseCode == 1);
    }

    public function isError()
    {
        return ($this->responseCode != 1);
    }

}