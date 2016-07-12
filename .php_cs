<?php

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(array(
        'long_array_syntax',
        'single_quote',
        'pre_increment',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__)
    )
;

// vim:ft=php
