$('#annonce-image').click(function () {
    const index = + $('#widgets-counter').val();
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g, index);

    $('#annonce_images').append(tmpl);
    $('#widgets-counter').val(index + 1);

    handleDeleteButton();
});

function handleDeleteButton() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#annonce_images div.form-group').length;

    $('#widgets-counter').val(count);
}
updateCounter();
handleDeleteButton();