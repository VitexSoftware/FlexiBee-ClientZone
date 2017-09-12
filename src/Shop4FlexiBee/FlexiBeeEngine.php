<?php

namespace Shop4FlexiBee;

/**
 * Description of Engine
 *
 * @author vitex
 */
class FlexiBeeEngine extends \Ease\Sand
{
    /**
     * Configuration
     * @var array
     */
    public $configuration = [];

    /**
     * Where do i get configuration ?
     * @var string
     */
    public $configFile = './config.json';

    /**
     * KreditFrom worker
     *
     * @param array $options Connection settings override
     */
    public function __construct($options = [])
    {
        if (isset($options['config'])) {
            $this->configFile = $options['config'];
        } else {
            if (file_exists('/etc/shop4flexibee/config.json')) {
                $this->configFile = '/etc/shop4flexibee/config.json';
            }
        }
        $this->loadConfig($this->configFile);
        parent::__construct();
    }

    /**
     * Load Configuration values from json file $this->configFile and define UPPERCASE keys
     */
    public function loadConfig($configFile)
    {
        $this->shared        = \Ease\Shared::instanced();
        $this->configuration = json_decode(file_get_contents($configFile), true);
        if (is_null($this->configuration)) {
            throw new exeption('Error reading '.$configFile);
        } else {
            foreach ($this->configuration as $configKey => $configValue) {
                if ((strtoupper($configKey) == $configKey) && (!defined($configKey))) {
                    define($configKey, $configValue);
                    $this->shared->setConfigValue($configKey, $configValue);
                }
            }
            $this->apiurl = $this->configuration['FLEXIBEE_URL'];
        }
    }
}
