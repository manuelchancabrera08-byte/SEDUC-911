document.getElementById("formCarga").addEventListener("submit", function(){

    let modal = document.getElementById("modalProgreso");
    modal.style.display = "flex";

    let barra = document.querySelector(".barra-interna");

    let progreso = 0;

    let intervalo = setInterval(() => {

        progreso += 5;
        barra.style.width = progreso + "%";

        if(progreso >= 99){
            clearInterval(intervalo);
        }

    }, 300);

});