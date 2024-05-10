<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')->schema([
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn (callable $set, $state) => $set('slug', Str::slug($state))
                            )->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('slug')
                            ->unique()
                            ->required()
                            ->disabled()
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
//                            ->visibility('private')
                    ])->columnSpan(1)
                        ->collapsible(),

                    Forms\Components\Section::make('Categories')->schema([
                        Forms\Components\CheckboxList::make('categories')
                            ->relationship('categories','name')
                            ->searchable()
                            ->hiddenLabel()
                    ])->columnSpan(1)
                        ->collapsible(),

                    Forms\Components\Section::make('Pricing')->schema([
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('price')
                                ->type('number')
                                ->required(),
                            Forms\Components\TextInput::make('stock')
                                ->type('number')
                                ->required(),
                        ]),
                    ])->columnSpan(1),

                ]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            RelationManagers\CategoriesRelationManager::class,
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
