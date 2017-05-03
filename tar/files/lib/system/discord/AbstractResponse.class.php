<?php

namespace wcf\system\discord;

use wcf\util\JSON;

class AbstractResponse {

    /**
     * @var \wcf\system\discord\API
     */
    protected $api = null;

    public function __construct() {
        $this->api = new API();
    }

    public function setAuth($token, $tokenType = API::AUTH_TYPE_BOT) {
        $this->api->setAuthentication($token, $tokenType);
    }

    protected function handleAPI() {
        $response = $this->api->execute();

        return JSON::decode($response['body']);
    }
}