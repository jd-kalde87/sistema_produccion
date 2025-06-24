document.addEventListener("DOMContentLoaded", function () {
    const puntadasInput = document.getElementById('puntadas');
    const cantidadInput = document.getElementById('cantidad');
    const totalInput = document.getElementById('total_puntadas');

    function calcularTotal() {
        const puntadas = parseInt(puntadasInput.value) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        totalInput.value = puntadas * cantidad;
    }

    if (puntadasInput && cantidadInput && totalInput) {
        puntadasInput.addEventListener('input', calcularTotal);
        cantidadInput.addEventListener('input', calcularTotal);
    }
});
