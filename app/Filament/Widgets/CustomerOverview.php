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
            Stat::make('Total de Clientes', Customer::count()),

            Stat::make('Clientes Inativos', Customer::where('status', 0)->count()),

            Stat::make('Total de Usuários', User::count()),

            Stat::make('Vistas únicas', '192.1k')
                ->description('32k increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Taxa de rejeição', '21%')
                ->description('7% decrease')
                ->color('danger')
                ->descriptionIcon('heroicon-m-arrow-trending-down'),
            Stat::make('Tempo médio na página', '6:12')
                ->description('3% increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
