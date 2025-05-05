let buttonAdd = document.getElementById('buttonAddId');
disableButton(true, buttonAdd);

// afficher modale ajouter un produit
document.querySelectorAll('.addProductButton').forEach(buttonAdd => {
    buttonAdd.addEventListener('click', function () {
        let bodyDiv = document.getElementById('body-id');
        let divHidden = document.getElementById('divAddProduct');
        divHidden.style.display = "block";
        bodyDiv.style.filter = 'blur(4px)';
        bodyDiv.style.pointerEvents = 'none';
        divHidden.style.filter = 'blur(0px)';


    });
});


// afficher modale édition produit
document.querySelectorAll('.editProductButton').forEach(buttonEdit => {
    buttonEdit.addEventListener('click', function () {

        let bodyDiv = document.getElementById('body-id');
        let idButton = buttonEdit.getAttribute('id');
        let divHidden = document.getElementById('divForm-' + idButton);
        divHidden.style.display = "block";
        bodyDiv.style.filter = 'blur(4px)';
        divHidden.style.filter = 'blur(0px)';
        bodyDiv.style.pointerEvents = 'none';



    });
});

function disableButton(params, button) {
    if (params) {
        button.disabled = true;
        button.classList.remove('bg-green-500', 'text-white', 'cursor-pointer', 'hover:bg-green-600');
        button.classList.add('bg-gray-300', 'text-gray-700', 'cursor-not-allowed');
    } else {
        button.disabled = false;
        button.classList.remove('bg-gray-300', 'text-gray-700', 'cursor-not-allowed');
        button.classList.add('bg-green-500', 'text-white', 'cursor-pointer', 'hover:bg-green-600');
    }
    return;
}

let arrayIdProduct = [];
let allPhotoProduct = [];
let inputPhotofiles = [];

function getData() {
    document.querySelectorAll('.closeButtonEditProduct').forEach(button => {
        let idProduct = button.getAttribute('id');
   
        arrayIdProduct.push(idProduct);
        allPhotoProduct.push([...document.querySelectorAll('.divPresentPhoto-' + idProduct)]);
        inputPhotofiles.push([...document.querySelectorAll('.inputPhotos-' + idProduct)]);
    });
    return [allPhotoProduct, allPhotoProduct, inputPhotofiles];
}
getData();




// supprimer les div des photos
document.querySelectorAll('.deletePhoto').forEach(buttonDelete => {
    let idPhoto = buttonDelete.getAttribute('id');
    buttonDelete.addEventListener('click', function () {
        let divPresentPhoto = document.getElementById('divPresPhoto-' + idPhoto);
        divPresentPhoto.remove();
           arrayIdProduct = [];
        allPhotoProduct = [];
        inputPhotofiles = [];
        getData();

    });
});


let hasPhoto;

// function photoPresent() {
    inputPhotofiles.forEach(inputFile => {
        inputFile[0].addEventListener('change', function () {
            hasPhoto = true;
        });
    })
// }



function checkVoidPhotos(id) {
    // let data = getData();


    let editForm = document.getElementById('editForm-' + id);
    let index = arrayIdProduct.indexOf(id.toString());


    if (!hasPhoto) {
        if (allPhotoProduct[index].length === 0) {
            alert('vous devez avoir minimum une photo');
        } else {
            editForm.submit();
        }
    } else {
        editForm.submit();
    }

}


// ajouter dynamiquement emplacement load photo
document.querySelectorAll('.inputPhotos').forEach(div => {
    let index = 1;
    function createDivPhotoLoad() {
        if (index > 4) {
            return;
        } else {
            let divInputPhoto = document.createElement('div');

            let labelPhoto = document.createElement('label');

            labelPhoto.className = 'block text-sm font-medium text-gray-700';
            labelPhoto.innerText = "Photo " + index;
            labelPhoto.setAttribute('for', 'photo' + index);
            divInputPhoto.appendChild(labelPhoto);

            let inputPhoto = document.createElement('input');
            inputPhoto.className = 'inputPhotoFile mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-indigo-600 hover:file:bg-indigo-100';
            inputPhoto.type = 'file';
            inputPhoto.setAttribute('id', 'photo' + index);
            inputPhoto.setAttribute('name', 'photo' + index);
            inputPhoto.setAttribute('accept', 'image/*');
            divInputPhoto.appendChild(inputPhoto);

            div.appendChild(divInputPhoto);

            inputPhoto.addEventListener('change', () => {
                createDivPhotoLoad();
                disableButton(false, buttonAdd);
            });
            index++;
        }
    };
    createDivPhotoLoad();
});



// fermer modale éditer un produit
document.querySelectorAll('.closeButtonEditProduct').forEach(buttonClose => {
    buttonClose.addEventListener('click', function () {
        let body = document.getElementById('body-id');
        let idButton = buttonClose.getAttribute('id');
        let divHidden = document.getElementById('divForm-' + idButton);
        divHidden.style.display = "none";
        body.style.filter = 'blur(0px)'; // rendre net
        body.style.pointerEvents = 'auto';


    });
});





// fermer modale ajouter un produit
document.querySelectorAll('.closeAddProduct').forEach(buttonClose => {
    buttonClose.addEventListener('click', function () {
        let body = document.getElementById('body-id');
        let divHidden = document.getElementById('divAddProduct');
        divHidden.style.display = "none";
        body.style.filter = 'blur(0px)'; // rendre net
        body.style.pointerEvents = 'auto';

    });
});







var bool = true;

document.addEventListener("DOMContentLoaded", function () {

    selectAll = document.getElementById('select-all-products');

    const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');

    selectAll.addEventListener('click', () => {
        if (selectAll) {
            if (bool === true) {
                checkboxes.forEach(checkbox => checkbox.checked = bool);
                bool = false;
            }
            else {
                checkboxes.forEach(checkbox => checkbox.checked = bool);
                bool = true;
            }
        }
    });
});