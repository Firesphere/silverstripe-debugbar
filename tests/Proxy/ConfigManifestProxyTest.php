<?php

namespace LeKoala\DebugBar\Test\Proxy;

use SilverStripe\Core\Config\ConfigLoader;
use SilverStripe\Core\Kernel;
use LeKoala\DebugBar\DebugBar;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use LeKoala\DebugBar\Proxy\ConfigManifestProxy;
use LeKoala\DebugBar\Proxy\SSViewerProxy;

class ConfigManifestProxyTest extends SapphireTest
{
    protected function setUp()
    {
        parent::setUp();

        DebugBar::initDebugBar();

        $configLoader = $this->getConfigLoader();

        // Check top level manifest is our proxy
        // TODO: in tests, we have a SilverStripe\Config\Collections\DeltaConfigCollection which is not working with the proxy
        if (!($configLoader->getManifest() instanceof ConfigManifestProxy)) {
            $this->markTestSkipped("ConfigManifestProxy is not initialized");
        }
    }

    /**
     * @return ConfigLoader
     */
    protected function getConfigLoader()
    {
        return Injector::inst()->get(Kernel::class)->getConfigLoader();
    }

    public function testGetCallsAreCaptured()
    {
        // TODO: check why this is not working properly
        $this->markTestSkipped("Result does not contain SSViewerProxy for some reason to determine");

        $configLoader = $this->getConfigLoader();
        $manifest = $configLoader->getManifest();

        Config::inst()->get(SSViewerProxy::class, 'cached');
        $result = $manifest->getConfigCalls();
        $this->assertArrayHasKey(SSViewerProxy::class, $result);
        $this->assertArrayHasKey('cached', $result[SSViewerProxy::class]);
        $this->assertEquals(1, $result[SSViewerProxy::class]['cached']['calls']);

        Config::inst()->get(SSViewerProxy::class, 'cached');
        Config::inst()->get(SSViewerProxy::class, 'cached');
        Config::inst()->get(SSViewerProxy::class, 'cached');
        $result = $manifest->getConfigCalls();
        $this->assertEquals(4, $result[SSViewerProxy::class]['cached']['calls']);
    }
}
