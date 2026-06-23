@props([
    'type' => 'indigo',
    'delay' => 'delay-2',
    'title' => '',
    'valueId' => null,
    'valueText' => null,
    'unit' => null,
    'icon' => '',
    'bgIcon' => ''
])

<div class="glass card-dynamic-{{ $type }} hover-lift hover-lift-{{ $type }} p-6 rounded-[2rem] relative overflow-hidden animate-card {{ $delay }}">
    <div class="flex justify-between items-start">
        <div class="space-y-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-{{ $type }}-600 dark:text-{{ $type }}-400">{{ $title }}</span>
            <div class="flex items-end gap-2 mt-1">
                @if($valueId)
                    <span class="text-4xl font-black text-{{ $type }}-600 dark:text-{{ $type }}-400" id="{{ $valueId }}">0</span>
                @else
                    <div class="flex flex-col mt-1 text-slate-800 dark:text-slate-200 font-bold">
                        {!! $valueText !!}
                    </div>
                @endif
                
                @if($unit)
                    <span class="text-{{ $type }}-500 dark:text-{{ $type }}-400 text-[10px] font-bold mb-1">{{ $unit }}</span>
                @endif
            </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-{{ $type }}-500/10 dark:bg-{{ $type }}-500/20 text-{{ $type }}-600 dark:text-{{ $type }}-300">
            <i class="fa-solid fa-{{ $icon }} text-xl"></i>
        </div>
    </div>
    <div class="icon-rotate absolute -right-3 -bottom-5 text-{{ $type }}-500/15 dark:text-{{ $type }}-500/10 text-7xl rotate-12 transition-transform duration-300">
        <i class="fa-solid fa-{{ $bgIcon }}"></i>
    </div>
</div>