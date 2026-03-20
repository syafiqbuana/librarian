<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- nis --}}
        <div class="mt-4">
            <x-input-label for="nis" :value="__('NIS')" />
            <x-text-input id="nis" class="block mt-1 w-full" type="number" name="nis" :value="old('nis')" required autocomplete="nis" />
            <x-input-error :messages="$errors->get('nis')" class="mt-2" />
        </div>



        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->

        {{-- Gender --}}
        <div class="mt-4">
            <x-input-label for="gender" :value="__('Jenis Kelamin')" />
            <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <option value="">-- Pilih Jenis Kelamin --</option>
                @foreach ($genderOptions as $gender)
                    <option value="{{ $gender }}" {{ old('gender') == $gender ? 'selected': ''}}>{{ $gender }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>
        {{-- class --}}

        <div class="mt-4">
            <x-input-label for="class" :value="__('Kelas')" />
            <select name="class" id="class" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($classOptions as $class)
                    <option value="{{ $class }}" {{ old('class') == $class ? 'selected': ''}}>{{ $class }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('class')" class="mt-2" />
        </div>

        {{-- major --}}

        <div class="mt-4">
            <x-input-label for="major" :value="__('Jurusan')"/>
            <select name="major" id="major" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <option value="">-- Pilih Jurusan --</option>
                @foreach ($majorOptions as $major )
                    <option value="{{ $major }}" {{ old('major') == $major ? 'selected': ''}}>{{ $major }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('major')" class="mt-2" />
        </div>
        {{-- Birth date --}}
        <div class="mt-4">
            <x-input-label for="birth_date" :value="__('Tanggal Lahir')" />
            <x-text-input id="birth_date" class="block mt-1 w-full"
                            type="date"
                            name="birth_date"
                            required autocomplete="birth_date"
                            :value="old('birth_date')" />
            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Nomor Telepon')" />

            <x-text-input id="phone" class="block mt-1 w-full"
                            type="number"
                            name="phone"
                            required autocomplete="phone"
                            :value="old('phone')" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="address" :value="__('Alamat')" />

            <x-text-input id="address" class="block mt-1 w-full"
                            type="text"
                            name="address"
                            required autocomplete="address"
                            :value=" old('address')"
                            />

            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>  

                <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah Punya Akun ?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
