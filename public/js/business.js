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

let buttonEdit = document.getElementById('buttonEditBusiness');
let buttonAdd = document.getElementById('buttonAddBusiness');

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



let idBusiness;
let allPhotoBusiness = [];
let inputPhotofiles = [];

function getData() {

    let divPhoto = [...document.getElementsByClassName('divPhoto')];
    if (divPhoto[0]) {

        idBusiness = divPhoto[0].getAttribute('id');
        allPhotoBusiness = [...document.querySelectorAll('.divPresentPhoto-' + idBusiness)];
    }

    inputPhotofiles = [...document.querySelectorAll('.inputPhotos')];
   

    return [allPhotoBusiness, allPhotoBusiness, inputPhotofiles];
}
getData();


let hasPhoto;

inputPhotofiles[0].addEventListener('change', function () {
    hasPhoto = true;
});



function checkVoidPhotos(id) {
    // let data = getData();


    let editForm = document.getElementById('editFormBusiness');
    let addForm = document.getElementById('addFormBusiness');
   


    if (!hasPhoto) {
        if (allPhotoBusiness.length === 0) {
            alert('vous devez avoir minimum une photo');
        } else {
            if (editForm) {
                editForm.submit();
                
            } else if (addForm) {
                addForm.submit();
            }
        }
    } else {
        if (editForm) {
            editForm.submit();
            
        } else if (addForm) {
            addForm.submit();
        }
    }

}









// ajouter dynamiquement emplacement load photo

let divInputs = document.getElementById('inputPhotos');
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

        divInputs.appendChild(divInputPhoto);

        inputPhoto.addEventListener('change', () => {
            createDivPhotoLoad();
          
        });
        index++;
    }
};
createDivPhotoLoad();


document.getElementById("delete-business-button").addEventListener("click", function (event) {
    event.preventDefault();
    
    Swal.fire({
    text: "Êtes-vous sûr(e) de vouloir supprimer votre commerce ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Oui, supprimer",
    cancelButtonText: "Annuler"
    }).then((result) => {
    if (result.isConfirmed) {
    document.getElementById("delete-business-form").submit();
    }
    });
    });