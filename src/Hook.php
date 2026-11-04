<?php

/**
 * The name of EventName before 1.1.0.
 *
 * @deprecated 1.1.0 Use EventName, Hook is removed in 2.0.
 */

declare(strict_types=1);

namespace ItalyStrap\Asset;

\class_alias(EventName::class, __NAMESPACE__ . '\\Hook');
