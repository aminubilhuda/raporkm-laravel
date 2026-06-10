<div wire:poll.3s="checkStatus">
    @if($isRunning)
        <div class="bg-cream rounded-field shadow-card p-5 border-l-[6px] border-l-teal-primary">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-extrabold text-teal-primary-dark flex items-center gap-2">
                        <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" />
                        Sinkronisasi Sedang Berjalan
                    </h3>
                    <p class="text-sm text-gray-500">Batch ID: {{ $batchId }}</p>
                </div>
                <form action="{{ route('tu.dapodik.cancel') }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan sinkronisasi?')">
                    @csrf
                    <button class="px-4 py-2 bg-coral/10 text-coral font-bold rounded-field hover:bg-coral/20 transition-colors">
                        <x-heroicon-o-x-circle class="w-4 h-4 inline" /> Batalkan
                    </button>
                </form>
            </div>

            {{-- Progress Bar --}}
            <div class="w-full bg-surface-base rounded-full h-3 mb-4">
                <div class="bg-teal-primary h-3 rounded-full transition-all duration-500"
                     style="width: {{ $progress }}%"></div>
            </div>

            <div class="grid grid-cols-4 gap-4 text-center text-sm">
                <div>
                    <div class="text-lg font-extrabold text-teal-primary-dark">{{ $processed }}</div>
                    <div class="text-gray-500">Diproses</div>
                </div>
                <div>
                    <div class="text-lg font-extrabold text-success">{{ $total - $failed - $pending }}</div>
                    <div class="text-gray-500">Berhasil</div>
                </div>
                <div>
                    <div class="text-lg font-extrabold text-coral">{{ $failed }}</div>
                    <div class="text-gray-500">Gagal</div>
                </div>
                <div>
                    <div class="text-lg font-extrabold text-gray-400">{{ $pending }}</div>
                    <div class="text-gray-500">Tertunda</div>
                </div>
            </div>
        </div>

    @elseif($status === 'finished' || $status === 'cancelled')
        <div class="bg-cream rounded-field shadow-card p-5 border-l-[6px] border-l-{{ $status === "finished' ? 'success' : 'gold' }}">
            <div class="flex items-center gap-3">
                <x-heroicon-o-check-circle class="w-8 h-8 {{ $status === 'finished' ? 'text-success' : 'text-gold-dark' }}" />
                <div>
                    <h3 class="font-extrabold text-teal-primary-dark">
                        {{ $status === 'finished' ? 'Sinkronisasi Selesai' : 'Sinkronisasi Dibatalkan' }}
                    </h3>
                    <p class="text-sm text-gray-500">Total {{ $total }} job, {{ $failed }} gagal</p>
                </div>
            </div>
        </div>
    @endif
</div>
