<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subscription;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            return parent::getEloquentQuery();
        }
        
        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->role == 'admin') {
            return true;
        }

        return false;
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Toko')
                    ->options(User::all()->pluck('name', 'id')->toArray())
                    ->required()
                    ->hidden(fn() => auth()->user()->role == 'store'),
                Forms\Components\Toggle::make('is_acive')
                    ->label('Aktif')
                    ->hidden(fn() => auth()->user()->role == 'store'),
                Forms\Components\Repeater::make('subscriptionPayment')
                    ->label('Metode Pembayaran')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('proof')
                            ->label('Bukti Transfer Ke Rekening 21321312312 (BCA) A/N Rafli Sebesar Rp. 50.000')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Success',
                                'failed' => 'Failed',
                            ])
                            ->required()
                            ->columnSpanFull()
                            ->hidden(fn() => auth()->user()->role == 'store'),
                    ])
                    ->columnSpanFull()
                    ->addable(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Toko')
                    ->hidden(fn() => auth()->user()->role == 'store'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Mulai')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->dateTime(),
                Tables\Columns\ImageColumn::make('subscriptionPayment.proof')
                    ->label('Bukti Pembayaran'),
                Tables\Columns\TextColumn::make('subscriptionPayment.status')
                    ->label('Status Pembayaran'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}