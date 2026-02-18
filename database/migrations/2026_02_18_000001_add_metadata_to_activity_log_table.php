<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            if (!Schema::hasColumn(config('activitylog.table_name', 'activity_log'), 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('properties');
            }
            if (!Schema::hasColumn(config('activitylog.table_name', 'activity_log'), 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn(config('activitylog.table_name', 'activity_log'), 'url')) {
                $table->text('url')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn(config('activitylog.table_name', 'activity_log'), 'outlet_id')) {
                $table->unsignedBigInteger('outlet_id')->nullable()->after('url');
                $table->index(['outlet_id', 'created_at'], 'activity_log_outlet_created_idx');
            }
            if (!Schema::hasColumn(config('activitylog.table_name', 'activity_log'), 'event')) {
                $table->string('event')->nullable()->after('outlet_id');
                $table->index('event', 'activity_log_event_idx');
            }
            if (!Schema::hasColumn(config('activitylog.table_name', 'activity_log'), 'batch_uuid')) {
                $table->uuid('batch_uuid')->nullable()->after('event');
                $table->index('batch_uuid', 'activity_log_batch_uuid_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            // Only drop what we added, but checking indexes is harder in migration
            // For safety, we just attempt to drop columns if they exist
            $columns = ['ip_address', 'user_agent', 'url', 'outlet_id', 'event', 'batch_uuid'];
            $toDrop = [];
            foreach($columns as $col) {
                if (Schema::hasColumn(config('activitylog.table_name', 'activity_log'), $col)) {
                    $toDrop[] = $col;
                }
            }
            
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
