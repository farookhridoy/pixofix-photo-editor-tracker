window.Echo.channel(`order`)
    .listen('FileLocked', (e) => {
        const card = document.querySelector(`#file-${e.id}`);
        if (card) {
            card.classList.add('opacity-50');
            const checkbox = card.querySelector('.claim-checkbox');
            if (checkbox) {
                checkbox.disabled = true;
            }
        }
    });
