public function up(): void
{
Schema::table('task_instances', function (Blueprint $table) {
$table->integer('generate_offset_days')->default(0)->after('deadline');
});
}

public function down(): void
{
Schema::table('task_instances', function (Blueprint $table) {
$table->dropColumn('generate_offset_days');
});
}