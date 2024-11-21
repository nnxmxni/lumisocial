<?php

use App\Models\User;
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
        Schema::create('one_time_passwords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignIdFor(User::class)
                ->references('id')->on('users')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('token');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onetimepasswords');
    }
};
