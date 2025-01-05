<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $profileData = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $passwordData = [];

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.edit-profile';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $this->fillForms();
    }

    public function editProfileForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profile Information')
                ->aside()
                ->description('Update your account\'s profile information and email address.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                ]),
        ])
            // ->model($this->getUser())
            ->statePath('profileData');
    }

    public function updateProfile(): void
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $this->editProfileForm->getState(); // @phpstan-ignore-line

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        $this->sendSuccessNotification();
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Update Password')
                ->aside()
                ->description('Ensure your account is using long, random password to stay secure.')
                ->schema([
                    Forms\Components\TextInput::make('Current password')
                        ->password()
                        ->required()
                        ->currentPassword(),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->live(debounce: 500)
                        ->same('passwordConfirmation'),
                    Forms\Components\TextInput::make('passwordConfirmation')
                        ->password()
                        ->required()
                        ->dehydrated(false),
                ]),
        ])
            // ->model($this->getUser())
            ->statePath('passwordData');
    }

    public function updatePassword(): void
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $this->editPasswordForm->getState(); // @phpstan-ignore-line

            $this->handleRecordUpdate($this->getUser(), $data);
        } catch (Halt $exception) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        // @codeCoverageIgnoreStart
        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_'.Filament::getAuthGuard() => $data['password'],
            ]);
        }
        // @codeCoverageIgnoreEnd

        $this->editPasswordForm->fill(); // @phpstan-ignore-line

        $this->sendSuccessNotification();
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    private function getUpdateProfileFormActions(): array // @phpstan-ignore-line
    {
        return [
            Action::make('updateProfileAction')
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editProfileForm'),
        ];
    }

    private function getUpdatePasswordFormActions(): array // @phpstan-ignore-line
    {
        return [
            Action::make('updatePasswordAction')
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editPasswordForm'),
        ];
    }

    private function getUser(): Authenticatable&Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            // @codeCoverageIgnoreStart
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
            // @codeCoverageIgnoreEnd
        }

        return $user;
    }

    private function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->editProfileForm->fill($data); // @phpstan-ignore-line
        $this->editPasswordForm->fill(); // @phpstan-ignore-line
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title(__('filament-panels::pages/auth/edit-profile.notifications.saved.title'))
            ->send();
    }
}
