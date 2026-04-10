<x-app-layout>
    @php
        $carts = session('cart', []);
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="mt-8 px-6 w-full flex flex-col gap-8">

        @if (session('success'))
            <div id="alertbox"
                class="bg-green-400 text-green-800 px-4 py-3 rounded mb-4 transition ease-in duration-300">
                {{ session('success') }}
            </div>
            <script>
                const alertbox = document.getElementById('alertbox');
                setTimeout(() => {
                    alertbox.style.display = 'none';
                    alertbox.style.opacity = '0';
                }, 1000);
            </script>
        @endif

        {{-- ===================== STUDENT DASHBOARD ===================== --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">
                Halo, {{ auth()->user()->name }} 
            </h1>
            <p class="text-sm text-gray-500 mb-5">Selamat datang di perpustakaan digital kami.</p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- Sedang Dipinjam --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Sedang Dipinjam</span>
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ $activeBorrowings }}</p>
                    <p class="text-xs text-gray-400">buku aktif</p>
                </div>

                {{-- Sudah Dikembalikan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Dikembalikan</span>
                        <div class="bg-emerald-100 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ $returnedBorrowings }}</p>
                    <p class="text-xs text-gray-400">riwayat peminjaman</p>
                </div>

                {{-- Terlambat --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Terlambat</span>
                        <div class="bg-red-100 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold {{ $overdueBorrowings > 0 ? 'text-red-500' : 'text-gray-800' }}">
                        {{ $overdueBorrowings }}
                    </p>
                    <p class="text-xs text-gray-400">melewati jatuh tempo</p>
                </div>

                {{-- Total Denda --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Denda</span>
                        <div class="bg-amber-100 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold {{ $totalFine > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                        Rp {{ number_format($totalFine, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">akumulasi denda</p>
                </div>

            </div>

            {{-- Alert jika ada buku terlambat --}}
            @if ($overdueBorrowings > 0)
                <div class="mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>
                        Kamu memiliki <strong>{{ $overdueBorrowings }} buku</strong> yang melewati batas waktu pengembalian. Segera kembalikan untuk menghindari denda lebih lanjut.
                    </span>
                </div>
            @endif
        </div>

        {{-- ===================== BUKU TERBARU ===================== --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">
                Buku Terbaru
            </h1>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                @foreach ($latestBooks as $book)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition duration-300 p-3 flex flex-col gap-1">
                        <div class="bg-slate-200 rounded-md p-2 border border-black">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                class="w-full h-[220px] object-cover rounded-md">
                            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 text-center min-h-[40px]">
                                {{ $book->title }}
                            </h3>
                            <div class="flex flex-row flex-wrap gap-1">
                                @foreach ($book->genres as $genre)
                                    <span class="text-xs text-gray-600 rounded-full bg-lime-300 px-1">
                                        {{ $genre->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-2 flex flex-col flex-grow gap-1 bg-slate-200 rounded-md p-2">
                            <p class="text-xs text-gray-600">
                                Penulis : <span class="font-medium">{{ $book->author }}</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                Rak: <span class="font-medium">{{ $book->rack_location }}</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                Stok:
                                <span class="font-semibold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $book->stock }}
                                </span>
                            </p>

                            @php
                                $isInCart = isset($carts[$book->id]);
                                $isFull = count($carts) >= 3;
                            @endphp

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                @if ($isInCart)
                                    <button disabled
                                        class="w-full flex items-center justify-center gap-2
                                        bg-gray-400 text-white text-xs font-medium
                                        py-2 px-2 rounded-lg cursor-not-allowed">
                                        ✔ Sudah di Cart
                                    </button>
                                @elseif ($isFull)
                                    <button disabled
                                        class="w-full flex items-center justify-center gap-2
                                        bg-gray-300 text-gray-600 text-xs font-medium
                                        py-2 px-2 rounded-lg cursor-not-allowed">
                                        Cart Penuh
                                    </button>
                                @else
                                    <form action="{{ url('/cart/add', $book->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center justify-center gap-2
                                            bg-emerald-500 hover:bg-emerald-600
                                            text-white text-xs font-medium
                                            py-2 px-2 rounded-lg transition shadow-sm">
                                            <x-heroicon-o-shopping-cart class="w-5 h-5 text-white shrink-0" />
                                            <span class="truncate">Keranjang</span>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('book.detail', $book->id) }}"
                                    class="w-full flex items-center justify-center gap-2
                                    bg-gray-100 hover:bg-gray-200
                                    text-gray-700 text-xs font-medium
                                    py-2 px-2 rounded-lg transition shadow-sm">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===================== SEMUA BUKU ===================== --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">
                Semua Buku
            </h1>

            <div class="grid grid-cols-2 md:grid-cols-3 w-full lg:grid-cols-5 gap-6">
                @foreach ($allBooks as $book)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition duration-300 p-3 flex flex-col gap-1">
                        <div class="bg-slate-200 rounded-md p-2 border border-black">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                class="w-full h-[220px] object-cover rounded-md">
                            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 text-center min-h-[40px]">
                                {{ $book->title }}
                            </h3>
                            <div class="flex flex-row flex-wrap gap-1">
                                @foreach ($book->genres as $genre)
                                    <span class="text-xs text-gray-600 rounded-full bg-lime-300 px-1">
                                        {{ $genre->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-2 flex flex-col flex-grow gap-2 bg-slate-200 rounded-md p-3">
                            <p class="text-xs text-gray-600">
                                Penulis : <span class="font-medium">{{ $book->author }}</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                Rak: <span class="font-medium">{{ $book->rack_location }}</span>
                            </p>
                            <p class="text-xs text-gray-600">
                                Stok:
                                <span class="font-semibold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $book->stock }}
                                </span>
                            </p>

                            <div class="grid grid-cols-2 gap-2 mt-2">
                                @php
                                    $isInCart = isset($carts[$book->id]);
                                    $isFull = count($carts) >= 3;
                                @endphp

                                @if ($isInCart)
                                    <button disabled
                                        class="w-full flex items-center justify-center gap-2
                                        bg-gray-400 text-white text-xs font-medium
                                        py-2 px-2 rounded-lg cursor-not-allowed">
                                        ✔ Sudah di Cart
                                    </button>
                                @elseif ($isFull)
                                    <button disabled
                                        class="w-full flex items-center justify-center gap-2
                                        bg-gray-300 text-gray-600 text-xs font-medium
                                        py-2 px-2 rounded-lg cursor-not-allowed">
                                        Cart Penuh
                                    </button>
                                @else
                                    <form action="{{ url('/cart/add', $book->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center justify-center gap-2
                                            bg-emerald-500 hover:bg-emerald-600
                                            text-white text-xs font-medium
                                            py-2 px-2 rounded-lg transition shadow-sm">
                                            <x-heroicon-o-shopping-cart class="w-5 h-5 text-white shrink-0" />
                                            <span class="truncate">Keranjang</span>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('book.detail', $book->id) }}"
                                    class="w-full flex items-center justify-center gap-2
                                    bg-gray-100 hover:bg-gray-200
                                    text-gray-700 text-xs font-medium
                                    py-2 px-2 rounded-lg transition shadow-sm">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>