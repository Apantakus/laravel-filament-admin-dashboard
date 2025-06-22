<?php

namespace App\Filament\Resources\StateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function form(Form $form): Form
    {
           return $form
            ->schema([
                 Forms\Components\Section::make("Relationships")
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->searchable(true)
                            ->preload()
                            ->live()
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->required()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            }),

                        Forms\Components\Select::make('state_id')
                            ->searchable(true)
                            ->preload()
                            ->options(fn(Get $get) => State::where('country_id', $get('country_id'))->pluck('name', 'id')->toArray())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('city_id', null)),

                        Forms\Components\Select::make('city_id')
                            ->searchable(true)
                            ->preload()
                            ->live()
                            ->options(fn(Get $get) => City::where('state_id', $get('state_id'))->pluck('name', 'id')->toArray())
                            ->required(),

                        Forms\Components\Select::make('department_id')
                            ->searchable(true)
                            ->preload()
                            ->relationship(name: 'department', titleAttribute: 'name')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make("User Name")
                    ->description('Please enter the user name details')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),
                Forms\Components\Section::make("User Address")
                    ->description('Please enter your address details')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->required()
                            ->maxLength(10),
                    ])->columns(2),
                Forms\Components\Section::make('dates')

                    ->schema([Forms\Components\DatePicker::make('date_of_birth')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('date_hired')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                ->date()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_hired')
                ->date()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
