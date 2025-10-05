<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Convertir el texto existente a JSONB
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING data::jsonb');
    }

    public function down(): void
    {
        // Revertir a texto
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::text');
    }
};

