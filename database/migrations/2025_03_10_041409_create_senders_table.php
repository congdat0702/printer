<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('senders', function (Blueprint $table) {
            $table->id(); // Cột ID tự động tăng
            $table->string('name'); // Tên người gửi
            $table->string('contact'); // Thông tin liên hệ của người gửi
            $table->text('address')->nullable(); // Địa chỉ có thể bỏ trống
            $table->timestamps(); // Các cột created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('senders'); // Xóa bảng senders khi rollback
    }
}