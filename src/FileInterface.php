<?php

declare(strict_types=1);

namespace ItalyStrap\Asset;

interface FileInterface
{
    /**
     * @return mixed
     */
    public function version();

    public function url(): string;
}
