<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')->schema([
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('name')
                            ->reactive()
                            ->afterStateUpdated(
                                fn(callable $set, $state, $operation) => $operation == 'create' ? $set('slug', Str::slug($state)):null
                            )->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->disabledOn('create')
                            ->dehydrated()
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('description')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columnSpanFull()->columns(4),
                ])->columnSpan(2),

                Forms\Components\Group::make([

                    Forms\Components\Section::make('Thumbnail')->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('products')
                            ->image()
                            ->hiddenLabel()
                            ->visibility('private'),
                    ])->columnSpan(1)
                        ->collapsible(),
                ]),

                Forms\Components\Section::make('Pricing')->schema([
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('IDR')
                            ->required(),
                        Forms\Components\TextInput::make('stock')
                            ->type('number')
                            ->required()->disabledOn('edit'),
                    ]),
                ])->columnSpan(1)->collapsible(),

                Forms\Components\Section::make('Categories')->schema([
                    Forms\Components\CheckboxList::make('categories')
                        ->relationship('categories', 'name')
                        ->searchable()
                        ->hiddenLabel()
                ])->columnSpan(1)->collapsible(),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->visibility('private'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('categories.name')->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock'),
            ])
            ->filters([
                Tables\Filters\Filter::make('Stock')
                    ->form([
                        Forms\Components\TextInput::make('start_stock')
                            ->label('Start Stock')
                            ->reactive()
                            ->numeric()
                            ->requiredWith('end_stock')
                            ->maxValue(fn(callable $get) => $get('end_stock') ?? null)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('end_stock')
                            ->label('End Stock')
                            ->reactive()
                            ->numeric()
                            ->requiredWith('start_stock')
                            ->minValue(fn(callable $get) => $get('start_stock') ?? null)
                            ->columnSpan(1),
                    ])->columns(2)
                    ->query(function ($query, $data) {
                        if ($data['start_stock'] && $data['end_stock'])
                            $query->where('stock', '>=', $data['start_stock'])->where('stock', '<=', $data['end_stock']);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
//            RelationManagers\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
