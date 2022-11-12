<?php

namespace App\Filament\Resources;

use App\Concerns\OnlyAdminConcern;
use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Filament\Resources\SubscriptionPlanResource\RelationManagers;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput\Mask;

class SubscriptionPlanResource extends Resource
{
    use OnlyAdminConcern;

    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 101;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('stripe_id')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpan(2),
                Forms\Components\TextInput::make('price')
                    ->mask(fn (Mask $mask) => $mask
                        ->patternBlocks([
                            'money' => fn (Mask $mask) => $mask
                                ->numeric()
                                ->thousandsSeparator()
                                ->decimalSeparator(),
                        ])
                        ->pattern('$money')
                    )
                    ->required(),
                Forms\Components\TextInput::make('trial_days')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\TextInput::make('monthly_credits')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Forms\Components\Select::make('subscription_plan_id')
                    ->label('Yearly plan')
                    ->options(SubscriptionPlan::whereNull('subscription_plan_id')->pluck('name', 'id')),
                Forms\Components\KeyValue::make('features')
                    ->keyLabel('Feature')
                    ->valueLabel('Detail')
                    ->columnSpan(2),
                Forms\Components\Checkbox::make('active')
                    ->label('Make this plan active')
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->label('Stripe ID')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('usd'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubscriptionPlans::route('/'),
        ];
    }
}
