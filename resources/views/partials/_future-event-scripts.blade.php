        <script>
            (function () {
                const cards = document.querySelectorAll('.future-event-card');

                // ── Agenda accordion ──
                const agendaItems = document.querySelectorAll('.future-agenda-item');
                function closeAgenda(item) {
                    const body = item.querySelector('.future-agenda-body');
                    const chev = item.querySelector('.agenda-chevron');
                    if (body) body.classList.add('hidden');
                    if (chev) chev.classList.remove('rotate-180');
                }
                function openAgenda(item) {
                    const body = item.querySelector('.future-agenda-body');
                    const chev = item.querySelector('.agenda-chevron');
                    if (body) body.classList.remove('hidden');
                    if (chev) chev.classList.add('rotate-180');
                }
                agendaItems.forEach(function (item) {
                    const head = item.querySelector('.future-agenda-head');
                    if (!head) return;
                    head.addEventListener('click', function () {
                        const body = item.querySelector('.future-agenda-body');
                        const isOpen = body && !body.classList.contains('hidden');
                        // accordion: close all, then open this one if it was closed
                        agendaItems.forEach(closeAgenda);
                        if (!isOpen) {
                            openAgenda(item);
                            setTimeout(() => item.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
                        }
                    });
                });

                cards.forEach(function (card) {
                    // ── Share / copy deep link ──
                    const shareBtn = card.querySelector('.future-share-btn');
                    if (shareBtn) {
                        shareBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const url = shareBtn.getAttribute('data-share-url');
                            const labelDefault = shareBtn.querySelector('.share-label-default');
                            const labelCopied = shareBtn.querySelector('.share-label-copied');
                            const icon = shareBtn.querySelector('.share-icon');

                            const showCopied = function () {
                                if (labelDefault) labelDefault.classList.add('hidden');
                                if (labelCopied) labelCopied.classList.remove('hidden');
                                if (icon) icon.textContent = 'check';
                                shareBtn.classList.add('border-vibrant-lime', 'bg-vibrant-lime/10');
                                setTimeout(function () {
                                    if (labelDefault) labelDefault.classList.remove('hidden');
                                    if (labelCopied) labelCopied.classList.add('hidden');
                                    if (icon) icon.textContent = 'link';
                                    shareBtn.classList.remove('border-vibrant-lime', 'bg-vibrant-lime/10');
                                }, 2200);
                            };

                            if (navigator.clipboard && window.isSecureContext) {
                                navigator.clipboard.writeText(url).then(showCopied).catch(function () {
                                    window.prompt(@json(__('messages.future_share_btn')), url);
                                });
                            } else {
                                const ta = document.createElement('textarea');
                                ta.value = url;
                                ta.style.position = 'fixed';
                                ta.style.opacity = '0';
                                document.body.appendChild(ta);
                                ta.select();
                                try { document.execCommand('copy'); showCopied(); }
                                catch (e2) { window.prompt(@json(__('messages.future_share_btn')), url); }
                                document.body.removeChild(ta);
                            }
                        });
                    }

                    const toggleBtn = card.querySelector('.future-register-toggle');
                    const panel = card.querySelector('.future-register-panel');
                    const form = card.querySelector('.future-register-form');
                    const successBox = card.querySelector('.future-register-success');
                    const errorsBox = card.querySelector('.future-register-errors');
                    const errorsList = card.querySelector('.future-register-errors-list');

                    if (!toggleBtn || !panel || !form) return;

                    toggleBtn.addEventListener('click', function () {
                        const willOpen = panel.classList.contains('hidden');
                        panel.classList.toggle('hidden', !willOpen);
                        toggleBtn.querySelector('.toggle-label-open').classList.toggle('hidden', willOpen);
                        toggleBtn.querySelector('.toggle-label-close').classList.toggle('hidden', !willOpen);
                        toggleBtn.querySelector('.toggle-chevron').classList.toggle('rotate-180', willOpen);
                        if (willOpen) {
                            setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
                            const firstInput = form.querySelector('input[name="name"]');
                            if (firstInput) setTimeout(() => firstInput.focus(), 350);
                        }
                    });

                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const submitBtn = form.querySelector('.future-register-submit');
                        const submitLabel = submitBtn.querySelector('.submit-label');
                        const submitIcon = submitBtn.querySelector('.submit-icon');
                        const submitSpinner = submitBtn.querySelector('.submit-spinner');

                        errorsBox.classList.add('hidden');
                        errorsList.innerHTML = '';

                        submitBtn.disabled = true;
                        submitIcon.classList.add('hidden');
                        submitSpinner.classList.remove('hidden');
                        const originalLabel = submitLabel.textContent;
                        submitLabel.textContent = @json(__('messages.future_register_sending'));

                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': formData.get('_token'),
                            },
                            credentials: 'same-origin',
                        })
                        .then(function (response) {
                            if (response.ok) {
                                return response.json().then(function () {
                                    form.classList.add('hidden');
                                    if (successBox) successBox.classList.remove('hidden');
                                    toggleBtn.classList.add('hidden');
                                });
                            }
                            return response.json().then(function (data) {
                                const errors = (data && data.errors) ? data.errors : { _generic: [data.message || @json(__('messages.future_register_error_generic'))] };
                                Object.keys(errors).forEach(function (key) {
                                    (errors[key] || []).forEach(function (msg) {
                                        const li = document.createElement('li');
                                        li.textContent = msg;
                                        errorsList.appendChild(li);
                                    });
                                });
                                errorsBox.classList.remove('hidden');
                                throw new Error('validation');
                            });
                        })
                        .catch(function (err) {
                            if (err && err.message === 'validation') return;
                            const li = document.createElement('li');
                            li.textContent = @json(__('messages.future_register_error_network'));
                            errorsList.innerHTML = '';
                            errorsList.appendChild(li);
                            errorsBox.classList.remove('hidden');
                        })
                        .finally(function () {
                            submitBtn.disabled = false;
                            submitIcon.classList.remove('hidden');
                            submitSpinner.classList.add('hidden');
                            submitLabel.textContent = originalLabel;
                        });
                    });
                });

                // ── Deep link: open via #event-ID → scroll, expand (if agenda), highlight ──
                function focusHashedEvent() {
                    const hash = window.location.hash;
                    if (!hash || hash.indexOf('#event-') !== 0) return;
                    const target = document.getElementById(hash.slice(1));
                    if (!target || !target.classList.contains('future-event-card')) return;

                    // If it's an agenda row, expand it
                    if (target.classList.contains('future-agenda-item')) {
                        agendaItems.forEach(closeAgenda);
                        openAgenda(target);
                    }

                    setTimeout(function () {
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        target.classList.add('ring-4', 'ring-vibrant-lime', 'ring-offset-4', 'ring-offset-[#fafaf5]');
                        setTimeout(function () {
                            target.classList.remove('ring-4', 'ring-vibrant-lime', 'ring-offset-4', 'ring-offset-[#fafaf5]');
                        }, 2800);
                    }, 250);
                }

                focusHashedEvent();
                window.addEventListener('hashchange', focusHashedEvent);
            })();
        </script>
