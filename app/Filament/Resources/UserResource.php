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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Manajemen User';

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo Toko')
                    ->image()
                    ->disk('public')
                    ->directory('logo-toko')
                    ->visibility('public')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Toko')
                    ->required(),
                Forms\Components\TextInput::make('username')
                    ->label('User Name')
                    ->hint('Minimal 5 karakter, tidak boleh ada spasi')
                    ->minLength(5)
                    ->unique(User::class, 'username', ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),
                Forms\Components\Select::make('role')
                    ->label('Peran')
                    ->options([
                        'admin' => 'Admin',
                        'store' => 'Toko',
                    ])
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo Toko')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Toko')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('User Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Mendaftar')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}