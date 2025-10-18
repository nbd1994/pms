const state = {
  search: "",
  category_id: "",
  sort: "",
  dir: "asc",
  page: 1,
  perPage: 10,
  loading: false,
  append: false,
  deleteId: null,
  scrollY: 0,
};

const qs = (s, r = document) => r.querySelector(s);
const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));
const csrf = () => qs('meta[name="csrf-token"]').content;

function debounce(fn, ms = 300) {
  let t;
  return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}

function savePrefs() {
  localStorage.setItem('pms:prefs', JSON.stringify({
    search: state.search,
    category_id: state.category_id,
    sort: state.sort,
    dir: state.dir,
    perPage: state.perPage
  }));
}
function loadPrefs() {
  const s = localStorage.getItem('pms:prefs');
  if (!s) return;
  try {
    const p = JSON.parse(s);
    Object.assign(state, p);
  } catch {}
}

function toast(msg, type = 'info') {
  const el = qs('#toast');
  if (!el) return;
  el.textContent = msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2000);
}

function openModal(id) {
  const modal = qs(`#${id}`);
  const backdrop = qs(`.modal-backdrop[data-backdrop-for="${id}"]`);
  if (!modal || !backdrop) return;
  modal.hidden = false;
  backdrop.hidden = false;
  backdrop.classList.add('open');
  trapFocus(modal);
  setTimeout(() => modal.classList.add('open'), 0);
}
function closeModal(id) {
  const modal = qs(`#${id}`);
  const backdrop = qs(`.modal-backdrop[data-backdrop-for="${id}"]`);
  if (!modal || !backdrop) return;
  modal.classList.remove('open');
  backdrop.classList.remove('open');
  modal.hidden = true;
  backdrop.hidden = true;
  releaseFocus();
}
function trapFocus(container) {
  const focusables = qsa('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', container)
    .filter(el => !el.hasAttribute('disabled'));
  if (!focusables.length) return;
  const first = focusables[0];
  const last = focusables[focusables.length - 1];
  function onKey(e) {
    if (e.key === 'Tab') {
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    } else if (e.key === 'Escape') {
      const id = container.id;
      closeModal(id);
    }
  }
  container.__focusHandler = onKey;
  document.addEventListener('keydown', onKey);
  first.focus();
}
function releaseFocus() {
  if (document.__focusHandler) {
    document.removeEventListener('keydown', document.__focusHandler);
    document.__focusHandler = null;
  }
}

async function fetchList({ append = false } = {}) {
  if (state.loading) return;
  state.loading = true;
  const params = new URLSearchParams({
    search: state.search,
    category_id: state.category_id,
    sort: state.sort,
    dir: state.dir,
    page: state.page,
    perPage: state.perPage,
  });
  const res = await fetch(`/products/partial?${params.toString()}`, {
    headers: { 'Accept': 'text/html' }
  });
  const html = await res.text();
  const container = qs('#productList');
  if (append && container.innerHTML.trim()) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    const rows = qsa('tbody tr', tmp);
    const tbody = qs('tbody', container);
    rows.forEach(r => tbody.appendChild(r));
    const meta = qs('#listMeta', tmp);
    const curMeta = qs('#listMeta', container) || document.createElement('div');
    curMeta.replaceWith(meta);
  } else {
    container.innerHTML = html;
    window.scrollTo(0, state.scrollY || 0);
  }
  state.loading = false;
  wireRowActions();
}

function setSort(key) {
  if (state.sort === key) {
    state.dir = state.dir === 'asc' ? 'desc' : 'asc';
  } else {
    state.sort = key;
    state.dir = 'asc';
  }
  state.page = 1;
  savePrefs();
  fetchList();
}

function validateForm(form) {
  const data = Object.fromEntries(new FormData(form));
  const errs = {};
  if (!data.name?.trim()) errs.name = 'Name is required.';
  if (!data.price || Number(data.price) < 0) errs.price = 'Price must be >= 0.';
  if (!data.category_id) errs.category_id = 'Category is required.';
  if (data.stock === '' || Number(data.stock) < 0) errs.stock = 'Stock must be >= 0.';
  if (!['Active','Inactive'].includes(data.status)) errs.status = 'Status is required.';
  qsa('.error[data-error-for]', form).forEach(e => e.textContent = '');
  Object.entries(errs).forEach(([k,v]) => {
    const el = qs(`.error[data-error-for="${k}"]`, form);
    if (el) el.textContent = v;
  });
  return { ok: Object.keys(errs).length === 0, data, errs };
}

async function submitForm(form) {
  const { ok, data } = validateForm(form);
  if (!ok) return;
  const isEdit = !!data.id;
  const url = isEdit ? `/products/${data.id}` : '/products';
  const method = isEdit ? 'PUT' : 'POST';
  const res = await fetch(url, {
    method,
    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
    body: new FormData(form),
  });
  if (res.status === 422) {
    const j = await res.json();
    Object.entries(j.errors || {}).forEach(([k, msgs]) => {
      const el = qs(`.error[data-error-for="${k}"]`, form);
      if (el) el.textContent = msgs[0];
    });
    return;
  }
  if (!res.ok) { toast('Failed to save', 'error'); return; }
  toast('Saved');
  closeModal('productModal');
  state.page = 1;
  await fetchList();
}

function fillForm(p = null) {
  const form = qs('#productForm');
  form.reset();
  qsa('.error[data-error-for]', form).forEach(e => e.textContent = '');
  const set = (n, v) => { const el = form.elements[n]; if (el) el.value = v ?? ''; };
  if (p) {
    set('id', p.id);
    set('name', p.name);
    set('price', p.price);
    set('description', p.description);
    set('category_id', p.category_id);
    set('stock', p.stock);
    set('status', p.status);
  } else {
    set('id', '');
  }
}

function wireRowActions() {
  qsa('[data-edit]').forEach(btn => {
    btn.onclick = () => {
      const tr = btn.closest('tr');
      const id = tr.dataset.id;
      fillForm({
        id,
        name: qs('[data-view="name"]', tr).textContent.trim(),
        price: qs('[data-view="price"]', tr).textContent.trim(),
        category_id: (__CATEGORIES__ || []).find(c => c.name === qs('[data-view="category"]', tr).textContent.trim())?.id ?? '',
        stock: qs('[data-view="stock"]', tr).textContent.trim(),
        status: qs('[data-view="status"]', tr).textContent.trim(),
      });
      openModal('productModal');
    };
  });
  qsa('[data-delete]').forEach(btn => {
    btn.onclick = () => {
      const tr = btn.closest('tr');
      state.deleteId = tr.dataset.id;
      openModal('confirmModal');
    };
  });
  qsa('[data-inline-edit]').forEach(btn => {
    btn.onclick = () => {
      const tr = btn.closest('tr');
      tr.classList.add('row-edit');
      qs('[data-actions]', tr).style.display = 'none';
      const form = qs('[data-inline-form]', tr);
      form.style.display = 'inline-flex';
      qs('[name="name"]', form).focus();
      const cancel = qs('[data-inline-cancel]', form);
      const save = qs('[data-inline-save]', form);
      const err = qs('[data-inline-error]', form);
      cancel.onclick = (e) => {
        e.preventDefault();
        err.textContent = '';
        form.style.display = 'none';
        qs('[data-actions]', tr).style.display = '';
        tr.classList.remove('row-edit');
      };
      save.onclick = async (e) => {
        e.preventDefault();
        err.textContent = '';
        const fd = new FormData(form);
        const res = await fetch(`/products/${tr.dataset.id}`, {
          method: 'PUT',
          headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
          body: fd
        });
        if (res.status === 422) {
          const j = await res.json();
          err.textContent = Object.values(j.errors).map(a=>a[0]).join(' ');
          return;
        }
        if (!res.ok) { err.textContent = 'Failed to save.'; return; }
        const prevId = tr.dataset.id;
        const params = new URLSearchParams({
          search: state.search, category_id: state.category_id, sort: state.sort, dir: state.dir, page: state.page, perPage: state.perPage
        });
        const html = await (await fetch(`/products/partial?${params.toString()}`, { headers: { 'Accept': 'text/html' } })).text();
        const tmp = document.createElement('div'); tmp.innerHTML = html;
        const newRow = tmp.querySelector(`tr[data-id="${prevId}"]`);
        if (newRow) tr.replaceWith(newRow);
        wireRowActions();
        toast('Updated');
      };
    };
  });
}

async function init() {
  loadPrefs();

  const filter = qs('#filterCategory');
  const search = qs('#searchInput');
  const sortName = qs('#sortName');
  const sortPrice = qs('#sortPrice');
  const btnNew = qs('#btnNew');
  const btnLoadMore = qs('#btnLoadMore');

  filter.value = state.category_id || '';
  search.value = state.search || '';

  filter.onchange = () => {
    state.category_id = filter.value;
    state.page = 1;
    savePrefs();
    fetchList();
  };

  search.oninput = debounce(() => {
    state.search = search.value.trim();
    state.page = 1;
    savePrefs();
    fetchList();
  }, 300);

  sortName.onclick = () => setSort('name');
  sortPrice.onclick = () => setSort('price');

  btnNew.onclick = () => {
    fillForm(null);
    openModal('productModal');
  };

  btnLoadMore.onclick = async () => {
    const meta = qs('#listMeta');
    const next = meta?.dataset.nextPage;
    if (!next) return;
    state.page = Number(next);
    state.scrollY = window.scrollY;
    await fetchList({ append: true });
  };

  qsa('[data-close]').forEach(btn => {
    btn.onclick = () => {
      const modal = btn.closest('.modal');
      if (modal) closeModal(modal.id);
    };
  });

  qs('#confirmDeleteBtn').onclick = async () => {
    if (!state.deleteId) return;
    const id = state.deleteId;
    state.deleteId = null;
    closeModal('confirmModal');
    const res = await fetch(`/products/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' }
    });
    if (!res.ok) { toast('Delete failed', 'error'); return; }
    toast('Deleted');
    state.page = 1;
    await fetchList();
  };

  qs('#productForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await submitForm(e.currentTarget);
  });

  await fetchList();
  wireRowActions();
}

document.addEventListener('DOMContentLoaded', init);