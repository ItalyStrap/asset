<?php

declare(strict_types=1);

namespace ItalyStrap\Asset\Test;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Asset\Asset;
use ItalyStrap\Asset\File;
use ItalyStrap\Asset\Script;
use ItalyStrap\Asset\Version\EmptyVersion;

// phpcs:disable
include_once __DIR__ . '/WPUnitBaseAsset.php';
// phpcs:enable

class ScriptTest extends WPUnitBaseAsset
{
    /**
     * @var \IntegrationTester
     */
    protected $tester;

    public function setUp(): void
    {
        // Before...
        parent::setUp();

        // Your set up methods here.
    }

    protected function getInstance(): Script
    {

        $file = new File(
            $this->finder->firstFile('style', 'css'),
            new EmptyVersion(),
            $_SERVER['WP_URL'],
            $_SERVER['WP_ROOT_FOLDER']
        );

        $config = ( new ConfigFactory() )->make([
            Asset::HANDLE   => 'handle',
            Asset::URL      => $file->url(),
            Asset::VERSION  => $file->version(),
        ]);

        return new Script($config);
    }
}
