const tbody = document.getElementById("tabla-canciones");
const mensaje = document.getElementById("mensaje");

let canciones = [];

function formatearDuracion(total) {
  const minutos = Math.floor(total / 60);
  const segundos = String(total % 60).padStart(2, "0");
  return `${minutos}:${segundos}`;
}

function mostrarMensaje(texto) {
  mensaje.textContent = texto;
}

function renderizarCanciones(lista) {
  tbody.replaceChildren();

  if (lista.length === 0) {
    const fila = document.createElement("tr");
    const celda = document.createElement("td");
    celda.colSpan = 5;
    celda.textContent = "Todavía no hay canciones";
    fila.append(celda);
    tbody.append(fila);
    return;
  }

  for (const cancion of lista) {
    const fila = document.createElement("tr");

    for (const valor of [
      cancion.title,
      cancion.artist,
      cancion.category,
      formatearDuracion(cancion.duration),
    ]) {
      const celda = document.createElement("td");
      celda.textContent = valor;
      fila.append(celda);
    }

    const acciones = document.createElement("td");

    const editar = document.createElement("button");
    editar.type = "button";
    editar.textContent = "Editar";
    editar.dataset.id = cancion.id;
    editar.disabled = true;

    const eliminar = document.createElement("button");
    eliminar.type = "button";
    eliminar.textContent = "Eliminar";
    eliminar.dataset.id = cancion.id;
    eliminar.disabled = true;

    acciones.append(editar, " ", eliminar);
    fila.append(acciones);
    tbody.append(fila);
  }
}

async function cargarCatalogo() {
  mostrarMensaje("Cargando catálogo…");
  try {
    const respuesta = await fetch("api/catalogo.php?action=list");
    const contenido = await respuesta.json();

    if (!respuesta.ok || contenido.ok !== true) {
      throw new Error(contenido.error || "No se pudo consultar.");
    }
    if (!Array.isArray(contenido.data)) {
      throw new Error("La respuesta del servidor no contiene una lista.");
    }

    console.log(contenido.data);
    canciones = contenido.data;
    renderizarCanciones(canciones);
    mostrarMensaje("");
  } catch (error) {
    mostrarMensaje("Error: " + error.message);
  } finally {
    // Aquí solo se retiraría el estado "cargando"; nunca se anuncia éxito.
  }
}

cargarCatalogo();
