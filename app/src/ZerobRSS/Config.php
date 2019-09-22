<?php
declare(strict_types=1);

namespace ZerobRSS;

class Config
{
    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function __get($key)
    {
        // Trick to do recursive stdclass conversion
        return json_decode(json_encode($this->config[$key]));
    }
}
