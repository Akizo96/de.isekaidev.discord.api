<?php

namespace wcf\system\discord;

use wcf\system\discord\API;
use wcf\system\exception\SystemException;

class Webhook {

    /**
     * Webhook ID
     * @var int
     */
    private $id = null;

    /**
     * Webhook Token
     * @var string
     */
    private $token = '';

    /**
     * Webhook username
     * @var string
     */
    private $username = '';

    /**
     * Webhook avatar
     * @var string
     */
    private $avatar_url = '';

    /**
     * content of the message
     * @var string
     */
    private $content = '';

    /**
     * tts message
     * @var boolean
     */
    private $tts = false;

    /**
     * embed messages
     * @var array
     */
    private $embeds = [];

    /**
     * Creates a new Webhook object
     *
     * @param int    $id    Webhook ID
     * @param string $token Webhook Token
     */
    public function __construct($id, $token) {
        $this->id = $id;
        $this->token = $token;
    }

    /**
     * Set author username - Overrides the username configured in Discord
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Set author avatar - Overrides the avatar configure in Discord
     *
     * @param string $avatar_url URL to a Image
     */
    public function setAvatar($avatar_url) {
        $this->avatar_url = $avatar_url;
    }

    /**
     * Set content of the message
     *
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Set if it is a tts message
     *
     * @param boolean $tts
     */
    public function setTTS($tts) {
        $this->tts = $tts;
    }

    /**
     * Add an embed message
     * You can find all possible fields in Discords documentation
     * https://discordapp.com/developers/docs/resources/channel#embed-object
     *
     * @param array $embed
     */
    public function addEmbed($embed) {
        $this->embeds[] = $embed;
    }

    /**
     * format to discord format
     *
     * @return array
     */
    public function getData() {
        $data = [];
        if ($this->content !== '') {
            $data['content'] = $this->content;
        }

        if ($this->username !== '') {
            $data['username'] = $this->username;
        }

        if ($this->avatar_url !== '') {
            $data['avatar_url'] = $this->avatar_url;
        }

        if ($this->tts === true) {
            $data['tts'] = true;
        }

        if (count($this->embeds) > 0) {
            $data['embeds'] = $this->embeds;
        }
        return $data;
    }

    /**
     * sends the webhook object
     *
     * @throws SystemException
     */
    public function send() {
        if ($this->content === '' && count($this->embeds) === 0) {
            throw new SystemException('atleast one of these fields must bet set: content,embeds');
        }

        $api = new API();
        $api->setUri('/webhooks/' . $this->id . '/' . $this->token);
        $api->setMethod('POST');
        $api->setParameter($this->getData());
        $api->execute();
    }
}