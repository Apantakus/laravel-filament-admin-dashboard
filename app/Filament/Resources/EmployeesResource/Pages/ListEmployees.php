<?php

namespace App\Filament\Resources\EmployeesResource\Pages;

use App\Filament\Resources\EmployeesResource;
use App\Models\Employee;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs():array 
    {
        return [
            'All' => Tab::make(),
            'This Week' => Tab::make()->modifyQueryUsing(fn(Builder $query)=>$query->where('date_hired', '>=', now()->subWeek()))->badge(Employee::query()->where('date_hired', '>=', now()->subWeek())->count()),
            'This Month' => Tab::make()->modifyQueryUsing(fn(Builder $query)=>$query->where('date_hired', '>=', now()->subMonth()))->badge(Employee::query()->where('date_hired', '>=', now()->subMonth())->count()),
            'This Year' => Tab::make()->modifyQueryUsing(fn(Builder $query)=>$query->where('date_hired', '>=', now()->subYear()))->badge(Employee::query()->where('date_hired', '>=', now()->subYear())->count()),    
        ];
    }
}

