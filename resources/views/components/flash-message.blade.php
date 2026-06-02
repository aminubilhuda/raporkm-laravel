@props([])

@if(session('status'))
<div id="flash-status" class="hidden fixed top-4 right-4 z-50 px-6 py-3 bg-teal-primary shadow-teal-glow rounded-pill text-white font-medium animate-bounce-in">
    <span class="flex items-center gap-2">
        <x-heroicon-o-check-circle class="w-5 h-5" />
        {{ session('status') }}
    </span>
</div>
<script>(function(){var f=document.getElementById('flash-status');if(f)f.classList.remove('hidden'),f.classList.add('animate-bounce-in'),setTimeout(function(){f.classList.add('hidden')},5000);})();</script>
@endif

@if(session('error'))
<div id="flash-error" class="hidden fixed top-4 right-4 z-50 px-6 py-3 bg-coral shadow-coral-glow rounded-pill text-white font-medium animate-bounce-in">
    <span class="flex items-center gap-2">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
        {{ session('error') }}
    </span>
</div>
<script>(function(){var f=document.getElementById('flash-error');if(f)f.classList.remove('hidden'),f.classList.add('animate-bounce-in'),setTimeout(function(){f.classList.add('hidden')},5000);})();</script>
@endif