<?php
namespace App\Filament\Resources;

use App\Filament\Resources\EmployeesResource\Pages;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeesResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationGroup = "Employee Management";

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getGlobalSearchResultTitle(Model $record):string {
        return $record->first_name .' '. $record->last_name;
    }
    
    public static function getGloballySearchableAttributes(): array {
        return [
            'first_name', 'last_name', 'middle_name'
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record):array {
        return [
            'Country' =>$record->country->name,
            'State' =>$record->state->name,
            'City' => $record->city->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery():Builder {
        return parent::getGlobalSearchEloquentQuery()->with(['country']);
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null {
        return static::getModel()::count() > 5 ? 'warning':'success';
    }

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Department')
                    ->indicator('Department'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
                ], )
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->successNotification(Notification::make()
                ->success()
            ->title('Employee deleted')
            ->body('Employee deleted successfully')),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Relationships')
                ->schema([
                    TextEntry::make('country.name')->label('Country name'),
                    TextEntry::make('state.name')->label('State name'),
                    TextEntry::make('city.name')->label('City name'),
                    TextEntry::make('department.name')->label('Department name'),
                ])->columns(2),
            Section::make('User Name')
                ->schema([
                    TextEntry::make('first_name')->label('First name'),
                    TextEntry::make('last_name')->label('Last name'),
                    TextEntry::make('middle_name')->label('Middle name'),

                ])->columns(3),
            Section::make('User Address')
                ->schema([
                    TextEntry::make('address')->label(' Address'),
                    TextEntry::make('zip_code')->label('Zip name'),

                ])->columns(2),
            Section::make('User Dates')
                ->schema([
                    TextEntry::make('date_of_birth')->label(' DOB'),
                    TextEntry::make('date_hired')->label('Date hired'),

                ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployees::route('/create'),
            'view'   => Pages\ViewEmployees::route('/{record}'),
            'edit'   => Pages\EditEmployees::route('/{record}/edit'),
        ];
    }
}
