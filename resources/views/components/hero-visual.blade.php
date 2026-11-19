<div class="relative min-h-[480px] flex-[1_1_460px] overflow-hidden rounded-[36px] bg-[#0B2A5B]" data-reveal="right" aria-hidden="true">

    {{-- garis warna logo, bergeser pelan --}}
    <div class="tk-stripe absolute -left-10 top-16 h-[30px] w-[260px] rounded-full bg-[#1A8CFF]"></div>
    <div class="tk-stripe absolute -left-[70px] top-[112px] h-[30px] w-[300px] rounded-full bg-[#EC0E80]" style="animation-delay:-2s"></div>
    <div class="tk-stripe absolute -left-[30px] top-40 h-[30px] w-[220px] rounded-full bg-[#FFD500]" style="animation-delay:-4s"></div>

    {{-- kartu nama contoh, melayang --}}
    <div class="tk-float absolute right-9 top-12 z-10 flex h-[176px] w-[300px] max-w-[70%] rotate-6 flex-col justify-between rounded-2xl bg-white p-[22px] shadow-[0_24px_48px_rgba(0,0,0,0.28)]">
        <div class="flex gap-1.5">
            <span class="h-2 w-7 rounded bg-[#1A8CFF]"></span>
            <span class="h-2 w-5 rounded bg-[#EC0E80]"></span>
            <span class="h-2 w-3 rounded bg-[#FFD500]"></span>
        </div>
        <div>
            <div class="text-[19px] font-extrabold text-[#0B2A5B]">TintaKita Online</div>
            <div class="mt-0.5 text-xs text-[#3C5379]">Cetak Mudah, Kapan Saja</div>
        </div>
    </div>

    {{-- kartu kedua di belakang --}}
    <div class="tk-float-b absolute right-20 top-[104px] z-0 h-[150px] w-[240px] max-w-[60%] -rotate-[8deg] rounded-2xl bg-[#EC0E80] shadow-[0_20px_40px_rgba(0,0,0,0.25)]"></div>

    {{-- kartu status pesanan, berjalan sendiri --}}
    <div class="absolute inset-x-9 bottom-9 flex flex-col gap-4 rounded-3xl bg-white p-6 shadow-[0_24px_48px_rgba(0,0,0,0.28)]">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-xs font-semibold text-[#5A6F92]">Pesanan Anda</div>
                <div class="text-base font-extrabold text-[#0B2A5B]">TKT-20261116-0001</div>
            </div>

            {{-- 4 lencana bergantian --}}
            <div class="relative h-8 w-[150px]">
                <span class="tk-badge absolute right-0 top-0 inline-flex items-center gap-2 rounded-full bg-[#E6EEFF] px-3.5 py-1.5 text-xs font-bold text-[#3730A3]" style="animation-delay:0s">
                    <span class="tk-spin h-3 w-3 rounded-full border-2 border-[#3730A3]/30 border-t-[#3730A3]"></span>Cek file
                </span>
                <span class="tk-badge absolute right-0 top-0 inline-flex items-center gap-2 rounded-full bg-[#FFE3F1] px-3.5 py-1.5 text-xs font-bold text-[#A8055A]" style="animation-delay:3s">
                    <span class="tk-spin h-3 w-3 rounded-full border-2 border-[#A8055A]/30 border-t-[#A8055A]"></span>Sedang dicetak
                </span>
                <span class="tk-badge absolute right-0 top-0 inline-flex items-center gap-2 rounded-full bg-[#FFF3B8] px-3.5 py-1.5 text-xs font-bold text-[#6B5600]" style="animation-delay:6s">
                    <span class="tk-spin h-3 w-3 rounded-full border-2 border-[#6B5600]/30 border-t-[#6B5600]"></span>Finishing
                </span>
                <span class="tk-badge absolute right-0 top-0 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-800" style="animation-delay:9s">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>Siap diambil
                </span>
            </div>
        </div>

        <div class="h-2.5 overflow-hidden rounded-full bg-[#E3EAF5]">
            <div class="tk-progress h-full rounded-full bg-gradient-to-r from-[#0B6BDB] to-[#1A8CFF]"></div>
        </div>

        <div class="flex justify-between text-xs font-semibold text-[#5A6F92]">
            <span class="tk-step" style="animation-delay:0s">Cek file</span>
            <span class="tk-step" style="animation-delay:3s">Cetak</span>
            <span class="tk-step" style="animation-delay:6s">Finishing</span>
            <span class="tk-step" style="animation-delay:9s">Siap</span>
        </div>
    </div>
</div>