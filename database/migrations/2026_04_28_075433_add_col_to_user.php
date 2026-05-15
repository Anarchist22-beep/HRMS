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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('designation')->nullable();
            $table->decimal('salary',10,2)->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('depart_id')->nullable();
            $table->string('role')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('depart_id')->references('id')->on('departments')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('designation');
            $table->dropColumn('salary');
            $table->dropColumn('project_id');
            $table->dropColumn('depart_id');
            $table->dropColumn('role');
        });
    }
};
