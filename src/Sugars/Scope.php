<?php

namespace Lxj\ClosurePHP\Sugars;

interface Scope
{
    const PUBLIC = 'public';
    const PROTECTED = 'protected';
    const PRIVATE = 'private';
    const UNKNOWN = 'unknown';

    const ALL_SCOPES = [
        self::PUBLIC,
        self::PROTECTED,
        self::PRIVATE,
    ];
}
