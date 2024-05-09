<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Closure;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Hash;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
//            Actions\DeleteAction::make(),
            Actions\Action::make('Change Password')
                ->color('info')
                ->form([
                    TextInput::make('current_password')
                        ->password()
                        ->autocomplete('current-password')
                        ->placeholder('Current Password')
                        ->required()
                        ->rules([
                            fn(): Closure => function ($attribute, $value, $fail) {
                                if (!Hash::check($value, $this->record->password)) {
                                    $fail('The provided password does not match your current password.');
                                }
                            }
                        ]),
                    TextInput::make('password')
                        ->password()
                        ->minValue(4)
                        ->autocomplete('new-password')
                        ->placeholder('Password')
                        ->required()
                        ->same('password_confirmation'),
                    TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->autocomplete('new-password')
                        ->placeholder('Confirm Password'),
                ])
                ->action(function ($record, array $data) {
                    $record->update([
                        'password' => Hash::make($data['password']),
                    ]);
                    Notification::make()
                        ->title('Password Changed')
                        ->success()
                        ->icon('heroicon-o-check');
                }),
        ];
    }
}
