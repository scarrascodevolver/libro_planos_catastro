/**
 * Script de prueba para llenar folios manuales masivos
 *
 * USO:
 * 1. Ir a la página de crear plano
 * 2. Seleccionar "11-150 folios" → "Manual" → Configurar tipo/ubicación
 * 3. Ingresar cantidad de folios (ej: 15)
 * 4. Abrir consola (F12 → Console)
 * 5. Copiar y pegar este script completo
 * 6. Ejecutar: testManualMasivo()
 *
 * O para probar con cantidad específica:
 * testManualMasivo(11) // Llena 11 folios
 */

// Datos de prueba
const nombresPrueba = ['Juan', 'María', 'Pedro', 'Ana', 'Carlos', 'Lucía', 'Diego', 'Carmen', 'José', 'Rosa', 'Miguel', 'Elena', 'Roberto', 'Patricia', 'Fernando'];
const apellidosPrueba = ['González', 'Rodríguez', 'Martínez', 'López', 'García', 'Hernández', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Flores', 'Rivera', 'Gómez', 'Díaz', 'Reyes'];
const comunasPrueba = ['101', '102', '103', '201', '202', '301', '302']; // Códigos de comunas del Biobío

function randomItem(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
}

function randomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function randomM2() {
    return randomInt(100, 50000);
}

function randomHa() {
    return (randomInt(1, 100) / 10).toFixed(2);
}

async function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function llenarFolioActual(index, total) {
    console.log(`📝 Llenando folio ${index + 1} de ${total}...`);

    // Llenar datos personales
    const solicitante = randomItem(nombresPrueba);
    const apPaterno = randomItem(apellidosPrueba);
    const apMaterno = randomItem(apellidosPrueba);

    $(`.solicitante-manual-multiple[data-index="${index}"]`).val(solicitante).trigger('input');
    $(`.ap-paterno-manual-multiple[data-index="${index}"]`).val(apPaterno);
    $(`.ap-materno-manual-multiple[data-index="${index}"]`).val(apMaterno);

    // Folio opcional (algunos con número, otros sin)
    if (Math.random() > 0.3) {
        $(`.folio-manual-multiple[data-index="${index}"]`).val(randomInt(100000, 999999));
    }

    // Datos del plano (solo primer folio)
    if (index === 0) {
        const comunaCodigo = randomItem(comunasPrueba);
        $(`.comuna-manual-multiple[data-index="${index}"]`).val(comunaCodigo).trigger('change');
        $(`.responsable-manual-multiple[data-index="${index}"]`).val('Responsable Test ' + randomInt(1, 100)).trigger('input');
        $(`.proyecto-manual-multiple[data-index="${index}"]`).val('Proyecto Test ' + randomInt(1, 100)).trigger('input');
    }

    await sleep(100);

    // Seleccionar cantidad de inmuebles (1-3 para prueba rápida)
    const cantidad = randomInt(1, 3);
    $(`.cantidad-inmuebles-manual-multiple[data-index="${index}"]`).val(cantidad).trigger('change');

    await sleep(200);

    // Llenar medidas de cada inmueble
    const esRural = wizardData.esRuralManual;

    for (let i = 0; i < cantidad; i++) {
        if (esRural) {
            // Llenar hectáreas (auto-convierte a M²)
            const ha = randomHa();
            $(`.hectareas-inmueble-manual-multiple[data-folio="${index}"][data-inmueble="${i}"]`).val(ha).trigger('input');
        } else {
            // Solo M²
            $(`.m2-inmueble-manual-multiple[data-folio="${index}"][data-inmueble="${i}"]`).val(randomM2()).trigger('input');
        }
    }

    await sleep(100);

    console.log(`✅ Folio ${index + 1} completado: ${solicitante} ${apPaterno} - ${cantidad} inmueble(s)`);
}

async function avanzarAlSiguiente() {
    const btnSiguiente = $('#btn-folio-siguiente-manual');
    if (btnSiguiente.length && !btnSiguiente.prop('disabled')) {
        btnSiguiente.click();
        await sleep(300);
        return true;
    }
    return false;
}

async function testManualMasivo(cantidadFolios = null) {
    console.log('🚀 Iniciando prueba de manual masivo...');

    // Verificar que estamos en el lugar correcto
    if (!wizardData || wizardData.origenFolios !== 'manual') {
        console.error('❌ Error: Debes estar en modo Manual. Selecciona "11-150 folios" → "Manual" primero.');
        return;
    }

    // Si se especifica cantidad, ingresarla
    if (cantidadFolios) {
        const inputCantidad = $('#cantidad-exacta-manual-masivo');
        if (inputCantidad.length) {
            inputCantidad.val(cantidadFolios).trigger('change');
            await sleep(500);
        }
    }

    // Verificar que hay folios configurados
    const total = wizardData.cantidadFolios;
    if (!total || total < 1) {
        console.error('❌ Error: No hay cantidad de folios configurada. Ingresa la cantidad primero.');
        return;
    }

    console.log(`📊 Procesando ${total} folios...`);

    // Llenar cada folio
    for (let i = 0; i < total; i++) {
        // Verificar que estamos en el folio correcto
        if (wizardData.folioActualIndex !== i) {
            console.error(`❌ Error: Se esperaba folio ${i} pero estamos en ${wizardData.folioActualIndex}`);
            break;
        }

        await llenarFolioActual(i, total);

        // Avanzar al siguiente (excepto en el último)
        if (i < total - 1) {
            const avanzado = await avanzarAlSiguiente();
            if (!avanzado) {
                console.error(`❌ Error: No se pudo avanzar del folio ${i + 1}`);
                break;
            }
        }
    }

    console.log('🎉 ¡Prueba completada! Ahora puedes hacer click en "Finalizar" y luego "Continuar a Confirmación"');
}

// Función auxiliar para limpiar y reiniciar
function resetTest() {
    console.log('🔄 Para reiniciar, recarga la página (F5)');
}

console.log('✅ Script de prueba cargado. Ejecuta: testManualMasivo() o testManualMasivo(15)');
