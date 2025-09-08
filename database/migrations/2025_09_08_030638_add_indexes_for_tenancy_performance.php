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
        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            // Index for tenant_id (most important for tenant filtering)
            if (!Schema::hasIndex('users', 'users_tenant_id_index')) {
                $table->index('tenant_id', 'users_tenant_id_index');
            }
            
            // Composite index for tenant_id + email (for authentication)
            if (!Schema::hasIndex('users', 'users_tenant_email_index')) {
                $table->index(['tenant_id', 'email'], 'users_tenant_email_index');
            }
            
            // Index for email_verified_at (for filtering verified users)
            if (!Schema::hasIndex('users', 'users_email_verified_at_index')) {
                $table->index('email_verified_at', 'users_email_verified_at_index');
            }
        });

        // Add indexes for tenants table
        Schema::table('tenants', function (Blueprint $table) {
            // Index for is_active (for filtering active tenants)
            if (!Schema::hasIndex('tenants', 'tenants_is_active_index')) {
                $table->index('is_active', 'tenants_is_active_index');
            }
            
            // Index for email (for tenant authentication)
            if (!Schema::hasIndex('tenants', 'tenants_email_index')) {
                $table->index('email', 'tenants_email_index');
            }
        });

        // Add indexes for domains table
        Schema::table('domains', function (Blueprint $table) {
            // Index for tenant_id (for finding domains by tenant)
            if (!Schema::hasIndex('domains', 'domains_tenant_id_index')) {
                $table->index('tenant_id', 'domains_tenant_id_index');
            }
        });

        // Add indexes for exchange_rates table (if exists)
        if (Schema::hasTable('exchange_rates')) {
            Schema::table('exchange_rates', function (Blueprint $table) {
                // Index for tenant_id (for tenant filtering)
                if (!Schema::hasIndex('exchange_rates', 'exchange_rates_tenant_id_index')) {
                    $table->index('tenant_id', 'exchange_rates_tenant_id_index');
                }
                
                // Composite index for tenant_id + from_currency_id + to_currency_id
                if (!Schema::hasIndex('exchange_rates', 'exchange_rates_tenant_currencies_index')) {
                    $table->index(['tenant_id', 'from_currency_id', 'to_currency_id'], 'exchange_rates_tenant_currencies_index');
                }
            });
        }

        // Add indexes for sessions table (for performance)
        Schema::table('sessions', function (Blueprint $table) {
            // Index for user_id (for finding user sessions)
            if (!Schema::hasIndex('sessions', 'sessions_user_id_index')) {
                $table->index('user_id', 'sessions_user_id_index');
            }
            
            // Index for last_activity (for cleaning old sessions)
            if (!Schema::hasIndex('sessions', 'sessions_last_activity_index')) {
                $table->index('last_activity', 'sessions_last_activity_index');
            }
        });

        // Note: countries, provinces, cities, currencies, and exchange_rates tables
        // already have their indexes created in their respective migration files
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_tenant_id_index');
            $table->dropIndex('users_tenant_email_index');
            $table->dropIndex('users_email_verified_at_index');
        });

        // Drop indexes for tenants table
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex('tenants_is_active_index');
            $table->dropIndex('tenants_email_index');
        });

        // Drop indexes for domains table
        Schema::table('domains', function (Blueprint $table) {
            $table->dropIndex('domains_tenant_id_index');
        });

        // Drop indexes for exchange_rates table (if exists)
        if (Schema::hasTable('exchange_rates')) {
            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->dropIndex('exchange_rates_tenant_id_index');
                $table->dropIndex('exchange_rates_tenant_currencies_index');
            });
        }

        // Drop indexes for sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('sessions_user_id_index');
            $table->dropIndex('sessions_last_activity_index');
        });

        // Note: countries, provinces, cities, currencies, and exchange_rates tables
        // indexes will be dropped when their respective tables are dropped
    }
};
