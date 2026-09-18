<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Central constants — no magic strings allowed elsewhere.
 * Per 02-spec/02-coding-guidelines cross-language rule.
 */
final class ApiConstants
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR   = 'error';

    public const RESOURCE_STUDENTS = 'students';
    public const RESOURCE_FILES    = 'files';
}
