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

    public function __construct($channelID) {
        parent::__construct();

        $this->guildID = $channelID;
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
     * @param array $data
     * @return bool|null
     */
    public function modifyMember($member, $data) {
        if ($member instanceof User) {
            if ($member->discordID === null) {
                return null;
            }
            $memberID = $member->discordID;
        }else{
            $memberID = $member;
        }

        $this->api->setUri('/guilds/' . $this->guildID . '/members/' . $memberID);
        $this->api->setMethod('PATCH');
        $this->api->setParameter($data);

        $response = $this->api->execute();
        if($response['statusCode'] === 204){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get guild member bans
     *
     * @return array
     */
    public function getBans() {
        return API::formatDiscordArray($this->getBase('/bans'));
    }

    /**
     * Get guild roles
     *
     * @return array
     */
    public function getRoles() {
        return $this->formatRoles($this->getBase('/roles'));
    }

    /**
     * Get guild text channels
     *
     * @return mixed
     */
    public function getTextChannels() {
        return $this->formatChannels($this->getBase('/channels'));
    }

    /**
     * Get guild voice channels
     *
     * @return array
     */
    public function getVoiceChannels() {
        return $this->formatChannels($this->getBase('/channels'), 'voice');
    }

    /**
     * Format channels to have channel id as array key and remove channels of opposite type
     *
     * @param array  $channels
     * @param string $type
     * @return array
     */
    public function formatChannels($channels, $type = 'text') {
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