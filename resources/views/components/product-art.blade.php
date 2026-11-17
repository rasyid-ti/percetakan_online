@props(['tall' => false])

<div @class(['relative overflow-hidden bg-brand-100', 'h-44' => ! $tall, 'h-96' => $tall])>
    <div @class(['absolute -rotate-6 rounded-xl bg-white shadow-lg', 'left-7 top-8 h-24 w-32' => ! $tall, 'left-[12%] top-[16%] h-44 w-64' => $tall])></div>
    <div @class(['absolute rotate-6 rounded-xl bg-ink-900 shadow-xl', 'left-24 top-11 h-24 w-32 p-3.5' => ! $tall, 'left-[28%] top-[32%] h-44 w-64 p-6' => $tall])>
        <div class="flex flex-col gap-1.5">
            <span class="h-2 w-11 rounded-full bg-brand-500"></span>
            <span class="h-2 w-8 rounded-full bg-magenta-500"></span>
            <span class="h-2 w-5 rounded-full bg-sun-400"></span>
        </div>
    </div>
</div>