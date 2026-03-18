let progreso = 0;
let intervalo;
let intervaloMensajes;

function mostrarProgreso(){

    let modal = document.getElementById("modalProgreso");
    modal.style.display = "flex";

    let barra = document.getElementById("barraProgreso");
    let texto = document.getElementById("porcentajeProgreso");

    let mensajes = [
        "Preparando archivo...",
        "Procesando datos...",
        "Insertando registros...",
        "Validando información...",
        "Finalizando..."
    ];

    let i = 0;

    clearInterval(intervalo);
    clearInterval(intervaloMensajes);

    progreso = 0;

    /* 🔥 PROGRESO FAKE */
    intervalo = setInterval(() => {

        if(progreso < 99){
            progreso += Math.random() * 7;
            progreso = Math.min(progreso, 99);

            barra.style.width = progreso + "%";
            texto.innerText = Math.round(progreso) + "%";
        }

    }, 800);

    /* 🔥 MENSAJES */
    intervaloMensajes = setInterval(() => {

        document.querySelector("#modalProgreso p").innerText = mensajes[i];
        i = (i + 1) % mensajes.length;

    }, 1800);

}