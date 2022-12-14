<?php

use App\Models\Course;
use App\Models\Group;
use App\Models\Room;
use App\Models\TimeCourse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class);
            $table->foreignIdFor(Room::class);
            $table->foreignIdFor(TimeCourse::class, 'time_id');
            $table->string('name');
            $table->json('days');
            $table->date('group_start_date');
            $table->date('group_end_date')->nullable();
            $table->boolean('active')->default(false);
            $table->integer('completed_lesson')->default(0);
            $table->integer('completed_module')->default(0);
            $table->date('next_lesson_date')->nullable();
            $table->json('lessons');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
};
