<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Buku') }}
        </h2>
    </x-slot>

    <div class="mt-12 px-6 w-full flex flex-col gap-6">

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

        <h1 class="text-2xl font-bold text-gray-800 mb-8">
            Buku Terbaru
        </h1>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

            @foreach ($latestBooks as $book)
                <div
                    class="bg-white rounded-lg shadow-sm hover:shadow-lg transition duration-300 p-3  flex flex-col gap-1">
                    <div class="bg-slate-200 rounded-md p-2 border border-black">
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                            class="w-full h-[220px] object-cover rounded-md">
                        <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 text-center min-h-[40px]">
                            {{ $book->title }}
                        </h3>

                        @foreach ($latestBooks as $book)
                            <div class="flex flex-row gap-1 justify-beetween ">
                                @foreach ($book->genres as $genre)
                                    <span
                                        class="text-xs text-gray-600 rounded-full bg-lime-300 px-1">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>


                    <div class="mt-2 flex flex-col flex-grow gap-1 bg-slate-200 rounded-md p-2">

                        {{-- Author --}}
                        <p class="text-xs text-gray-600 ">
                            Penulis : <span class="font-medium">{{ $book->author }}</span>
                        </p>

                        {{-- Rack Location --}}
                        <p class="text-xs text-gray-600 ">
                            Rak: <span class="font-medium">{{ $book->rack_location }}</span>
                        </p>

                        {{-- Stock --}}
                        <p class="text-xs text-gray-600 ">
                            Stok:
                            <span class="font-semibold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $book->stock }}
                            </span>
                        </p>

                        {{-- Borrow Button --}}
                        <button
                            class="mt-auto bg-emerald-500 hover:bg-emerald-600 text-white text-sm py-2 rounded-md transition">
                            Tambah Ke Keranjang
                        </button>

                    </div>

                </div>
            @endforeach
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            Semua Buku
        </h1>


        <div class="grid grid-cols-2 md:grid-cols-3 w-full lg:grid-cols-5 gap-6">

            @foreach ($allBooks as $book)
                <div
                    class="bg-white rounded-lg shadow-sm hover:shadow-lg transition duration-300 p-3  flex flex-col gap-1">
                    <div class="bg-slate-200 rounded-md p-2 border border-black">
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                            class="w-full h-[220px] object-cover rounded-md">
                        <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 text-center min-h-[40px]">
                            {{ $book->title }}
                        </h3>

                        @foreach ($latestBooks as $book)
                            <div class="flex flex-row gap-1 justify-beetween ">
                                @foreach ($book->genres as $genre)
                                    <span
                                        class="text-xs text-gray-600 rounded-full bg-lime-300 px-1">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>


                    <div class="mt-2 flex flex-col flex-grow gap-2 bg-slate-200 rounded-md p-3">

                        {{-- Author --}}
                        <p class="text-xs text-gray-600">
                            Penulis : <span class="font-medium">{{ $book->author }}</span>
                        </p>

                        {{-- Rack Location --}}
                        <p class="text-xs text-gray-600">
                            Rak: <span class="font-medium">{{ $book->rack_location }}</span>
                        </p>

                        {{-- Stock --}}
                        <p class="text-xs text-gray-600">
                            Stok:
                            <span class="font-semibold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $book->stock }}
                            </span>
                        </p>

                        {{-- Button --}}
                        <div class="grid grid-cols-2 gap-2 mt-2">

                            <!-- Keranjang -->
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

                            <!-- Detail -->
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

</x-app-layout>
