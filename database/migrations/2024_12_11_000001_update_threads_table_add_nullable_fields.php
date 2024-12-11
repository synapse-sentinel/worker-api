<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (Schema::hasColumn('threads', 'name')) {
                $table->string('name')->nullable()->change();
            }
            if (Schema::hasColumn('threads', 'description')) {
                $table->string('description')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            if (Schema::hasColumn('threads', 'name')) {
                $table->string('name')->nullable(false)->change();
            }
            if (Schema::hasColumn('threads', 'description')) {
                $table->string('description')->nullable(false)->change();
            }
        });
    }
};