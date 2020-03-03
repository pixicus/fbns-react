<?php

namespace Fbns\Client;

use BinSoul\Net\Mqtt\Connection as ConnectionInterface;
use BinSoul\Net\Mqtt\Message;
use Fbns\Client\Proto\ClientInfo;
use Fbns\Client\Proto\Connect;
use Fbns\Client\Thrift\Compact\Writer;

class Connection implements ConnectionInterface
{
    const FBNS_CLIENT_CAPABILITIES = 439;
    const FBNS_ENDPOINT_CAPABILITIES = 128;
    const FBNS_APP_ID = '567310203415052';
    const FBNS_CLIENT_STACK = 3;
    const FBNS_PUBLISH_FORMAT = 1;

    /** @var AuthInterface */
    private $auth;

    /** @var string */
    private $userAgent;
    /** @var int */
    private $clientCapabilities;
    /** @var int */
    private $endpointCapabilities;
    /** @var int */
    private $publishFormat;
    /** @var bool */
    private $noAutomaticForeground;
    /** @var bool */
    private $makeUserAvailableInForeground;
    /** @var bool */
    private $isInitiallyForeground;
    /** @var int */
    private $networkType;
    /** @var int */
    private $networkSubtype;
    /** @var int */
    private $clientMqttSessionId;
    /** @var int[] */
    private $subscribeTopics;
    /** @var int */
    private $appId;
    /** @var int */
    private $clientStack;

    /**
     * Connection constructor.
     *
     * @param string $userAgent
     */
    public function __construct(AuthInterface $auth, $userAgent)
    {
        $this->auth = $auth;
        $this->userAgent = $userAgent;

        $this->clientCapabilities = self::FBNS_CLIENT_CAPABILITIES;
        $this->endpointCapabilities = self::FBNS_ENDPOINT_CAPABILITIES;
        $this->publishFormat = self::FBNS_PUBLISH_FORMAT;
        $this->noAutomaticForeground = true;
        $this->makeUserAvailableInForeground = false;
        $this->isInitiallyForeground = false;
        $this->networkType = 1;
        $this->networkSubtype = 0;
        $this->subscribeTopics = [(int) Lite::MESSAGE_TOPIC_ID, (int) Lite::REG_RESP_TOPIC_ID];
        $this->appId = self::FBNS_APP_ID;
        $this->clientStack = self::FBNS_CLIENT_STACK;
    }

    /**
     * @return string
     */
    public function toThrift()
    {
        $clientInfo = new ClientInfo();
        $clientInfo->userId = $this->auth->getUserId();
        $clientInfo->userAgent = $this->userAgent;
        $clientInfo->clientCapabilities = $this->clientCapabilities;
        $clientInfo->endpointCapabilities = $this->endpointCapabilities;
        $clientInfo->publishFormat = $this->publishFormat;
        $clientInfo->noAutomaticForeground = $this->noAutomaticForeground;
        $clientInfo->makeUserAvailableInForeground = $this->makeUserAvailableInForeground;
        $clientInfo->isInitiallyForeground = $this->isInitiallyForeground;
        $clientInfo->networkType = $this->networkType;
        $clientInfo->networkSubtype = $this->networkSubtype;
        if ($this->clientMqttSessionId === null) {
            $sessionId = (int) ((microtime(true) - strtotime('Last Monday')) * 1000);
        } else {
            $sessionId = $this->clientMqttSessionId;
        }
        $clientInfo->clientMqttSessionId = $sessionId;
        $clientInfo->subscribeTopics = [(int) Lite::MESSAGE_TOPIC_ID, (int) Lite::REG_RESP_TOPIC_ID];
        $clientInfo->clientType = $this->auth->getClientType();
        $clientInfo->appId = $this->appId;
        $clientInfo->deviceSecret = $this->auth->getDeviceSecret();
        $clientInfo->clientStack = $this->clientStack;

        $connect = new Connect();
        $connect->clientIdentifier = $this->auth->getClientId();
        $connect->clientInfo = $clientInfo;
        $connect->password = $this->auth->getPassword();

        $writer = new Writer();

        return $writer($connect->toStruct());
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return int
     */
    public function getClientCapabilities()
    {
        return $this->clientCapabilities;
    }

    /**
     * @param int $clientCapabilities
     */
    public function setClientCapabilities($clientCapabilities)
    {
        $this->clientCapabilities = $clientCapabilities;
    }

    /**
     * @return int
     */
    public function getEndpointCapabilities()
    {
        return $this->endpointCapabilities;
    }

    /**
     * @param int $endpointCapabilities
     */
    public function setEndpointCapabilities($endpointCapabilities)
    {
        $this->endpointCapabilities = $endpointCapabilities;
    }

    /**
     * @return bool
     */
    public function isNoAutomaticForeground()
    {
        return $this->noAutomaticForeground;
    }

    /**
     * @param bool $noAutomaticForeground
     */
    public function setNoAutomaticForeground($noAutomaticForeground)
    {
        $this->noAutomaticForeground = $noAutomaticForeground;
    }

    /**
     * @return bool
     */
    public function isMakeUserAvailableInForeground()
    {
        return $this->makeUserAvailableInForeground;
    }

    /**
     * @param bool $makeUserAvailableInForeground
     */
    public function setMakeUserAvailableInForeground($makeUserAvailableInForeground)
    {
        $this->makeUserAvailableInForeground = $makeUserAvailableInForeground;
    }

    /**
     * @return bool
     */
    public function isInitiallyForeground()
    {
        return $this->isInitiallyForeground;
    }

    /**
     * @param bool $isInitiallyForeground
     */
    public function setIsInitiallyForeground($isInitiallyForeground)
    {
        $this->isInitiallyForeground = $isInitiallyForeground;
    }

    /**
     * @return int
     */
    public function getNetworkType()
    {
        return $this->networkType;
    }

    /**
     * @param int $networkType
     */
    public function setNetworkType($networkType)
    {
        $this->networkType = $networkType;
    }

    /**
     * @return int
     */
    public function getNetworkSubtype()
    {
        return $this->networkSubtype;
    }

    /**
     * @param int $networkSubtype
     */
    public function setNetworkSubtype($networkSubtype)
    {
        $this->networkSubtype = $networkSubtype;
    }

    /**
     * @return int
     */
    public function getClientMqttSessionId()
    {
        return $this->clientMqttSessionId;
    }

    /**
     * @param int $clientMqttSessionId
     */
    public function setClientMqttSessionId($clientMqttSessionId)
    {
        $this->clientMqttSessionId = $clientMqttSessionId;
    }

    /**
     * @return int[]
     */
    public function getSubscribeTopics()
    {
        return $this->subscribeTopics;
    }

    /**
     * @param int[] $subscribeTopics
     */
    public function setSubscribeTopics($subscribeTopics)
    {
        $this->subscribeTopics = $subscribeTopics;
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return int
     */
    public function getClientStack()
    {
        return $this->clientStack;
    }

    /**
     * @param int $clientStack
     */
    public function setClientStack($clientStack)
    {
        $this->clientStack = $clientStack;
    }

    /**
     * @return AuthInterface
     */
    public function getAuth()
    {
        return $this->auth;
    }

    public function setAuth(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    public function getProtocol(): int
    {
        // TODO: Implement getProtocol() method.
    }

    public function getClientID(): string
    {
        return $this->auth->getClientId();
    }

    public function isCleanSession(): bool
    {
        // TODO: Implement isCleanSession() method.
    }

    public function getUsername(): string
    {
        // TODO: Implement getUsername() method.
    }

    public function getPassword(): string
    {
        // TODO: Implement getPassword() method.
    }

    public function getWill(): ?Message
    {
        // TODO: Implement getWill() method.
    }

    public function getKeepAlive(): int
    {
        return 100;
    }

    public function withProtocol(int $protocol): ConnectionInterface
    {
        // TODO: Implement withProtocol() method.
    }

    public function withClientID(string $clientID): ConnectionInterface
    {
        // TODO: Implement withClientID() method.
    }

    public function withCredentials(string $username, string $password): ConnectionInterface
    {
        // TODO: Implement withCredentials() method.
    }

    public function withWill(Message $will): ConnectionInterface
    {
        // TODO: Implement withWill() method.
    }

    public function withKeepAlive(int $timeout): ConnectionInterface
    {
        // TODO: Implement withKeepAlive() method.
    }
}
