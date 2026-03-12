<x-action-section>
    <x-slot name="title">
        {{ __('Two Factor Authentication') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Add additional security to your account using two factor authentication.') }}
    </x-slot>

    <x-slot name="content">
        <div class="space-y-5">
            <h3 class="text-lg font-medium text-cyan-200">
                @if ($this->enabled)
                    @if ($showingConfirmation)
                        {{ __('Finish enabling two factor authentication.') }}
                    @else
                        {{ __('You have enabled two factor authentication.') }}
                    @endif
                @else
                    {{ __('You have not enabled two factor authentication.') }}
                @endif
            </h3>

            <div class="max-w-xl text-sm text-slate-300">
                <p>
                    {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                </p>
            </div>

            @if (! $this->enabled)
                <div class="mt-5 flex items-center gap-3">
                    <x-button
                        type="button"
                        wire:click="startEnableTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Enable') }}
                    </x-button>
                </div>

                @if ($confirmingEnableInline)
                    <div class="mt-6 rounded-2xl border border-[color:var(--wood-border)] bg-[color:var(--wood-panel)] p-6 shadow-lg">
                        <h3 class="text-xl font-semibold text-[color:var(--wood-heading)]">
                            {{ __('Enable Two Factor Authentication') }}
                        </h3>

                        <div class="mt-3 text-sm text-[color:var(--wood-text)]">
                            {{ __('Please confirm your password to continue. After that, you will be able to scan the QR code with your authenticator app and complete the setup.') }}
                        </div>

                        <div class="mt-4 max-w-xl">
                            <x-input
                                type="password"
                                class="mt-1 block w-full md:w-3/4"
                                autocomplete="current-password"
                                placeholder="{{ __('Password') }}"
                                wire:model="password"
                                wire:keydown.enter="proceedEnableTwoFactorAuthentication"
                            />

                            <x-input-error for="password" class="mt-2" />
                        </div>

                        <div class="mt-6 flex flex-wrap justify-end gap-3">
                            <x-secondary-button
                                type="button"
                                wire:click="cancelEnableTwoFactorAuthentication"
                            >
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-button
                                type="button"
                                wire:click="proceedEnableTwoFactorAuthentication"
                                wire:loading.attr="disabled"
                            >
                                {{ __('Continue') }}
                            </x-button>
                        </div>
                    </div>
                @endif
            @endif

            @if ($this->enabled)
                <div class="mt-6 rounded-2xl border border-[color:var(--wood-border)] bg-[color:var(--wood-panel)] p-6 shadow-lg space-y-5">
                    @if ($showingQrCode)
                        <div class="max-w-xl text-sm text-slate-300">
                            <p class="font-semibold">
                                @if ($showingConfirmation)
                                    {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                                @else
                                    {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                                @endif
                            </p>
                        </div>

                        <div class="inline-block rounded-xl border border-[color:var(--wood-border)] bg-slate-900/70 p-3">
                            {!! $this->user->twoFactorQrCodeSvg() !!}
                        </div>

                        <div class="max-w-xl text-sm text-slate-300">
                            <p class="font-semibold break-all">
                                {{ __('Setup Key') }}: {{ decrypt($this->user->two_factor_secret) }}
                            </p>
                        </div>

                        @if ($showingConfirmation)
                            <div class="max-w-md">
                                <x-label for="code" value="{{ __('Code') }}" />

                                <x-input
                                    id="code"
                                    type="text"
                                    name="code"
                                    class="block mt-1 w-full md:w-1/2"
                                    inputmode="numeric"
                                    autofocus
                                    autocomplete="one-time-code"
                                    wire:model="code"
                                    wire:keydown.enter="confirmTwoFactorAuthentication"
                                />

                                <x-input-error for="code" class="mt-2" />
                            </div>
                        @endif
                    @endif

                    @if ($showingRecoveryCodes)
                        <div class="max-w-xl text-sm text-slate-300">
                            <p class="font-semibold">
                                {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                            </p>
                        </div>

                        <div class="grid gap-2 max-w-xl px-4 py-4 font-mono text-sm bg-slate-900/70 rounded-lg border border-[color:var(--wood-border)]">
                            @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                                <div>{{ $code }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    @if ($showingRecoveryCodes)
                        <x-secondary-button type="button" wire:click="regenerateRecoveryCodes" wire:loading.attr="disabled">
                            {{ __('Regenerate Recovery Codes') }}
                        </x-secondary-button>
                    @elseif ($showingConfirmation)
                        <x-button type="button" wire:click="confirmTwoFactorAuthentication" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </x-button>
                    @else
                        <x-secondary-button type="button" wire:click="showRecoveryCodes" wire:loading.attr="disabled">
                            {{ __('Show Recovery Codes') }}
                        </x-secondary-button>
                    @endif

                    @if ($showingConfirmation)
                        <x-secondary-button type="button" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    @else
                        <x-danger-button type="button" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </x-danger-button>
                    @endif
                </div>
            @endif
        </div>
    </x-slot>
</x-action-section>

