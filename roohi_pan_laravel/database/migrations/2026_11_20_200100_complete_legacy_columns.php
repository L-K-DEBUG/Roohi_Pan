<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable()->unique();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable()->change();
            });
        }

        if (! Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'remember_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->rememberToken();
            });
        }

        if (! Schema::hasColumn('users', 'updated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }

        DB::table('users')
            ->whereNull('name')
            ->update(['name' => DB::raw('username')]);

        foreach ([
            'items' => ['created_at', 'updated_at'],
            'inventory' => ['created_at'],
            'orders' => ['updated_at'],
            'order_items' => ['created_at', 'updated_at'],
        ] as $tableName => $columns) {
            foreach ($columns as $column) {
                if (! Schema::hasColumn($tableName, $column)) {
                    Schema::table($tableName, function (Blueprint $table) use ($column) {
                        $table->timestamp($column)->nullable();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // Keep compatibility columns to avoid dropping data needed by the app.
    }
};
