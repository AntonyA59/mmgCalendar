window.onload = () => {
    // Modal Dashboard Admin
    let supprimerClient = document.querySelectorAll("#client-trigger")
    for (let bouton of supprimerClient) {
        bouton.addEventListener('click', function () {
            document.querySelector(".modal-footer a").href = `/admin/client/desactiveClient/${this.dataset.id}`
            document.querySelector(".modal-body p.mail").innerHTML = `Email: ${this.dataset.mail}"`
            document.querySelector(".modal-body p.societe").innerHTML = `Société: ${this.dataset.societe}"`
            document.querySelector(".modal-body p").innerHTML = `${this.dataset.titre}`
        })
    }
    // Fin Modal DashBoard Admin

    //Debut Modal Rdv
    let supprimerRdv = document.querySelectorAll("#rdv-trigger-admin")
    for (let bouton of supprimerRdv) {
        bouton.addEventListener('click', function () {
            document.querySelector(".modal-footer a").href = `/user/rdv/removeRdv/${this.dataset.id}`
            document.querySelector(".modal-body p.id").innerHTML = `ID: ${this.dataset.id}`
            document.querySelector(".modal-body p.nom").innerHTML = `"${this.dataset.nom}"`
            document.querySelector(".modal-body p.horaire").innerHTML = `Date: ${this.dataset.horaire}"`
            document.querySelector(".modal-body p.societe").innerHTML = `Société: ${this.dataset.societe}"`
        })
    }

}