function validate() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const form = document.getElementById('id-form');

    if (new Date(startDate) > new Date(endDate)) {
        alert("La date de fin doit être ultérieure à la date de début.");
        return false;
    } else {
        if (form.checkValidity()) {
            form.submit();
        } else {
            window.alert('Veuillez remplir tous les champs');
        }
        return true;
    }

    let idBusiness;
    let photoPromotion;
    let inputPhotofile;

    function getData() {

            photoPromotion = document.getElementById('divPresPhoto');
            inputPhotofile = document.getElementById('photoId');

        return [photoPromotion, inputPhotofile];
    }
    getData();


    let hasPhoto;
    document.getElementById('photoId').addEventListener('change', function () {
        hasPhoto = true;
    });

    if (!hasPhoto) {
        if (!photoPromotion) {
            alert('vous devez avoir une photo');
        } else {
            form.submit();
    
        }
    } else {
        form.submit();
    
    }
}

// supprimer les div des photos
document.querySelectorAll('.deletePhoto').forEach(buttonDelete => {
    let idPhoto = buttonDelete.getAttribute('id');
    buttonDelete.addEventListener('click', function () {
        let divPresentPhoto = document.getElementById('divPresPhoto');
        divPresentPhoto.remove();
        arrayIdProduct = [];
        allPhotoProduct = [];
        inputPhotofiles = [];
        getData();

    });
});

