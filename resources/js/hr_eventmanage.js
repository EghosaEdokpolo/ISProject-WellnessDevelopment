document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast');

    function showToast(message, type) {
        if (!toast) return;
        toast.textContent = message;
        toast.className = 'ev-toast ' + (type === 'ok' ? 'ev-toast-ok' : 'ev-toast-err');
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    // 1. Parse event data safely from the dropdown's data-events attribute
    const eventSelect = document.getElementById('eventcategory');
    const eventData = eventSelect ? JSON.parse(eventSelect.dataset.events || '{}') : {};

    // 2. QR Code Generation & Download
    const qrPlaceholder = document.getElementById('qrPlaceholder');
    const qrHolder = document.getElementById('qrHolder');
    const qrEventName = document.getElementById('qrEventName');
    const downloadBtn = document.getElementById('downloadQrBtn');
    let currentSvg = null;
    let currentQrId = null;
    let currentQrName = null;

    document.querySelectorAll('.qr-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.qrId;
            const name = btn.dataset.qrName;

            qrPlaceholder.style.display = 'none';
            qrHolder.style.display = 'block';
            qrHolder.innerHTML = '<span style="color:#94a3b8; font-size:13px;">Generating…</span>';
            qrEventName.textContent = name;
            downloadBtn.disabled = true;

            try {
                const res = await fetch(`/events/${id}/qr`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('Server returned ' + res.status);
                const svgText = await res.text();
                qrHolder.innerHTML = svgText;
                currentSvg = svgText;
                currentQrId = id;
                currentQrName = name;
                downloadBtn.disabled = false;
            } catch (err) {
                qrHolder.innerHTML = `<span style="color:#b91c1c; font-size:13px;">Failed: ${err.message}</span>`;
            }
        });
    });

    if (downloadBtn) {
        downloadBtn.addEventListener('click', () => {
            if (!currentSvg || !currentQrId) return;

            // Remove characters Windows/Mac don't allow in filenames (like / \ : * ? " < > |)
            const safeName = currentQrName.replace(/[\/\\:*?"<>|]/g, '').trim();

            const blob = new Blob([currentSvg], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${safeName} QR code.svg`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    // 3. Event Preview Dropdown Change Listener
    if (eventSelect) {
        eventSelect.addEventListener('change', (e) => {
            const id = e.target.value;
            const preview = document.getElementById('eventPreview');
            if (!id || !eventData[id]) {
                preview.innerHTML = '<span style="color:#94a3b8;">Select an event above to preview its details.</span>';
                return;
            }
            const d = eventData[id];
            preview.innerHTML = `
                <strong>${d.name}</strong><br>
                <span style="color:#6b7280;"></span> ${d.date}<br>
                <span style="color:#6b7280;"></span> ${d.location}<br>
                <span style="color:#6b7280;">🏷</span> ${d.category} &nbsp;·&nbsp; <span style="color:#6b7280;">👥</span> Capacity: ${d.capacity}
                ${d.desc ? `<div style="margin-top:10px; padding-top:10px; border-top:1px solid #e5e7eb; color:#6b7280; font-size:12px;">${d.desc}</div>` : ''}
            `;
        });
    }

    // 4. Open Gmail Compose Window with Prefilled Event Alert
    const sendBtn = document.getElementById('sendCommBtn');
if (sendBtn) {
    sendBtn.addEventListener('click', () => {
        const eventId = eventSelect ? eventSelect.value : '';
        const userEmail = sendBtn.dataset.userEmail || '';

        if (!eventId || !eventData[eventId]) {
            showToast('Pick an event first.', 'err');
            return;
        }

        if (!userEmail.toLowerCase().endsWith('@strathmore.edu')) {
            showToast('Please log in with your @strathmore.edu account to send invites.', 'err');
            return;
        }

        const d = eventData[eventId];
        const subject = 'Wellness Event Alert: ' + d.name;
        const bodyText =
            'Hello Team,\n\n' +
            'You are invited to attend our upcoming Strathmore University wellness session!\n\n' +
            'Event: ' + d.name + '\n' +
            'Date & Time: ' + d.date + '\n' +
            'Location: ' + d.location + '\n' +
            (d.comlink ? 'Communication Link: ' + d.comlink + '\n' : '') +
            'Wellness Category: ' + d.category + '\n' +
            'Capacity: ' + d.capacity + ' slots\n\n' +
            'Details:\n' +
            (d.desc || 'Join us for an engaging wellness session.') + '\n\n' +
            'Best regards,\n' +
            'HR Wellness Coordinator\n' +
            'Strathmore University';

        const params = new URLSearchParams({
            view: 'cm',
            fs: '1',
            authuser: userEmail,
            hd: 'strathmore.edu',
            su: subject,
            body: bodyText
        });

        window.open('https://google.com?' + params.toString(), '_blank', 'noopener,noreferrer');
        showToast('Opened Gmail with your prefilled event draft!', 'ok');
    });
}


});
