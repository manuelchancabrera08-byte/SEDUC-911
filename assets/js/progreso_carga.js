/* PROGRESO REAL SEGURO */

function mostrarProgreso(){

    let modal = document.getElementById("modalProgreso");
    modal.style.display = "flex";

    let barra = document.getElementById("barraProgreso");
    let texto = document.getElementById("porcentajeProgreso");

    let intervalo = setInterval(function(){

        fetch("includes/progreso_carga.php")
        .then(res => res.json())
        .then(data => {

            let progreso = data.progreso || 0;

            barra.style.width = progreso + "%";
            texto.innerText = progreso + "%";

            if(progreso >= 100){
                clearInterval(intervalo);
            }

        });

    },1000);

}

/* ACTIVAR SOLO VISUAL */
document.getElementById("formCarga").addEventListener("submit", function(){
    mostrarProgreso();
});