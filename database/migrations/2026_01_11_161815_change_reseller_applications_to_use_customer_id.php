<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if user_id column exists
        $hasUserIdColumn = \DB::select("SHOW COLUMNS FROM reseller_applications LIKE 'user_id'");
        
        if (!empty($hasUserIdColumn)) {
            // Get all foreign keys for the table
            $foreignKeys = \DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'reseller_applications' 
                AND COLUMN_NAME = 'user_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            // Drop foreign key if exists
            foreach ($foreignKeys as $fk) {
                \DB::statement("ALTER TABLE reseller_applications DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
            
            // Drop the column
            \DB::statement("ALTER TABLE reseller_applications DROP COLUMN user_id");
        }
        
        // Check if customer_id already exists
        $hasCustomerIdColumn = \DB::select("SHOW COLUMNS FROM reseller_applications LIKE 'customer_id'");
        
        if (empty($hasCustomerIdColumn)) {
            // Add customer_id column with foreign key
            \DB::statement("
                ALTER TABLE reseller_applications 
                ADD COLUMN customer_id BIGINT UNSIGNED NOT NULL AFTER id,
                ADD CONSTRAINT reseller_applications_customer_id_foreign 
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasCustomerIdColumn = \DB::select("SHOW COLUMNS FROM reseller_applications LIKE 'customer_id'");
        
        if (!empty($hasCustomerIdColumn)) {
            $foreignKeys = \DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'reseller_applications' 
                AND COLUMN_NAME = 'customer_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                \DB::statement("ALTER TABLE reseller_applications DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
            
            \DB::statement("ALTER TABLE reseller_applications DROP COLUMN customer_id");
        }
        
        $hasUserIdColumn = \DB::select("SHOW COLUMNS FROM reseller_applications LIKE 'user_id'");
        
        if (empty($hasUserIdColumn)) {
            \DB::statement("
                ALTER TABLE reseller_applications 
                ADD COLUMN user_id BIGINT UNSIGNED NOT NULL AFTER id,
                ADD CONSTRAINT reseller_applications_user_id_foreign 
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ");
        }
    }
};
