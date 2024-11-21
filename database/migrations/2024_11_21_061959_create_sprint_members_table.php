<?php

use App\Models\User;
use App\Models\Sprint;
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
        Schema::create('sprint_members', function (Blueprint $table) {
            $table->foreignIdFor(User::class)
                ->references('id')->on('users');
            $table->foreignIdFor(Sprint::class)
                ->references('id')->on('sprints');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_creator')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprint_members');
    }
};
