{{--
    Shared AJAX "like" behaviour. Works for any element with [data-like-btn]
    on the page (the wall cards and the watch-page pill both use it).
    The button must contain a .like-icon and an element with [data-like-count].
--}}
<style>
    .like-btn { cursor: pointer; }
    .like-btn .like-icon { transition: transform .2s ease; }
    .like-btn.is-liked .like-icon { font-variation-settings: 'FILL' 1; color: #f43f5e; }
    .like-icon.bump { animation: likebump .35s ease; }
    @keyframes likebump { 0% { transform: scale(1); } 40% { transform: scale(1.4); } 100% { transform: scale(1); } }

    /* Pill variant on the watch page fills solid when liked. */
    .like-pill.is-liked { background-color: #f43f5e !important; color: #fff !important; }
    .like-pill.is-liked .like-icon { color: #fff; }
</style>
<script>
    (function () {
        const token = '{{ csrf_token() }}';

        function wire(btn) {
            if (btn.dataset.wired) return;
            btn.dataset.wired = '1';

            btn.addEventListener('click', async function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (btn.dataset.busy) return;
                btn.dataset.busy = '1';

                try {
                    const res = await fetch(btn.dataset.url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    if (!res.ok) throw new Error('request failed');
                    const data = await res.json();

                    const countEl = btn.querySelector('[data-like-count]');
                    if (countEl) countEl.textContent = data.count;

                    btn.classList.toggle('is-liked', !!data.liked);
                    btn.setAttribute('aria-pressed', data.liked ? 'true' : 'false');

                    const icon = btn.querySelector('.like-icon');
                    if (icon) { icon.classList.remove('bump'); void icon.offsetWidth; icon.classList.add('bump'); }
                } catch (err) {
                    /* fail silently — the page is still usable */
                }

                delete btn.dataset.busy;
            });
        }

        document.querySelectorAll('[data-like-btn]').forEach(wire);
    })();
</script>
