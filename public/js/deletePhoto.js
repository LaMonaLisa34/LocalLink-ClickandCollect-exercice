
document.querySelectorAll('.businessPhoto').forEach(buttonDelete => {
buttonDelete.addEventListener('click', function() {
    // this.style.display = "none";
    this.remove();
});
});



document.querySelectorAll('.deletePhoto').forEach(buttonDelete => {
buttonDelete.addEventListener('click', function() {
    // this.style.display = "none";
    this.remove();
});
});


// let divHidden = document.querySelectorAll('.divFormEditProduct');

document.querySelectorAll('.editProductButton').forEach(buttonEdit => {
buttonEdit.addEventListener('click', function() {
    let body = document.getElementById('body-id');
    let idButton = buttonEdit.getAttribute('id');
    let divHidden = document.getElementById('divForm-'+idButton);
	// divHidden.style.zIndex = "2";
	divHidden.style.display = "block";
    body.style.filter = 'blur(4px)'; // rendre flou
    divHidden.style.filter = 'blur(0px)'; // rendre net

			
});
});


document.querySelectorAll('.closeButtonEditProduct').forEach(buttonEdit => {
buttonEdit.addEventListener('click', function() {
    let body = document.getElementById('body-id');
    let idButton = buttonEdit.getAttribute('id');
    let divHidden = document.getElementById('divForm-'+idButton);
	// divHidden.style.zIndex = "2";
	divHidden.style.display = "none";
    body.style.filter = 'blur(0px)'; // rendre flou
    // divHidden.style.filter = 'blur(4px)'; // rendre net

			
});
});


document.querySelectorAll('.closeButtonEditProduct').forEach(buttonClose => {
    let idButton = buttonClose.getAttribute('id');
    let divHidden = document.getElementById('divForm-'+idButton);
    buttonClose.addEventListener('click', () => {
        const blurTable = [];
        blurTable.push(document.getElementById('divForm-'+idButton));
        for (let i = 0; i < blurTable.length; i++) {
        }
    });
});


