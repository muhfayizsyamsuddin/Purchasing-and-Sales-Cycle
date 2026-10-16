// Gentelella v4 — Vite entry.
import 'gentelella';

// Custom styles.
import '../css/gentelella.scss';
import '../css/barang.css';

import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const notyf = new Notyf({
    duration: 4000,
    position: {
        x: 'left',
        y: 'bottom',
    },
    dismissible: true,
});

window.notyf = notyf;

// Global delete confirmation using SweetAlert2.
document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) return;

    const method = form.querySelector('input[name="_method"]')
        ?.value?.toUpperCase();

    if (method !== 'DELETE') return;

    if (form.dataset.confirmedDelete === 'true') {
        delete form.dataset.confirmedDelete;
        return;
    }

    event.preventDefault();

    if (form.dataset.confirmingDelete === 'true') return;
    form.dataset.confirmingDelete = 'true';

    try {
        const result = await Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        });

        if (result.isConfirmed) {
            form.dataset.confirmedDelete = 'true';
            form.requestSubmit();
        }
    } finally {
        delete form.dataset.confirmingDelete;
    }
});