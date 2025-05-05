const zoomLevel = 13;
const placeComedie = {
    lat: 43.610769,
    lng: 3.876716
}
const map = L.map('map').setView([placeComedie.lat, placeComedie.lng], zoomLevel);

function init() {
    const mainLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 25,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
}

function setMarker(lat, lon, popup = false, businessId, businessName = '', category = '', promotion = false, openingHour = 'Non rensigné', closingHour = 'Non rensigné', averageScore) {
    const marker = L.marker([lat, lon]).addTo(map);
    if (popup) {

        const promotionText = promotion ? 'Promotion en cours !' : '';

        var popupContent = `
        <div style="text-align: center;">
            <a href="/home/business/${businessId}" style="margin: 0;">${businessName} (<span class="text-xm">${category}</span>)</a>
            <p style="margin: 0;">Horaire du jour: ${openingHour} - ${closingHour}</p>
            <p style="margin: 0;">${promotionText}</p>
            <div style="display: flex; align-items: center; justify-content: center; gap: 5px; margin: 0;">
                <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewbox="0 0 22 20">
                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                </svg>
                <span>${averageScore}/5</span>
            </div>
        </div>`;
    

        marker.bindPopup(popupContent);
    }

    marker.on('click', function () {
        //map.setView([lat, lon], 15);
        map.flyTo([lat, lon], 16, {
            animate: true,
            duration: 2
          });
        if (popup) { marker.openPopup(); }
    });

    // console.log(businessName);
    // console.log("Nouveau marqueur ajouté sur la carte à : " + lat + " / " + lon);
}



function focusView(lat, lon, zoom = 13) {
    // console.log("Set view to : " + lat + " / " + lon);
    map.setView(new L.LatLng(lat, lon, zoom));
}

function clickZoom(e) {
    console.log("click");
    // e.openPopup();
}


init();

