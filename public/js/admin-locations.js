document.addEventListener('DOMContentLoaded', function () {
  // Center admin map roughly on Philippines area
  var map = L.map('admin-map').setView([12.8797, 121.7740], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  var currentMarker = null;

  // Show only existing locations that are within Philippines bounds
  var PH_BOUNDS = { minLat:4, maxLat:21, minLng:115, maxLng:130 };
  function inPhilippines(lat, lng){
    return lat >= PH_BOUNDS.minLat && lat <= PH_BOUNDS.maxLat && lng >= PH_BOUNDS.minLng && lng <= PH_BOUNDS.maxLng;
  }

  if (window.existingLocations && window.existingLocations.length) {
    window.existingLocations.forEach(function(p){
      if (!inPhilippines(p.lat, p.lng)) return; // skip non-Philippines
      var m = L.marker([p.lat, p.lng]).addTo(map);
      var popup = '<div style="display:flex;align-items:center">'
        + (p.image? '<img src="'+p.image+'" style="width:90px;height:60px;object-fit:cover;margin-right:8px;border-radius:4px">':'')
        + '<div><b>'+p.name+'</b></div></div>';
      m.bindPopup(popup);
    });
  }

  function setLatLngInputs(lat,lng){
    document.getElementById('latitude').value = lat.toFixed(7);
    document.getElementById('longitude').value = lng.toFixed(7);
  }

  map.on('click', function(e){
    var lat = e.latlng.lat, lng = e.latlng.lng;
    if (!inPhilippines(lat, lng)){
      alert('Please click within the Philippines bounds.');
      return;
    }
    if (currentMarker) map.removeLayer(currentMarker);
    currentMarker = L.marker(e.latlng, {draggable:true}).addTo(map);
    setLatLngInputs(e.latlng.lat, e.latlng.lng);
    currentMarker.on('dragend', function(ev){
      var p = ev.target.getLatLng();
      if (!inPhilippines(p.lat, p.lng)){
        alert('Marker moved outside Philippines; move it back within bounds.');
      }
      setLatLngInputs(p.lat,p.lng);
    });
  });

  // Allow placing/moving marker from manual inputs
  function createOrMoveMarker(lat, lng){
    var latNum = parseFloat(lat);
    var lngNum = parseFloat(lng);
    if (isNaN(latNum) || isNaN(lngNum)) return false;
    if (!inPhilippines(latNum, lngNum)) return false;
    var latlng = L.latLng(latNum, lngNum);
    if (currentMarker) {
      currentMarker.setLatLng(latlng);
    } else {
      currentMarker = L.marker(latlng, {draggable:true}).addTo(map);
      currentMarker.on('dragend', function(ev){
        var p = ev.target.getLatLng();
        setLatLngInputs(p.lat,p.lng);
      });
    }
    map.panTo(latlng);
    return true;
  }

  var placeBtn = document.getElementById('place-from-inputs');
  if (placeBtn){
    placeBtn.addEventListener('click', function(){
      var lat = document.getElementById('latitude').value;
      var lng = document.getElementById('longitude').value;
      if (!createOrMoveMarker(lat,lng)) {
        alert('Please enter valid numeric latitude and longitude within the Philippines bounds.');
      }
    });
  }

  // Update marker when inputs are changed (optional: on blur)
  var latInput = document.getElementById('latitude');
  var lngInput = document.getElementById('longitude');
  if (latInput && lngInput){
    latInput.addEventListener('change', function(){ createOrMoveMarker(latInput.value, lngInput.value); });
    lngInput.addEventListener('change', function(){ createOrMoveMarker(latInput.value, lngInput.value); });
  }

  // Image preview for file input
  var imageFile = document.getElementById('image_file');
  var imagePreview = document.getElementById('image-preview');
  if (imageFile && imagePreview){
    imageFile.addEventListener('change', function(ev){
      var f = ev.target.files[0];
      if (!f) return imagePreview.innerHTML = '';
      var reader = new FileReader();
      reader.onload = function(e){
        imagePreview.innerHTML = '<img src="'+e.target.result+'" style="max-width:200px;border-radius:6px" />';
      };
      reader.readAsDataURL(f);
    });
  }
});
