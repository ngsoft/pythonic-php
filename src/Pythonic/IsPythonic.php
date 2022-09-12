<?php

declare(strict_types=1);

namespace Pythonic;

use Attribute;

/**
 * Defines Internal PHP Methods as Pythonic
 * to be used with __dir__()
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class IsPythonic
{

}
