@php
$unitPriceFormatted = number_format($unitPrice ?? 0, 2);
$subtotalFormatted = number_format($subtotal ?? 0, 2);
@endphp

<article class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900/90 to-slate-800/90 p-6 shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:border-emerald-400/40 hover:shadow-emerald-400/30 hover:transform hover:scale-[1.01]">

    <div class="mt-6 overflow-hidden rounded-xl border border-white/10 bg-slate-950/50 backdrop-blur-sm">
        <table class="min-w-full border-separate border-spacing-0 text-sm text-slate-100" style="width: 100%;">
            <thead>
                <tr class="bg-gradient-to-r from-slate-900/90 to-slate-800/90 text-xs uppercase tracking-wider text-slate-300">
                    <th scope="col" class="px-6 py-4 text-left font-medium" style="width: 25%;">Servicio</th>
                    <th scope="col" class="px-6 py-4 text-left font-medium" style="width: 25%;">Cantidad</th>
                    <th scope="col" class="px-6 py-4 text-left font-medium" style="width: 25%;">Precio unitario</th>
                    <th scope="col" class="px-6 py-4 text-left font-medium" style="width: 25%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr class="divide-x divide-white/5 bg-slate-900/40 backdrop-blur transition-colors duration-200 hover:bg-slate-900/60">
                    <td class="px-6 py-5 font-medium text-slate-200">{{ $serviceName }}</td>
                    <td class="px-6 py-5 font-semibold text-white">{{ $quantity }}</td>
                    <td class="px-6 py-5 font-medium text-slate-200">{{ $unitPriceFormatted }} PEN</td>
                    <td class="px-6 py-5 text-base font-semibold text-emerald-300">{{ $subtotalFormatted }} PEN</td>
                </tr>
            </tbody>
        </table>
    </div>
</article>