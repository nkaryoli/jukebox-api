const tbody = document.getElementById("tabla-canciones");
const mensaje = document.getElementById("mensaje");
const formulario = document.getElementById("formulario-cancion");
const tituloFormulario = document.getElementById("titulo-formulario");
const avisoEdicion = document.getElementById("aviso-edicion");
const botonGuardar = document.getElementById("boton-guardar");
const botonCancelar = document.getElementById("boton-cancelar");

let canciones = [];

let operacionEnCurso = false;

let idEditando = null;

function formatearDuracion(total) {
  const minutos = Math.floor(total / 60);
  const segundos = String(total % 60).padStart(2, "0");
  return `${minutos}:${segundos}`;
}

function mostrarMensaje(texto) {
  mensaje.textContent = texto;
}

function leerToken() {
  return document.querySelector('meta[name="csrf-token"]').content;
}

function bloquearAcciones(bloquear) {
  operacionEnCurso = bloquear;
  botonGuardar.disabled = bloquear;
  botonCancelar.disabled = bloquear;
  for (const boton of tbody.querySelectorAll('button[data-accion="editar"]')) {
    boton.disabled = bloquear;
  }
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
    editar.dataset.accion = "editar";
    editar.disabled = operacionEnCurso;

    const eliminar = document.createElement("button");
    eliminar.type = "button";
    eliminar.textContent = "Eliminar";
    eliminar.dataset.id = cancion.id;
    eliminar.dataset.accion = "eliminar";
    eliminar.disabled = true;

    acciones.append(editar, " ", eliminar);
    fila.append(acciones);
    tbody.append(fila);
  }
}

async function cargarCatalogo() {
    mostrarMensaje('Cargando catálogo…');
    let hayError = false;
    try {
        const respuesta = await fetch('api/catalogo.php?action=list');
        const contenido = await respuesta.json();

        if (!respuesta.ok || contenido.ok !== true) {
            throw new Error(contenido.error || 'No se pudo consultar.');
        }
        if (!Array.isArray(contenido.data)) {
            throw new Error('La respuesta del servidor no contiene una lista.');
        }

        canciones = contenido.data;
        renderizarCanciones(canciones);
    } catch (error) {
        hayError = true;
        mostrarMensaje('Error: ' + error.message);
    } finally {
        if (!hayError) {
            mostrarMensaje('');
        }
    }
}

function salirModoEdicion() {
  idEditando = null;
  formulario.reset();
  tituloFormulario.textContent = "Añadir canción";
  botonGuardar.textContent = "Añadir canción";
  avisoEdicion.hidden = true;
  avisoEdicion.textContent = "";
  botonCancelar.hidden = true;
}

function iniciarEdicion(id) {
  if (!Number.isInteger(id) || id < 1) {
    mostrarMensaje("Error: identificador no válido.");
    return;
  }
  const elegida = canciones.find((cancion) => cancion.id === id);
  if (!elegida) {
    mostrarMensaje(
      "Error: la canción ya no está en la lista. Recarga la página.",
    );
    return;
  }

  idEditando = id;
  formulario.elements.title.value = elegida.title;
  formulario.elements.artist.value = elegida.artist;
  formulario.elements.category.value = elegida.category;
  formulario.elements.duration.value = elegida.duration;
  formulario.elements.src.value = elegida.src;

  tituloFormulario.textContent = "Editar canción";
  botonGuardar.textContent = "Guardar cambios";
  avisoEdicion.textContent = "Editando: " + elegida.title;
  avisoEdicion.hidden = false;
  botonCancelar.hidden = false;
  mostrarMensaje("");

  formulario.elements.title.focus();
}

async function enviarFormulario(evento) {
  evento.preventDefault();
  if (operacionEnCurso) {
    return;
  }

  const editando = idEditando !== null;
  const datos = {
    title: formulario.elements.title.value,
    artist: formulario.elements.artist.value,
    category: formulario.elements.category.value,
    duration: Number(formulario.elements.duration.value),
    src: formulario.elements.src.value,
  };
  if (editando) {
    datos.id = idEditando;
  }

  bloquearAcciones(true);
  mostrarMensaje("Guardando…");

  try {
    const accion = editando ? "update" : "create";
    const respuesta = await fetch("api/catalogo.php?action=" + accion, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": leerToken(),
      },
      body: JSON.stringify(datos),
    });
    const contenido = await respuesta.json();

    if (!respuesta.ok || contenido.ok !== true) {
      throw new Error(contenido.error || "No se pudo guardar la canción.");
    }
    if (!Array.isArray(contenido.data)) {
      throw new Error("La respuesta del servidor no contiene una lista.");
    }

    canciones = contenido.data;
    renderizarCanciones(canciones);
    salirModoEdicion();
    mostrarMensaje(editando ? "Cambios guardados" : "Canción añadida");
  } catch (error) {
    if (error instanceof TypeError) {
      mostrarMensaje(
        "No se ha podido confirmar la operación. Recarga la página y revisa la lista antes de reenviar.",
      );
    } else {
      mostrarMensaje("Error: " + error.message);
    }
  } finally {
    bloquearAcciones(false);
  }
}

tbody.addEventListener("click", (evento) => {
  const boton = evento.target.closest('button[data-accion="editar"]');
  if (boton && !operacionEnCurso) {
    iniciarEdicion(Number(boton.dataset.id));
  }
});

botonCancelar.addEventListener("click", () => {
  salirModoEdicion();
  mostrarMensaje("Edición cancelada");
  formulario.elements.title.focus();
});

formulario.addEventListener("submit", enviarFormulario);

cargarCatalogo();
