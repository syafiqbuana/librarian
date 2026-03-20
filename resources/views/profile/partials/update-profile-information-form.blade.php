<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        {{-- update phone and address --}}
        <div>
            <x-input-label for="phone" :value="__('Nomor Telepon')" />
            <x-text-input id="phone" name="phone" type="number" class="mt-1 block w-full" :value="old('phone', auth()->user()->studentDetail?->phone)"
                required autofocus autocomplete="phone" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>
        <div>
            <x-input-label for="address" :value="__('Alamat')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', auth()->user()->studentDetail?->address)"
                required autofocus autocomplete="address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="nis" :value="__('NIS')" />
            <x-text-input id="nis" name="nis" type="number" class="mt-1 block w-full" :value="old('nis', auth()->user()->studentDetail?->nis)"
                required autofocus autocomplete="nis" />
            <x-input-error class="mt-2" :messages="$errors->get('nis')" />
        </div>

        <div>
            <x-input-label for="birth-date" :value="__('Tanggal Lahir')" />
            <x-text-input type="date" name="birth_date" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" :value="old(
                'birth_date',
                auth()->user()->studentDetail?->birth_date
                    ? \Carbon\Carbon::parse(auth()->user()->studentDetail?->birth_date)->format('Y-m-d')
                    : '',
            )" />
        </div>

        <div>
            <x-input-label for="gender" :value="__('Jenis Kelamin')" />
            <select id="gender" name="gender"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                :value="old('gender', $user->studentDetail->gender)" required autofocus autocomplete="gender">
                @foreach ($genderOptions as $gender)
                    <option value="{{ $gender }}"
                        {{ old('gender', auth()->user()->studentDetail->gender) == $gender ? 'selected' : '' }}>
                        {{ $gender }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="major" :value="__('Jurusan')" />
            <x-text-input id="major" name="major" type="text" class="mt-1 block w-full" :value="old('major', $user->studentDetail?->major ?? '')"
                :readonly="auth()->user()->studentDetail?->major ? true : false"></x-text-input>
            <x-input-error :messages="$errors->get('major')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="class" :value="__('Kelas')" />
            <x-text-input id="class" name="class" class="mt-1 block w-full" :value="old('class', $user->studentDetail?->class ?? '')"
                :readonly="auth()->user()->studentDetail?->class ? true : false"></x-text-input>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
