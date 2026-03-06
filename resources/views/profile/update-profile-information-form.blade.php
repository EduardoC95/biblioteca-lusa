<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div
                x-data="{
                    photoName: null,
                    photoPreview: null,
                    setPreview(files) {
                        if (!files || !files[0]) return;
                        this.photoName = files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.photoPreview = e.target.result; };
                        reader.readAsDataURL(files[0]);
                    },
                    handleDrop(event) {
                        const files = event.dataTransfer.files;
                        if (!files || !files.length) return;
                        this.$refs.photo.files = files;
                        this.$refs.photo.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }"
                class="col-span-6"
            >
                <input
                    type="file"
                    id="photo"
                    class="hidden"
                    accept="image/*"
                    wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="setPreview($event.target.files)"
                />

                <div class="rounded-xl border border-cyan-300/20 bg-slate-900/40 p-4">
                    <h3 class="font-display text-xl text-cyan-200">Profile Picture</h3>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="mb-2 text-sm font-semibold">Current</p>

                            <div class="flex h-40 items-center justify-center overflow-hidden rounded-lg border border-cyan-300/25 bg-slate-950/40">
                                <template x-if="!photoPreview">
                                    <img
                                        src="{{ $this->user->profile_photo_url }}"
                                        alt="Current profile picture"
                                        class="h-full w-full object-cover"
                                        onerror="this.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';"
                                    />
                                </template>

                                <template x-if="photoPreview">
                                    <img x-bind:src="photoPreview" alt="New profile picture preview" class="h-full w-full object-cover" />
                                </template>

                                <div class="hidden h-full w-full items-center justify-center text-sm text-slate-500">Sem foto</div>
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-sm font-semibold">Upload</p>

                            <div
                                class="flex h-40 cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-cyan-300/35 bg-slate-950/30 px-4 text-center"
                                x-on:click="$refs.photo.click()"
                                x-on:dragover.prevent
                                x-on:drop.prevent="handleDrop($event)"
                            >
                                <span class="text-sm">Drag and drop an image here</span>
                                <span class="mt-1 text-xs text-slate-500">or click to browse</span>
                            </div>

                            <p class="mt-2 text-xs text-slate-500" x-show="photoName" x-text="photoName"></p>

                            <div class="mt-3 flex gap-2">
                                <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()">
                                    {{ __('Browse') }}
                                </x-secondary-button>

                                @if ($this->user->profile_photo_path)
                                    <x-secondary-button type="button" wire:click="deleteProfilePhoto">
                                        {{ __('Remove') }}
                                    </x-secondary-button>
                                @endif
                            </div>

                            <p class="mt-2 text-xs text-slate-500">Max file size: 4MB. Formats: JPG, PNG, WEBP.</p>
                            <x-input-error for="photo" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-slate-300 hover:text-cyan-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-300" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
