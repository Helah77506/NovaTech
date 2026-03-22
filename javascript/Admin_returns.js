// ===============================
// PROCESS RETURN (APPROVE / REJECT)
// ===============================
function processReturn(returnId, action) {
    const label = action === 'approve' ? 'approve' : 'reject';
    if (!confirm(`Are you sure you want to ${label} return #${returnId}?`)) return;

    const actionsCell = document.getElementById('actions-' + returnId);
    const buttons = actionsCell.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);

    fetch('Admin_returns.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: action, return_id: returnId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {

            // Update badge
            const badge = document.getElementById('badge-' + returnId);
            if (action === 'approve') {
                badge.textContent = 'Approved';
                badge.className = 'badge completed';
            } else {
                badge.textContent = 'Rejected';
                badge.className = 'badge badge-rejected';
            }

            // Replace buttons with processed date
            actionsCell.innerHTML = '<small style="color:#6b7280;">Just now</small>';

            showToast(data.message, action === 'approve' ? 'success' : 'info');
            updateStatCards(action);

        } else {
            showToast(data.message || 'Something went wrong.', 'error');
            buttons.forEach(btn => btn.disabled = false);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Network error. Try again.', 'error');
        buttons.forEach(btn => btn.disabled = false);
    });
}

// ===============================
// UPDATE STAT CARDS
// ===============================
function updateStatCards(action) {
    const cards = document.querySelectorAll('.analytics-grid .card p');
    if (cards.length >= 4) {
        const pending = parseInt(cards[1].textContent) || 0;
        cards[1].textContent = Math.max(0, pending - 1);

        if (action === 'approve') {
            cards[2].textContent = (parseInt(cards[2].textContent) || 0) + 1;
        } else {
            cards[3].textContent = (parseInt(cards[3].textContent) || 0) + 1;
        }
    }
}

// ===============================
// VIEW FULL REASON
// ===============================
function showReason(returnId) {
    const hidden = document.getElementById('reason-' + returnId);
    if (hidden) alert(hidden.value);
}

// ===============================
// TOAST NOTIFICATION
// ===============================
function showToast(message, type) {
    const old = document.querySelector('.admin-toast');
    if (old) old.remove();

    const toast = document.createElement('div');
    toast.className = 'admin-toast';
    toast.textContent = message;

    const colors = { success: '#10b981', error: '#dc2626', info: '#3b82f6' };

    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '24px',
        right: '24px',
        background: colors[type] || '#333',
        color: '#fff',
        padding: '12px 20px',
        borderRadius: '8px',
        fontSize: '14px',
        fontWeight: '600',
        boxShadow: '0 4px 14px rgba(0,0,0,0.15)',
        zIndex: '9999',
        animation: 'toastIn 0.3s ease'
    });

    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Inject styles
const style = document.createElement('style');
style.textContent = `
    @keyframes toastIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .badge-rejected {
        background: #dc2626;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        color: white;
    }
`;
document.head.appendChild(style);