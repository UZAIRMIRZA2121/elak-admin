<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use Carbon\Carbon;

class DeleteExpiredCarts extends Command
{
    protected $signature = 'cart:delete-expired';

    protected $description = 'Delete expired pending carts after 5 minutes';

    public function handle()
    {
         Cart::where('status', 'approved')
            ->where('created_at', '<=', Carbon::now()->subMinutes(15))
            ->delete();

        $deleted = Cart::where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subMinutes(5))
            ->delete();

        $this->info("Deleted carts: " . $deleted);
    }
}