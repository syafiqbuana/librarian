<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <x-heroicon-o-book-open class="w-6 h-6 text-gray-700" />
            Detail Buku
        </h2>
    </x-slot>

    <div class="py-10">



        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white shadow-lg rounded-2xl p-6 grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Cover Buku -->
                <div class="flex justify-center">
                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                        class="w-64 h-80 object-cover rounded-xl shadow-md">
                </div>

                <!-- Detail Buku -->
                <div class="md:col-span-2 space-y-4">

                    <!-- Judul -->
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $book->title }}
                    </h1>

                    <!-- Penulis -->
                    <p class="text-gray-600 flex items-center gap-2">
                        <x-heroicon-o-user class="w-5 h-5 text-gray-500" />
                        <span>Penulis: <span class="font-medium">{{ $book->author }}</span></span>
                    </p>

                    <!-- Genre -->
                    <div>
                        <p class="text-gray-600 mb-1 flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5 text-gray-500" />
                            Genre:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($book->genres as $genre)
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Stock -->
                    <p class="text-gray-600 flex items-center gap-2">
                        <x-heroicon-o-archive-box class="w-5 h-5 text-gray-500" />
                        <span>
                            Stok:
                            <span
                                class="font-semibold 
                                {{ $book->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $book->stock > 0 ? $book->stock . ' tersedia' : 'Habis' }}
                            </span>
                        </span>
                    </p>

                    <!-- Deskripsi -->
                    <div>
                        <p class="text-gray-600 mb-1 flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-gray-500" />
                            Deskripsi:
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $book->description }}
                        </p>
                    </div>

                    <!-- Button -->
                    <div class="pt-4 flex gap-3">

                        <!-- Add to Cart -->
                        <form action="{{ url('/cart/add', $book->id) }}" method="POST">
                            @csrf
                            <button
                                class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 
                                       text-white px-5 py-2 rounded-lg shadow transition">
                                <x-heroicon-o-shopping-cart class="w-5 h-5 text-white" />
                                Add to Cart
                            </button>
                        </form>

                        <!-- Kembali -->
                        <a href="{{ url()->previous() }}"
                            class="flex items-center gap-2 bg-gray-200 hover:bg-gray-300 
                                   text-gray-800 px-5 py-2 rounded-lg transition">
                            <x-heroicon-o-arrow-left class="w-5 h-5" />
                            Kembali
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
