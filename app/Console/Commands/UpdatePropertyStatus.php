<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdatePropertyStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'property:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status property menjadi Terjual jika batas_akhir_penawaran sudah lewat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = DB::table('property')
            ->where('status', 'Tersedia')
            ->whereDate('batas_akhir_penawaran', '<', Carbon::today())
            ->update(['status' => 'Terjual']);

        $this->info("✅ Status berhasil diupdate. Total properti diupdate: {$updated}");
    }
}
