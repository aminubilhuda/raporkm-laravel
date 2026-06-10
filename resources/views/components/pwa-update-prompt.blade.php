@props([])

<div id="pwa-update-toast"
     class="fixed bottom-4 right-4 z-[100] hidden max-w-sm w-full bg-white rounded-lg shadow-lg border border-teal-primary/20 p-4"
     role="alert"
     aria-live="polite">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-teal-primary" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 11.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900">Pembaruan Tersedia</p>
            <p class="mt-1 text-sm text-gray-500">Versi baru aplikasi sudah tersedia.</p>
        </div>
        <button id="pwa-update-accept"
                class="flex-shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-teal-primary rounded-md hover:bg-teal-dark transition-colors">
            Muat Ulang
        </button>
        <button id="pwa-update-dismiss"
                class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function() {
    const toast = document.getElementById('pwa-update-toast');
    const acceptBtn = document.getElementById('pwa-update-accept');
    const dismissBtn = document.getElementById('pwa-update-dismiss');

    if (!toast || !acceptBtn || !dismissBtn) return;

    acceptBtn.addEventListener('click', function() {
        window.pwaAcceptUpdate();
    });

    dismissBtn.addEventListener('click', function() {
        toast.classList.add('hidden');
    });

    // Listen for SW update message
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'UPDATE_AVAILABLE') {
                toast.classList.remove('hidden');
            }
        });
    }
})();
</script>
