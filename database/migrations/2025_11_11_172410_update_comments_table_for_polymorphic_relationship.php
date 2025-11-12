<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Remove old task_id column if it exists
        if (Schema::hasColumn('comments', 'task_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropForeign(['task_id']);
                $table->dropColumn('task_id');
            });
        }

        // Remove old tasks_id column if it exists
        if (Schema::hasColumn('comments', 'tasks_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropForeign(['tasks_id']);
                $table->dropColumn('tasks_id');
            });
        }

        // Add polymorphic columns if they don't exist
        if (!Schema::hasColumn('comments', 'commentable_type')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->string('commentable_type')->after('user_id');
            });
        }

        if (!Schema::hasColumn('comments', 'commentable_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedBigInteger('commentable_id')->after('commentable_type');
            });
        }

        // Add index for better performance
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['commentable_type', 'commentable_id']);
        });
    }

    public function down()
    {
        // Reverse the changes if needed
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['commentable_type', 'commentable_id']);
            $table->dropColumn(['commentable_type', 'commentable_id']);
        });
    }
};
