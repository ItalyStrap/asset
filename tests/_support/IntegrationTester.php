<?php

declare(strict_types=1);

/**
 * The actor of the integration suite, WordPress is loaded.
 *
 * @SuppressWarnings(PHPMD)
 */
class IntegrationTester extends \Codeception\Actor
{
    use _generated\IntegrationTesterActions;
}
