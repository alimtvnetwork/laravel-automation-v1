<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table): void {
            $table->id();
            $table->string('owner_id', 120);
            $table->string('filename', 255);
            $table->string('mime', 120);
            $table->unsignedBigInteger('size');
            $table->string('path', 500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
