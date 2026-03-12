<x-action-section>
    <x-slot name="title">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete your account.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-slate-300">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="$set('confirmingUserDeletion', true)" wire:loading.attr="disabled" type="button">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>

        @if ($confirmingUserDeletion)
            <div class="mt-6 rounded-2xl border border-[color:var(--wood-border)] bg-[color:var(--wood-panel)] p-6 shadow-lg">
                <h3 class="text-xl font-semibold text-[color:var(--wood-heading)]">
                    {{ __('Delete Account') }}
                </h3>

                <div class="mt-3 text-sm text-[color:var(--wood-text)]">
                    {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </div>

                <div
                    class="mt-4"
                    x-data="{}"
                    x-init="$nextTick(() => $refs.password.focus())"
                >
                    <x-input
                        type="password"
                        class="mt-1 block w-full md:w-3/4"
                        autocomplete="current-password"
                        placeholder="{{ __('Password') }}"
                        x-ref="password"
                        wire:model="password"
                        wire:keydown.enter="deleteUser"
                    />

                    <x-input-error for="password" class="mt-2" />
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <x-secondary-button wire:click="$set('confirmingUserDeletion', false)" wire:loading.attr="disabled" type="button">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button wire:click="deleteUser" wire:loading.attr="disabled" type="button">
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>
            </div>
        @endif
    </x-slot>
</x-action-section>