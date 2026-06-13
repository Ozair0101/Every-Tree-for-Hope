{{--
    Reusable online-donation button. Opens the Aseel campaign widget (same target
    as the navbar "Donate Now" button), but lets each placement use its own label
    that conveys the donate concept without repeating "Donate Now".

    Params (all optional):
      $label — button text. Default: messages.donate_cta_support
      $class — anchor classes. Default: solid primary pill.
      $icon  — leading material symbol. Default: volunteer_activism
      $arrow — show trailing arrow. Default: true
--}}
@php
    $donateLabel = $label ?? __('messages.donate_cta_support');
    $donateClass = $class ?? 'group inline-flex items-center gap-2 px-7 py-4 bg-primary text-white font-extrabold text-sm tracking-wide uppercase rounded-full shadow-lg shadow-primary/20 hover:bg-primary/90 hover:-translate-y-0.5 transition-all';
    $donateIcon = $icon ?? 'volunteer_activism';
    $donateArrow = $arrow ?? true;
@endphp
<a href="#?campaign=camp_01KT1CQZWEEX5SMARBECPAAA3T" class="{{ $donateClass }}">
    <span class="material-symbols-outlined text-base">{{ $donateIcon }}</span>
    {{ $donateLabel }}
    @if ($donateArrow)
        <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
    @endif
</a>
