// Variables globales
let sortableInstance = null;
let reorderMode = false;
let newPagesFiles = [];

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeEditForm();
});

function initializeEditForm() {
    // Character counter
    const synopsis = document.getElementById('synopsis');
    const synopsisCount = document.getElementById('synopsisCount');
    
    synopsis.addEventListener('input', function() {
        synopsisCount.textContent = this.value.length;
    });
    
    // Event listeners
    document.getElementById('cover_image').addEventListener('change', handleCoverUpload);
    document.getElementById('newPages').addEventListener('change', handleNewPagesUpload);
    
    // Form submission
    document.getElementById('editComicForm').addEventListener('submit', function() {
        const btn = document.getElementById('updateBtn');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');
        
        btn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
    });
}

// === GESTIÓN DE PORTADA ===
function handleCoverUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    if (!file.type.match(/^image\/(jpeg|png|jpg)$/)) {
        alert('Solo JPG, PNG o JPEG');
        e.target.value = '';
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        alert('Máximo 5MB');
        e.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        showCoverPreview(e.target.result);
    };
    reader.readAsDataURL(file);
}

function showCoverPreview(src) {
    document.getElementById('coverPreview').innerHTML = `<img src="${src}" alt="Nueva portada" class="cover-image">`;
    document.getElementById('coverControls').style.display = 'flex';
}

function removeCover() {
    document.getElementById('coverPreview').innerHTML = `
        <div class="cover-placeholder">
            <i class="fas fa-image fa-3x"></i>
            <h6>Cambiar Portada</h6>
            <p>JPG, PNG hasta 5MB</p>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover_image').click()">
                <i class="fas fa-upload me-1"></i>Seleccionar
            </button>
        </div>
    `;
    document.getElementById('coverControls').style.display = 'none';
    document.getElementById('cover_image').value = '';
}

// === GESTIÓN DE PÁGINAS ===
function handleNewPagesUpload(e) {
    const files = Array.from(e.target.files);
    if (files.length === 0) return;
    
    // Validación
    for (let file of files) {
        if (!file.type.match(/^image\/(jpeg|png|jpg)$/)) {
            alert('Solo JPG, PNG o JPEG');
            e.target.value = '';
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert('Máximo 10MB por página');
            e.target.value = '';
            return;
        }
    }
    
    newPagesFiles = files;
    updateNewPagesDisplay();
}

function updateNewPagesDisplay() {
    const container = document.getElementById('newPagesContainer');
    const grid = document.getElementById('newPagesGrid');
    
    if (newPagesFiles.length > 0) {
        container.style.display = 'block';
        grid.innerHTML = '';
        
        Array.from(newPagesFiles).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'new-page-item';
                div.innerHTML = `
                    <div class="page-item-header">
                        <span class="page-number-badge new">+${index + 1}</span>
                        <button type="button" class="btn-page-control btn-delete" onclick="removeNewPage(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="page-thumbnail-container">
                        <img src="${e.target.result}" class="page-management-thumb">
                    </div>
                    <div class="page-info-small">
                        <small>${file.name}</small>
                    </div>
                `;
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        container.style.display = 'none';
    }
}

function removeNewPage(index) {
    const filesArray = Array.from(newPagesFiles);
    filesArray.splice(index, 1);
    
    const dt = new DataTransfer();
    filesArray.forEach(file => dt.items.add(file));
    document.getElementById('newPages').files = dt.files;
    
    newPagesFiles = dt.files;
    updateNewPagesDisplay();
}

// === REORDENAMIENTO MEJORADO ===
function toggleReorderMode() {
    reorderMode = !reorderMode;
    const container = document.getElementById('pagesContainer');
    const handles = document.querySelectorAll('.drag-handle');
    const saveBtn = document.getElementById('saveOrderBtn');
    const cancelBtn = document.getElementById('cancelOrderBtn');
    const reorderBtn = document.querySelector('[onclick="toggleReorderMode()"]');
    
    if (reorderMode) {
        // Activar modo reordenar
        container.classList.add('reorder-mode');
        handles.forEach(h => h.style.display = 'block');
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
        reorderBtn.innerHTML = '<i class="fas fa-times me-2"></i>Cancelar Reorden';
        
        // Guardar orden original
        saveOriginalOrder();
        
        if (typeof Sortable !== 'undefined') {
            sortableInstance = Sortable.create(container, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                direction: 'horizontal',
                onStart: function(evt) {
                    console.log('Iniciando drag:', evt.oldIndex);
                },
                onEnd: function(evt) {
                    console.log('Terminando drag:', evt.oldIndex, '->', evt.newIndex);
                    updatePageNumbers();
                }
            });
        }
    } else {
        // Desactivar modo reordenar
        container.classList.remove('reorder-mode');
        handles.forEach(h => h.style.display = 'none');
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        reorderBtn.innerHTML = '<i class="fas fa-arrows-alt me-2"></i>Reordenar Páginas';
        
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
        }
    }
}

// Variable para guardar orden original
let originalOrder = [];

function saveOriginalOrder() {
    originalOrder = [];
    document.querySelectorAll('.page-management-item').forEach((item, index) => {
        originalOrder.push({
            id: item.dataset.pageId,
            number: item.dataset.pageNumber,
            element: item.cloneNode(true)
        });
    });
    console.log('Orden original guardado:', originalOrder);
}

function updatePageNumbers() {
    document.querySelectorAll('.page-management-item').forEach((item, index) => {
        const badge = item.querySelector('.page-number-badge');
        const newNumber = index + 1;
        badge.textContent = newNumber;
        item.dataset.pageNumber = newNumber;
    });
}

function savePageOrder() {
    const newOrder = [];
    document.querySelectorAll('.page-management-item').forEach((item, index) => {
        newOrder.push({
            id: parseInt(item.dataset.pageId),
            newNumber: index + 1
        });
    });
    
    console.log('Guardando nuevo orden:', newOrder);
    
    // Obtener el ID del comic desde la URL
    const currentUrl = window.location.href;
    const urlParts = currentUrl.split('/');
    const comicId = urlParts[urlParts.indexOf('comics') + 1];
    
    console.log('Comic ID detectado:', comicId);
    
    // Obtener token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('❌ Error: Token CSRF no encontrado');
        return;
    }
    
    console.log('Token CSRF:', csrfToken.getAttribute('content'));
    
    // Enviar AJAX
    fetch(`/comics/${comicId}/reorder-pages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ pages: newOrder })
    })
    .then(response => {
        console.log('Respuesta del servidor:', response.status, response.statusText);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error del servidor (texto):', text);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        
        if (data.success) {
            alert('✅ Orden de páginas actualizado correctamente');
            toggleReorderMode();
            
            // Actualizar badge con confirmación visual
            const badge = document.querySelector('.section-title .badge');
            badge.classList.add('bg-success');
            setTimeout(() => {
                badge.classList.remove('bg-success');
                badge.classList.add('bg-primary');
            }, 2000);
        } else {
            alert('❌ Error al actualizar el orden: ' + (data.message || 'Error desconocido'));
            console.error('Error del servidor:', data);
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        alert('❌ Error de conexión: ' + error.message);
    });
}

function cancelReorder() {
    if (originalOrder.length === 0) {
        location.reload();
        return;
    }
    
    // Restaurar orden original
    const container = document.getElementById('pagesContainer');
    container.innerHTML = '';
    
    originalOrder.forEach(orig => {
        container.appendChild(orig.element.cloneNode(true));
    });
    
    console.log('Orden restaurado');
    toggleReorderMode();
}

// === ELIMINAR PÁGINA ===
function deletePage(pageId) {
    if (!confirm('¿Eliminar esta página?')) return;
    
    const element = document.querySelector(`[data-page-id="${pageId}"]`);
    element.remove();
    updatePageNumbers();
    
    const badge = document.querySelector('.section-title .badge');
    const count = document.querySelectorAll('.page-management-item').length;
    badge.textContent = `${count} páginas`;
    
    alert('Página eliminada');
}

// === MODAL ===
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}