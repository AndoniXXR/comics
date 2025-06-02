<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Eliminar Comic
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>¿Estás seguro de que quieres eliminar "{{ $comic->title }}"?</strong></p>
                <p class="text-warning">
                    <i class="fas fa-warning me-1"></i>
                    Esta acción no se puede deshacer. Se eliminarán:
                </p>
                <ul>
                    <li>El comic y toda su información</li>
                    <li>Todas las páginas ({{ $comic->pages->count() }} páginas)</li>
                    <li>La portada</li>
                    <li>Todas las calificaciones y favoritos</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('comics.destroy', $comic->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>