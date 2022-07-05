let bouton1;
let bouton2;
let mdpSaisi1;
let mdpSaisi2;
let regMaj = /[A-Z]/;
let regMin = /[a-z]/;
let regChiffre = /[0-9]/;
let regCaractere = /\W/;
let compteurSec;
let text = document.getElementById("message");
let text2= document.getElementById("message2")

/**
 * Fonction permettant de prevenir l'utilisateur visuellement que le mot de passe est valide
 */
function verification_mdp(){
    /* on cible le champ ou on entre le mot passe */
    mdpSaisi1 = document.querySelector(".mdpVerif").value;

    /* On initialise le compteur à 0  */
    compteurSec = 0;

    /* Si le champ du mot de passe est vide, il n'affiche aucun message */
    if(mdpSaisi1 == ""){
        text.innerHTML= ''
    }
    /* On vérifie si champ du mot de passe contient un chiffre et le compteur est incrémenter de 1 */
    if(regChiffre.test(mdpSaisi1)){
        compteurSec ++;
    }
    /* Si le champ du mot de passe contient une minuscule et le compteur est incrémenter de 1 */
    if(regMin.test(mdpSaisi1)){
        compteurSec ++;
    }
    /* Si le champ du mot de passe contient une majuscule et le compteur est incrémenter de 1 */
    if(regMaj.test(mdpSaisi1)){
        compteurSec ++;
    }
    /* Si le champ du mot de passe contient un caractére spéciale,  le compteur est incrémenter de 1 */
    if(regCaractere.test(mdpSaisi1)){
        compteurSec ++;
    }
    /* Si le champ du mot de passe à une longueur inférieur a 8 , le compteur est décrémenter de 1 */
    if(mdpSaisi1.length < 8){
        compteurSec --
    }
    
    // Alerte par rapport au résultat du compteur
    if(compteurSec == 0){
        text.innerHTML= '<span class="text-danger fw-lighter" >Mot de passe invalide</span>';
    }
    
    if(compteurSec == 1){
        text.innerHTML= '<span class="text-danger fw-lighter" >Mot de passe invalide</span>';
    }

    if(compteurSec == 2){
        text.innerHTML= '<span class="text-danger fw-lighter" >Mot de passe invalide</span>';
    }

    if(compteurSec == 3){
        text.innerHTML= '<span class="text-danger fw-lighter" >Mot de passe invalide</span>';
    }

    if(compteurSec == 4){
        text.innerHTML= '<span class="text-success fw-lighter">Mot de passe valide</span>';
    }

}

document.querySelector(".mdpVerif",).addEventListener('keyup', verification_mdp, false );

/**
 * Fonction qui permet de verifier si les deux mots de passe sont identiques
 */
function confirm_mdp(){
    /* On cible les deux champ du mots de passe */
    mdpSaisi1 = document.querySelector(".mdpVerif").value;
    mdpSaisi2 = document.querySelector(".mdpConfirm").value;

    if(mdpSaisi1 == mdpSaisi2){
        text2.innerHTML='<span class="text-success fw-lighter">Les deux mots de passe sont identiques</span>'
    }else{
        text2.innerHTML='<span class="text-danger fw-lighter">Les deux mots de passe ne sont pas identiques</span>'
    }
    
    if(mdpSaisi2 == ""){
        text2.innerHTML=''
    }
}   

document.querySelector(".mdpConfirm").addEventListener('keyup', confirm_mdp, false );