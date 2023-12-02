async function initMap() {
    // Request needed libraries.
    const { Map, InfoWindow } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    //Default coordinates: 41.527785 -8.622201
    let latitude = document.getElementById("address-latitude").value
    let longitude = document.getElementById("address-longitude").value;

    if (latitude === "" || longitude === "") {
        latitude = 41.527785;
        longitude = -8.622201;
    }

    latitude = parseFloat(latitude);
    longitude = parseFloat(longitude);

    const map = new Map(document.getElementById("map"), {
        center: { lat: latitude, lng: longitude },
        zoom: 14,
        mapId: "edit-playground-domingoasdez",
    });
    const infoWindow = new InfoWindow();
    const draggableMarker = new AdvancedMarkerElement({
        map,
        position: { lat: latitude, lng: longitude },
        gmpDraggable: true,
        title: "This marker is draggable.",
    });

    draggableMarker.addListener("dragend", (event) => {
        const position = draggableMarker.position;

        document.getElementById("address-latitude").value = position.lat;
        document.getElementById("address-longitude").value = position.lng;

        infoWindow.close();
        infoWindow.setContent(
            `Campo definido em: ${position.lat.toFixed(4)}, ${position.lng.toFixed(4)}`,
        );
        infoWindow.open(draggableMarker.map, draggableMarker);
    });
}

$(document).ready(function () {
    initMap();
})