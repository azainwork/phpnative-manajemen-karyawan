// Flash auto-dismiss
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(el)?.close(), 5000);
});

// Confirm delete
document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', e => {
        if (!confirm('Yakin ingin menghapus data ini?')) e.preventDefault();
    });
});

// Foto preview
const inputFoto = document.getElementById('foto');
const previewFoto = document.getElementById('previewFoto');
if (inputFoto && previewFoto) {
    inputFoto.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/jpg'].includes(file.type)) {
            alert('Format harus JPG/JPEG!'); this.value = ''; return;
        }
        if (file.size > 300 * 1024) {
            alert('Ukuran maksimal 300KB!'); this.value = ''; return;
        }
        const reader = new FileReader();
        reader.onload = e => previewFoto.src = e.target.result;
        reader.readAsDataURL(file);
    });
}