@props(['route'])

<div class="px-4 mt-3 mb-2">
    <form method="POST" action="{{ route($route) }}" id="semester-switcher-form">
        @csrf
        <label class="block text-[10px] font-bold uppercase tracking-wider text-teal-light/60 mb-1">Semester Aktif</label>
        <div class="relative">
            <select name="semester_select" onchange="window.__submitSemester(this)" class="w-full text-xs rounded-lg px-2 py-1.5 cursor-pointer focus:border-gold focus:ring-1 focus:ring-gold appearance-none pr-7 bg-cream"
                style="background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.2);">
                @forelse($semesterOptions as $opt)
                    <option value="{{ $opt['tahun_id'] }}-{{ $opt['semester_id'] }}"
                        {{ $activeTpId == $opt['tahun_id'] && $activeSemesterId == $opt['semester_id'] ? 'selected' : '' }}
                        style="background: #fff; color: #1a1a1a;">
                        {{ $opt['label'] }}
                    </option>
                @empty
                    <option value="" disabled style="background: #fff; color: #999;">Tidak ada data</option>
                @endforelse
            </select>
            <div class="pointer-events-none absolute inset-y-0 flex items-center" style="right: 12px;">
                <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-white/70" />
            </div>
        </div>
        <input type="hidden" name="tahun_pelajaran_id" id="tp-hidden" class="bg-cream rounded-field border-teal-primary/20">
        <input type="hidden" name="semester_id" id="sem-hidden" class="bg-cream rounded-field border-teal-primary/20">
    </form>
</div>

<script>
(function() {
    function submitSemester(select) {
        var parts = select.value.split('-');
        document.getElementById('tp-hidden').value = parts[0];
        document.getElementById('sem-hidden').value = parts[1];
        select.form.submit();
    }
    window.__submitSemester = submitSemester;

    var sel = document.querySelector('[name="semester_select"]');
    if (sel && sel.value && sel.value.indexOf('-') !== -1) {
        var parts = sel.value.split('-');
        var tpH = document.getElementById('tp-hidden');
        var semH = document.getElementById('sem-hidden');
        if (tpH) tpH.value = parts[0];
        if (semH) semH.value = parts[1];
    }
})();
</script>
