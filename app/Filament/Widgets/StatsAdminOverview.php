<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
        Stat::make('Users', User::query()->count())
            ->description('All users from the database')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
        Stat::make('Teams', Team::query()->count())
            ->description('All teams from the database')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart([7, 2, 10, 3, 15, 4, 17]),
        Stat::make('Employees', Employee::query()->count())
            ->description('All teams from the database')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
        ];
    }
}
