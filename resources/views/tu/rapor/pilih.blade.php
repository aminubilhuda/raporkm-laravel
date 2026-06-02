@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-document-text class="w-7 h-7" />
            Cetak Rapor
        </h1>
        <p class="mt-1 text-sm text-gray-500">Pilih siswa, tahun pelajaran, dan semester untuk mencetak rapor.</p>
    </div>

    <form method="GET" id="raporForm" class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary space-y-4">
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Siswa</label>
                <select name="siswa_id" required class="w-full border-gray-300 rounded-pill px-4 py-2 text-sm focus:ring-teal-primary focus:border-teal-primary">
                    <option value="">— Pilih Siswa —</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_siswa }} ({{ $s->nisn }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Pelajaran</label>
                <select name="tahun" required class="w-full border-gray-300 rounded-pill px-4 py-2 text-sm focus:ring-teal-primary focus:border-teal-primary">
                    <option value="">— Pilih —</option>
                    @foreach(\App\Models\TahunPelajaran::all() as $tp)
                        <option value="{{ $tp->id }}">{{ $tp->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Semester</label>
                <select name="semester" required class="w-full border-gray-300 rounded-pill px-4 py-2 text-sm focus:ring-teal-primary focus:border-teal-primary">
                    <option value="">— Pilih —</option>
                    @foreach(\App\Models\Semester::all() as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 pt-2">
            <button type="button" onclick="submitRapor('semester')" class="bg-teal-primary hover:bg-teal-primary-dark text-white font-bold py-3 px-4 rounded-pill flex items-center justify-center gap-2 transition-all">
                <x-heroicon-o-document class="w-5 h-5" />
                Cetak Rapor Semester (PDF)
            </button>
            <button type="button" onclick="submitRapor('mid')" class="bg-coral hover:bg-coral-dark text-white font-bold py-3 px-4 rounded-pill flex items-center justify-center gap-2 transition-all">
                <x-heroicon-o-document class="w-5 h-5" />
                Cetak Rapor Mid-Semester (PDF)
            </button>
        </div>
    </form>
</div>

<script>
function submitRapor(type) {
    const form = document.getElementById('raporForm');
    const siswa = form.querySelector('[name=siswa_id]').value;
    const tahun = form.querySelector('[name=tahun]').value;
    const semester = form.querySelector('[name=semester]').value;
    if (!siswa || !tahun || !semester) {
        alert('Pilih siswa, tahun, dan semester terlebih dahulu.');
        return;
    }
    const base = type === 'semester'
        ? "{{ url('/tu/rapor/semester') }}/" + siswa + "/" + tahun + "/" + semester
        : "{{ url('/tu/rapor/mid') }}/" + siswa + "/" + tahun + "/" + semester;
    window.open(base, '_blank');
}
</script>
@endsection
