<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class FileRecord extends Model
{
    protected $table = 'files';

    protected $fillable = ['owner_id', 'filename', 'mime', 'size', 'path'];

    protected $casts = [
        'size' => 'integer',
    ];
}
