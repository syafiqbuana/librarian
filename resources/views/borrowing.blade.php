<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Riwayat Peminjaman</h2>
    </x-slot>

    <div class="p-6">

        {{-- alert --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- data --}}
        @forelse($borrowings as $borrowing)

            <div class="bg-white shadow rounded-lg p-4 mb-4">

                <img src="{{ asset('storage/' . $borrowing->cover_image) }}" alt="">

                {{-- header --}}
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <p class="text-sm text-gray-500">
                            Tanggal Pinjam: {{ $borrowing->borrow_date->format('d M Y') }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Jatuh Tempo: {{ $borrowing->due_date->format('d M Y') }}
                        </p>
                    </div>

                    {{-- status --}}
                    <div class="flex flex-row gap-2">
                        @if ($borrowing->isOverdue())
                            <span class="bg-red-100 text-red-600 yellow-red-600 px-3 py-1 rounded-full text-xs">
                                Belum Dikembalikan
                            </span>
                            <span class="bg-red-100 text-red-600 yellow-red-600 px-3 py-1 rounded-full text-xs">Denda: Rp {{ number_format($borrowing->fine, 0, ',', '.') }}</span>
                        @elseif($borrowing->isReturnedLate())
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs">
                                Terlambat Dikembalikan
                            </span>
                            <span class="bg-red-100 text-red-600 yellow-red-600 px-3 py-1 rounded-full text-xs">Denda: Rp {{ number_format($borrowing->fine, 0, ',', '.') }}</span>
                        @elseif($borrowing->isReturned())
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs">
                                Dikembalikan
                            </span>
                        @else
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs">
                                Dipinjam
                            </span>
                        @endif
                    </div>
                </div>

                {{-- list buku --}}
                <div class="border-t pt-3">
                    <ul class="space-y-2">
                        @foreach ($borrowing->borrowingDetail as $detail)
                            <li class="flex items-center justify-between">
                                <span class="font-medium">
                                    {{ $detail->book->title }}
                                </span>
                                @if ($borrowing->isWaiting())
                                    <span class="text-sm text-gray-500">
                                        Menunggu Konfirmasi Admin
                                    </span>
                                @elseif ($borrowing->isPendingReturn())
                                    <span class="text-sm text-gray-500">
                                        Menunggu Konfirmasi Pengembalian
                                    </span>
                                @elseif ($borrowing->isReturned() || $borrowing->isReturnedLate())
                                    <span class="text-sm text-gray-500">
                                        Dikembalikan Pada : {{ $borrowing->return_date->format('d M Y') }}
                                    </span>
                                @else
                                    <form action="{{ route('borrowing.return', $borrowing->id) }}" method="POST">
                                        @csrf
                                        @if ($borrowing->isReturnedLate() || $borrowing->isReturned())
                                            <span class="">Dikembalikan Pada:
                                                {{ $borrowing->return_date->format('d M Y') }}</span>
                                        @else
                                            <button type="submit"
                                                class="bg-green-500 text-white px-3 py-1 rounded-md text-md">
                                                Kembalikan
                                            </button>
                                        @endif
                                    </form>
                                @endif

                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        @empty
            <p class="text-gray-500">Belum ada riwayat peminjaman.</p>
        @endforelse

        {{-- pagination --}}

    </div>
</x-app-layout>
