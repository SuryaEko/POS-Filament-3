<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Orders';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        $update_total = fn($get, $set) => $set('../../total', collect($get('../../order_details'))->sum('total'));
        return $form
            ->schema([
                Forms\Components\Section::make('Information')
                    ->schema(self::getDetailFormSchema())->columnSpan(1),
                Forms\Components\Section::make('Order Detail')
                    ->schema([
                        Forms\Components\Repeater::make('order_details')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateHydrated($update_total)
                                    ->afterStateUpdated(function ($state, $get, $set) use ($update_total) {
                                        $product_price = Product::find($state)?->price ?? 0;
                                        $quantity = $get('quantity') ?? 0;

                                        $set('price', $product_price);
                                        $set('total', $product_price * $quantity);
                                        $update_total($get, $set);
                                    })
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->live(onBlur: true)
                                    ->numeric()
                                    ->maxValue(function ($get) {
                                        $product_stock = Product::find($get('product_id'))?->stock ?? 0;
                                        return $product_stock;
                                    })
                                    ->afterStateHydrated($update_total)
                                    ->afterStateUpdated(function ($state, $get, $set) use ($update_total) {
                                        $product_price = $get('price') ?? 0;
                                        $quantity = $state;

                                        $set('total', $product_price * $quantity);
                                        $update_total($get, $set);
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('total')
                                    ->disabled()
                                    ->dehydrated(),
                            ])->columns(4),
                        Forms\Components\TextInput::make('total')
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                if (!$state)
                                    $component->state(0);
                            }),
                    ])->columnSpan(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cashier.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('IDR')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(Order::$statuses)
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getDetailFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('invoice_number')
                ->required()
                ->unique(ignoreRecord: true)
                ->dehydrated()
                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                    if (!$state)
                        $component->state(Order::getInvoiceNumber());
                })->disabled(),
            Forms\Components\Select::make('customer_id')
                ->relationship('customer', 'name')
                ->preload()
                ->searchable()
                ->required()
                ->createOptionForm(self::getCustomerFormSchema())
                ->editOptionForm(self::getCustomerFormSchema())
                ->columnSpan(1),
            Forms\Components\Select::make('cashier_id')
                ->relationship('cashier', 'name')
                ->preload()
                ->searchable()
                ->required()
                ->columnSpan(1),
            Forms\Components\ToggleButtons::make('status')
                ->inline()
                ->options(Order::$statuses)
                ->required(),
        ];
    }

    public static function getCustomerFormSchema()
    {
        return [
            Forms\Components\Section::make('Information')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->preload()
                        ->searchable()
                        ->createOptionForm(UserResource::getFormSchema(roles: ['customer'])),

                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email(),
                    Forms\Components\TextInput::make('phone')
                        ->tel(),
                    Forms\Components\Textarea::make('address'),
                ]),
        ];
    }
}
