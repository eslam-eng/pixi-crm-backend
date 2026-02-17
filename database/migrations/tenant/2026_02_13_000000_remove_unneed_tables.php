<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('deal_custom_fields');
        Schema::dropIfExists('lead_custom_fields');
        Schema::dropIfExists('task_custom_fields');
        Schema::dropIfExists('client_custom_fields');
        Schema::dropIfExists('contact_custom_fields');
        Schema::dropIfExists('custom_fields');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_sections');
    }
};
