<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone')->unique()->after('email');
            $table->string('gender')->nullable()->after('phone');
            $table->string('dob')->nullable()->after('gender');
            $table->string('otp')->nullable()->after('dob');
            $table->timestamp('otp_sent_at')->nullable()->after('otp');
            $table->timestamp('otp_verified_at')->nullable()->after('otp_sent_at');
            $table->string('provider_id')->nullable()->after('otp_verified_at');
            $table->boolean('status')->default(1)->nullable()->after('provider_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->change();
            $table->dropColumn('phone');
            $table->dropColumn('gender');
            $table->dropColumn('dob');
            $table->dropColumn('otp');
            $table->dropColumn('otp_sent_at');
            $table->dropColumn('otp_verified_at');
            $table->dropColumn('provider_id');
            $table->dropColumn('status');
            // $table->text('meta_keywords')->change();
        });
    }
}
