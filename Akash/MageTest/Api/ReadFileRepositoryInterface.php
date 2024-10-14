<?php

namespace Akash\MageTest\Api;
interface ReadFileRepositoryInterface
{
    /**
     * Read file
     * 
     * @param string $source
     * @param string $profileName
     * @return mixed
     */
    public function readFile($source, $profileName);
}