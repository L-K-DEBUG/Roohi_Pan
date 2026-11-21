<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\Item;
use App\Support\InventoryRules;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ViewFacade::composer('layouts.business', function ($view): void {
            $thresholds = InventoryRules::lowStockThresholds();
            $drinkNames = Item::query()
                ->whereIn('category', ['SOFT DRINKS (CAN)', 'SOFT DRINKS (BOTTLE)'])
                ->pluck('name');

            foreach ($drinkNames as $drinkName) {
                $thresholds[$drinkName] = 4;
            }

            $view->with(
                'lowStockItems',
                Inventory::query()
                    ->where(function ($query) use ($thresholds): void {
                        foreach ($thresholds as $itemName => $threshold) {
                            $query->orWhere(function ($query) use ($itemName, $threshold): void {
                                $query->where('item_name', $itemName)
                                    ->where('quantity', '<=', $threshold);
                            });
                        }
                    })
                    ->orderBy('item_name')
                    ->get(),
            );
        });
    }
}
