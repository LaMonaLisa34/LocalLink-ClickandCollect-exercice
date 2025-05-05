let totalpriceText = document.getElementById('total-price');
let saveInput = document.getElementById('save');
let commandInput = document.getElementById('command');
let form = document.getElementById('form');
let multPrice = 0;
let save = false;


function updatePrices(element, price, id) {
    if (!save) {
        save = true;
        saveInput.classList.remove('hidden')
        commandInput.classList.add('hidden')
    }
    element.name = `products[${id}]`
    let multPriceText = document.getElementById(id);
    multPrice = element.value * price;
    multPriceText.innerHTML = `${multPrice.toFixed(2)
        } €`;
    let AllPrices = document.querySelectorAll('.multPrice')
    totalPrice = 0
    AllPrices.forEach(element => {
        tempPrice = element.innerText;
        totalPrice += parseFloat(tempPrice);
    });
    totalpriceText.innerHTML = `Total : ${totalPrice.toFixed(2)
        } €`;
}

function sumbit(userId) {
    form.action = `/user/${userId}/cart/update`;
    if (save) {
        save = false;
        saveInput.classList.add('hidden')
        commandInput.classList.remove('hidden')
        form.submit()
    }
}

window.onbeforeunload = confirmExit;
function confirmExit() {
    if (!save) {
    } else {
        return "Vous allez quitter cette page.  Si vous ne cliquez pas sur le bouton de sauvegarde, tout changement sera perdu. Êtes vous sur de vouloir quitter la page ?";
    }
}

function deleteFunction(userId, cartId) {
    save = false;
    form.action = `/user/${userId}/cart/${cartId}/delete`;
    form.submit();
}


function submitCommand(userId) {
    form.action = `/user/${userId}/command/create`;
    form.submit();
}