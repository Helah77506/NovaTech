// ===============================
// DELETE REVIEW
// ===============================
function deleteReview(reviewId) {
    if (!confirm(`Are you sure you want to delete review #${reviewId}? This cannot be undone.`)) return;

    const row = document.getElementById('review-row-' + reviewId);
    const deleteBtn = row ? row.querySelector('button.danger') : null;
    if (deleteBtn) deleteBtn.disabled = true;

    fetch('Admin_reviews.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', review_id: reviewId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {

            // Animate row out
            if (row) {
                row.style.transition = 'opacity 0.3s, transform 0.3s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => row.remove(), 300);
            }

            // Update total count
            const statTotal = document.getElementById('stat-total');
            if (statTotal) {
                const current = parseInt(statTotal.textContent) || 0;
                statTotal.textContent = Math.max(0, current - 1);
            }

            showToast(data.message, 'success');

        } else {
            showToast(data.message || 'Failed to delete.', 'error');
            if (deleteBtn) deleteBtn.disabled = false;
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Network error. Try again.', 'error');
        if (deleteBtn) deleteBtn.disabled = false;
    });
}

// ===============================
// VIEW FULL COMMENT
// ===============================
function showComment(reviewId) {
    const hidden = document.getElementById('comment-' + reviewId);
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

// Inject animation
const style = document.createElement('style');
style.textContent = `
    @keyframes toastIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);