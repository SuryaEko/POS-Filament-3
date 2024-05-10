<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->placeholder('Name')
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->placeholder('Email')
                        ->columnSpan(1),
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('created_at')
                            ->hiddenOn('create')
                            ->disabled()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('updated_at')
                            ->hiddenOn('create')
                            ->disabled()
                            ->columnSpan(1),
                    ])->columnSpanFull()->columns(2)
                ])
                    ->columnSpan(2)
                    ->columns(2),
                Forms\Components\Section::make('Password')->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->minValue(4)
                        ->autocomplete('new-password')
                        ->placeholder('Password')
                        ->required()->same('password_confirmation'),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->autocomplete('new-password')
                        ->placeholder('Confirm Password'),
                ])
                    ->visibleOn('create')
                    ->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'super_admin' => 'danger',
                        'cashier' => 'success',
                        'customer' => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
//                Tables\Filters\SelectFilter::make('roles')
//                    ->relationship('roles', 'name')
//                    ->multiple()
//                    ->preload()
//                    ->searchable()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
