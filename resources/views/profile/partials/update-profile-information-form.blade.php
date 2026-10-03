<section>
    <header>
        <h2 class="md-section-title">Información del perfil</h2>

        <p class="md-subtitle mt-0.5">Actualiza el nombre y el correo electrónico de tu cuenta.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="md-label">Nombre</label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('name')" class="md-error" />
        </div>

        <div>
            <label for="email" class="md-label">Correo electrónico</label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="md-field"
            >

            <x-input-error :messages="$errors->get('email')" class="md-error" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-500/10 dark:text-amber-200">
                    <p>
                        Tu correo electrónico no está verificado.

                        <button
                            form="send-verification"
                            class="ml-1 font-medium underline underline-offset-2 hover:no-underline"
                        >
                            Reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 font-medium text-emerald-700 dark:text-emerald-300">
                            Se envió un nuevo enlace de verificación a tu correo electrónico.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <button type="submit" class="md-btn md-btn-md md-btn-filled">Guardar</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 dark:text-emerald-400"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>