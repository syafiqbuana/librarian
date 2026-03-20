<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛒 Keranjang Buku
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (empty($books) || count($books) == 0)
            <div class="text-center py-20">
                <h3 class="text-lg font-semibold text-gray-600">Keranjang masih kosong 📚</h3>
                <a href="{{ route('dashboard') }}"
                    class="mt-4 inline-block bg-emerald-500 text-white px-4 py-2 rounded-md">
                    Kembali ke Dashboard
                </a>
            </div>
        @else
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <table class="w-full text-left">


                    @php $total = 0; @endphp

                    @foreach ($books as $book)
                        @php $total += $carts[$book->id]['quantity']; @endphp

                        <tr class="border-b hover:bg-gray-50 transition">
                            <!-- Cover -->
                            <td class="p-4">
                                <img src="{{ asset('storage/' . $book->cover_image) }}"
                                    class="w-16 h-20 object-cover rounded-md shadow">
                            </td>

                            <!-- Info -->
                            <td class="p-4">
                                <h3 class="font-semibold text-gray-800">
                                    {{ $book->title }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $book->author }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    Stok: {{ $book->stock }}
                                </p>
                            </td>

                            <!-- Quantity -->
                            <td class="p-4 text-center font-semibold">
                                {{ $carts[$book->id]['quantity'] }}
                            </td>

                            <!-- Action -->
                            <td class="p-4 text-center">
                                <form action="{{ url('/cart/remove/' . $book->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="mt-6 flex justify-between items-center bg-white p-4 rounded-xl shadow">
                <h3 class="text-lg font-semibold">
                    Total Buku: {{ $total }}
                </h3>

                <form action="{{ route('borrowing.store') }}" method="POST">
                    @csrf
                      <button type="submit"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-md transition">
                    Lanjut Pinjam
                </button>
                </form>
              
            </div>

        @endif

    </div>
</x-app-layout>
