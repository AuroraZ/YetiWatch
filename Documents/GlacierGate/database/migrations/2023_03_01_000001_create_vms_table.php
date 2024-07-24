// database/migrations/2023_03_01_000001_create_vms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVmsTable extends Migration
{
    public function up()
    {
        Schema::create('vms', function (Blueprint $table) {
            $table->id();
            $table->integer('vmid');
            $table->string('name');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vms');
    }
}
