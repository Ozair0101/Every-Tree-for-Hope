<div class="rounded-[2rem] shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead class="bg-deep-green text-white">
                <tr>
                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                        Donator</th>
                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                        Impact</th>
                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                        Support</th>
                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                        Date</th>
                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                        Location</th>
                    <th class="text-right py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                        Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($verifiedDonators as $donator)
                    <tr class="donor-row transition-colors cursor-pointer group hover:bg-stone-50">
                        <td class="py-4 px-4 md:py-6 md:px-8">
                            <div class="flex items-center gap-3 md:gap-4">
                                <div
                                    class="w-10 h-10 md:w-12 md:h-12 rounded-full overflow-hidden border-2 border-stone-100 group-hover:border-gold-accent transition-colors flex-shrink-0">
                                    @if ($donator->profile_image)
                                        <img alt="{{ $donator->full_name }} Profile" class="w-full h-full object-cover"
                                            src="{{ asset('storage/' . $donator->profile_image) }}" />
                                    @else
                                        <div class="w-full h-full bg-deep-green/10 flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-deep-green text-sm md:text-base">person</span>
                                        </div>
                                    @endif
                                </div>
                                <span
                                    class="text-deep-green font-serif text-lg md:text-xl group-hover:text-gold-accent transition-colors truncate">
                                    {{ $donator->full_name }}
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4 md:py-6 md:px-8">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sage-green text-sm">park</span>
                                <span
                                    class="text-charcoal font-medium text-sm md:text-base">{{ $donator->trees_sponsored }}
                                    Trees</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 md:py-6 md:px-8">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gold-accent text-sm">payments</span>
                                <span
                                    class="text-charcoal font-medium text-sm md:text-base">${{ number_format($donator->financial_support ?? 0, 0) }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 md:py-6 md:px-8">
                            <span
                                class="text-charcoal/60 text-sm md:text-base">{{ $donator->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="py-4 px-4 md:py-6 md:px-8">
                            <span
                                class="text-charcoal/60 text-sm md:text-base truncate block max-w-[120px] md:max-w-none">
                                {{ $donator->location ?? 'International' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 md:py-6 md:px-8 text-right">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 md:gap-1.5 md:px-3 md:py-1 bg-deep-green/5 rounded-full">
                                <span
                                    class="material-symbols-outlined text-[12px] md:text-[14px] text-deep-green">verified</span>
                                <span
                                    class="text-[8px] md:text-[9px] font-bold text-deep-green uppercase tracking-widest hidden sm:inline">Verified</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 md:px-8 text-center text-charcoal/60 text-sm md:text-base">
                            No verified donators yet. Be the first to support our mission!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination - Outside Table Container -->
@if ($verifiedDonators->hasPages())
    <div class="mt-8 flex justify-center" id="pagination-container">
        {{ $verifiedDonators->links() }}
    </div>
@endif
