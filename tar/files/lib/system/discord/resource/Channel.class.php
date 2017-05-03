<?php

namespace wcf\system\discord\resource;


use wcf\system\discord\AbstractResponse;
use wcf\system\discord\API;

class Channel extends AbstractResponse {
    /**
     * Discord Channel ID
     * @var int
     */
    private $channelID = null;

    public function __construct($channelID) {
        parent::__construct();

        $this->channelID = $channelID;
    }

    /**
     * Get information from channel, which information depends on the passed $uri
     *
     * @param string $uri
     * @return array
     */
    public function getBase($uri = '') {
        $this->api->setUri('/channels/' . $this->channelID . $uri);
        $this->api->setMethod('GET');
        $this->api->addParameter('limit', 500);

        return $this->handleAPI();
    }

    /**
     * Get pinned channel messages
     *
     * @return array
     */
    public function getPinnedMessages() {
        return API::formatDiscordArray($this->getBase('/pins'));
    }

    /**
     * Post a message to the channel
     * See Discord API documentation about valid message fields
     * https://discordapp.com/developers/docs/resources/channel#create-message
     *
     * @param $messageData
     * @return array
     */
    public function postMessage($messageData) {
        $this->api->setUri('/channels/' . $this->channelID . '/messages');
        $this->api->setMethod('POST');
        $this->api->setParameter($messageData);

        return $this->handleAPI();
    }

}