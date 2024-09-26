<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Cliente', Customer::count()),

            Stat::make('Total de Usuários', User::count())
        ];
    }
}
