<?php

namespace App\Filament\Resources\EmployeesResource\Pages;

use App\Filament\Resources\EmployeesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;



class CreateEmployees extends CreateRecord
{
    protected static string $resource = EmployeesResource::class;

    protected function getCreatedNotificationTitle(): string {
        return "Employee created successfully";
    }

    protected function getCreatedNotification() : ?Notification {
        return Notification::make()->success()->title('Employee created')->body('The employee has been successfully created');
    }
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Debug the form data to see if country_id and others are included
    //     dd($data);

    //     return $data;
    // }
}
