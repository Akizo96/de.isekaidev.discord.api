<?php

namespace wcf\system\discord\resource;

use wcf\data\user\User;
use wcf\system\discord\AbstractResponse;
use wcf\system\discord\API;

class Guild extends AbstractResponse {

    /**
     * Discord Guild ID
     * @var int
     */
    private $guildID = null;

    public function __construct($guildID) {
        parent::__construct();

        $this->guildID = $guildID;
    }

    /**
     * Get information from guild, which information depends on the passed $uri
     *
     * @param string $uri
     * @return array
     */
    public function getBase($uri = '') {
        $this->api->setUri('/guilds/' . $this->guildID . $uri);
        $this->api->setMethod('GET');
        $this->api->addParameter('limit', 500);

        return $this->handleAPI();
    }

    /**
     * Get guild member
     *
     * @param \wcf\data\user\User|int $member
     * @return array|null
     */
    public function getMember($member) {
        if ($member instanceof User) {
            if ($member->discordID === null) {
                return null;
            }
            return $this->getBase('/members/' . $member->discordID);
        }

        return $this->getBase('/members/' . $member);
    }

    /**
     * Modify guild member data
     * See Discord API documentation about valid data fields
     * https://discordapp.com/developers/docs/resources/guild#modify-guild-member
     *
     * @param \wcf\data\user\User|int $member
     * @param array                   $data
     * @return bool|null
     */
    public function modifyMember($member, $data) {
        if ($member instanceof User) {
            if ($member->discordID === null) {
                return null;
            }
            $memberID = $member->discordID;
        } else {
            $memberID = $member;
        }

        $this->api->setUri('/guilds/' . $this->guildID . '/members/' . $memberID);
        $this->api->setMethod('PATCH');
        $this->api->setParameter($data);

        $response = $this->api->execute();
        if ($response !== false && $response['statusCode'] === 204) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get guild member bans
     *
     * @return array|bool
     */
    public function getBans() {
        $response = $this->getBase('/bans');
        if ($response !== false) {
            return API::formatDiscordArray($response);
        } else {
            return false;
        }
    }

    /**
     * Get guild roles
     *
     * @return array|bool
     */
    public function getRoles() {
        $response = $this->getBase('/roles');
        if ($response !== false) {
            return $this->formatRoles($response);
        } else {
            return false;
        }
    }

    /**
     * Get guild text channels
     *
     * @return mixed|bool
     */
    public function getTextChannels() {
        $response = $this->getBase('/channels');
        if ($response !== false) {
            return $this->formatChannels($response);
        } else {
            return false;
        }
    }

    /**
     * Get guild voice channels
     *
     * @return array|bool
     */
    public function getVoiceChannels() {
        $response = $this->getBase('/channels');
        if ($response !== false) {
            return $this->formatChannels($response, 'voice');
        } else {
            return false;
        }

    }

    /**
     * Format channels to have channel id as array key and remove channels of opposite type
     *
     * @param array  $channels
     * @param string $type
     * @return array
     */
    private function formatChannels($channels, $type = 'text') {
        foreach ($channels as $key => $channel) {
            if ($channel['type'] !== $type) {
                unset($channels[$key]);
            }
        }

        return API::formatDiscordArray($channels);
    }

    /**
     * Format roles to have role id as array key
     * removing @everyone role
     * removing managed roles
     *
     * @param array $roles
     * @return array
     */
    private function formatRoles($roles) {
        foreach ($roles as $key => $role) {
            if ($role['name'] === '@everyone' || $role['managed'] === true) {
                unset($roles[$key]);
            }
        }

        return API::formatDiscordArray($roles);
    }
}