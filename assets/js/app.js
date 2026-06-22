document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.toast').forEach((toast) => {
    bootstrap.Toast.getOrCreateInstance(toast, { delay: 4500 }).show();
  });

  document.querySelectorAll('.needs-validation').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  document.querySelectorAll('.favorite-form').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = form.querySelector('button');
      const label = button.querySelector('span');
      button.disabled = true;
      button.classList.add('is-loading');

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form),
        });

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
          window.location.href = 'login.php';
          return;
        }

        const payload = await response.json();
        if (payload.ok) {
          button.classList.toggle('saved', Boolean(payload.saved));
          label.textContent = payload.saved ? 'Saved' : 'Save';
          showToast(payload.message, 'success');
        } else {
          showToast(payload.message || 'Unable to update favorite.', 'danger');
        }
      } catch (error) {
        showToast('Network error. Please try again.', 'danger');
      } finally {
        button.disabled = false;
        button.classList.remove('is-loading');
      }
    });
  });

  const budgetForm = document.getElementById('budgetForm');
  if (budgetForm) {
    const totalEl = document.getElementById('budgetTotal');
    const fields = ['days', 'hotel_cost', 'food_cost', 'transport_cost', 'misc_cost'];
    const calculate = () => {
      const values = Object.fromEntries(fields.map((id) => [id, Number(document.getElementById(id)?.value || 0)]));
      const days = Math.max(1, values.days || 1);
      const total = days * (values.hotel_cost + values.food_cost + values.transport_cost + values.misc_cost);
      totalEl.textContent = new Intl.NumberFormat('en-PK', {
        style: 'currency',
        currency: 'PKR',
        maximumFractionDigits: 0,
      }).format(total);
    };

    fields.forEach((id) => document.getElementById(id)?.addEventListener('input', calculate));
    calculate();
  }

  const destinationMap = document.getElementById('destinationMap');
  if (destinationMap && window.L) {
    const lat = Number(destinationMap.dataset.lat);
    const lng = Number(destinationMap.dataset.lng);
    const title = destinationMap.dataset.title || 'Destination';
    const location = destinationMap.dataset.location || '';

    if (Number.isFinite(lat) && Number.isFinite(lng)) {
      const map = L.map(destinationMap, {
        scrollWheelZoom: false,
      }).setView([lat, lng], 10);

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      }).addTo(map);

      L.marker([lat, lng]).addTo(map)
        .bindPopup(`<strong>${escapeHtml(title)}</strong><br>${escapeHtml(location)}`)
        .openPopup();

      setTimeout(() => map.invalidateSize(), 250);
    }
  }
});

function showToast(message, type = 'primary') {
  const container = document.querySelector('.toast-container') || createToastContainer();
  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;
  toast.querySelector('.toast-body').textContent = message;
  container.appendChild(toast);
  bootstrap.Toast.getOrCreateInstance(toast, { delay: 3500 }).show();
  toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

function createToastContainer() {
  const container = document.createElement('div');
  container.className = 'toast-container position-fixed top-0 end-0 p-3';
  document.body.appendChild(container);
  return container;
}

function escapeHtml(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}
