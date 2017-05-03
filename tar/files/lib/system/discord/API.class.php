<?php

namespace wcf\system\discord;

use wcf\util\HTTPRequest;

class API {

    /**
     * base API URL
     */
    const API_URL = 'https://discordapp.com/api';

    const AUTH_TYPE_BOT = 'Bot';
    const AUTH_TYPE_BEARER = 'Bearer';

    /**
     * API endpoint
     * @var string
     */
    private $uri = '';

    /**
     * HTTP method
     * @var string
     */
    private $method = 'GET';

    /**
     * post parameters
     * @var array
     */
    private $params = [];

    /**
     * Discord API auth type
     * @var string
     */
    private $authType = self::AUTH_TYPE_BOT;

    /**
     * Discord API auth token
     * @var string
     */
    private $authToken = '';

    /**
     * Set the Discord API Endpoint
     * @param string $uri Discord API endpoint
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    /**
     * HTTP method
     * @param string $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * add a post parameter
     * @param mixed $key
     * @param mixed $value
     */
    public function addParameter($key, $value) {
        if ($value === '') {
            unset($this->params[$key]);
        }

        $this->params[$key] = $value;
    }

    /**
     * set all post parameters at once
     * @param array $params
     */
    public function setParameter(array $params) {
        $this->params = $params;
    }

    /**
     * Set the authentication parameters
     * @param string $token
     * @param string $type
     */
    public function setAuthentication($token, $type = self::AUTH_TYPE_BOT) {
        $this->authToken = $token;
        $this->authType = $type;
    }

    /**
     * @return array
     */
    public function execute() {
        $request = new HTTPRequest(static::API_URL . $this->uri, ['method' => $this->method], $this->params);

        $request->addHeader('user-agent', 'DiscordBot (WoltLab Suite, v1.0.0)');

        if ($this->authToken !== '' && $this->authType !== '') {
            $request->addHeader('authorization', $this->authType . ' ' . $this->authToken);
        }

        $request->execute();
        return $request->getReply();
    }

    /**
     * format a array to serve as id => item
     *
     * @param array $array
     * @return array
     */
    public static function formatDiscordArray(array $array) {
        $formatted = [];
        foreach ($array as $item) {
            $formatted[$item['id']] = $item;
        }
        return $formatted;
    }

}