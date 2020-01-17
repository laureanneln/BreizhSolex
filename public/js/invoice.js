$('#add-item').click(function() {
    // Récupérer le numéro des futurs champs qu'on créé
    const index = +$('#widgets-counter').val();

    // Récupérer le prototype des entrées
    const tmpl = $('#invoice_items').data('prototype').replace(/_name_/g, index);

    // Injecter le code au sein de la div
    $('#invoice_items').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Gérer le bouton supprimer
    handleDeleteButtons();
})

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        $(target).remove();
    })
}

handleDeleteButtons();