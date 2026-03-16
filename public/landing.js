document.addEventListener('DOMContentLoaded', function () {
  // Default center roughly Philippines
  var defaultCenter = [12.8797, 121.7740];
  var defaultZoom = 6;

  var map = L.map('map', { zoomControl: true }).setView(defaultCenter, defaultZoom);

  // Define tile layers
  var regularMapLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  var satelliteMapLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 19,
    attribution: '&copy; Esri, DigitalGlobe, Earthstar Geographics'
  });

  // Add layer control
  var baseLayers = {
    'Map': regularMapLayer,
    'Satellite': satelliteMapLayer
  };
  L.control.layers(baseLayers).addTo(map);

  // User geolocation marker and accuracy circle; store last position for distance calc
  var userMarker = null;
  var userAccuracyCircle = null;
  var userLatLng = null;

  // Haversine distance in meters between two [lat, lng] points
  function distanceMeters(lat1, lng1, lat2, lng2) {
    var R = 6371000;
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLng = (lng2 - lng1) * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng/2) * Math.sin(dLng/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  function formatDistance(meters) {
    if (meters >= 1000) return (meters / 1000).toFixed(1) + ' km away';
    return Math.round(meters) + ' m away';
  }

  function setDetailRow(spanId, value) {
    var span = document.getElementById(spanId);
    var wrap = document.getElementById(spanId + '-wrap');
    if (!span) return;
    span.textContent = (value != null && String(value).trim() !== '') ? String(value).trim() : '—';
    if (wrap) wrap.style.display = '';
  }

  function locateUser(){
    if (!navigator.geolocation){
      alert('Geolocation is not supported by your browser.');
      return;
    }
    navigator.geolocation.getCurrentPosition(function(pos){
      var lat = pos.coords.latitude;
      var lng = pos.coords.longitude;
      var accuracy = Math.max(pos.coords.accuracy || 30, 10);

      if (userMarker) { map.removeLayer(userMarker); }
      if (userAccuracyCircle) { map.removeLayer(userAccuracyCircle); }

      userLatLng = { lat: lat, lng: lng };
      var blueIcon = L.divIcon({
        className: 'user-location-marker',
        html: '<div style="width:20px;height:20px;background:#1976d2;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>',
        iconSize: [26, 26],
        iconAnchor: [13, 13]
      });
      userMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(map).bindPopup('You are here').openPopup();
      userAccuracyCircle = L.circle([lat, lng], { radius: accuracy, color: '#1976d2', fillColor: '#1976d2', fillOpacity: 0.15, weight: 2 }).addTo(map);
      map.setView([lat, lng], 15);

      updateDistancesOnCards();
    }, function(err){
      if (err.code === err.PERMISSION_DENIED) {
        alert('Location permission denied. Allow location access to use Locate me.');
      } else if (err.code === err.POSITION_UNAVAILABLE) {
        alert('Location unavailable. Check your device settings and try again.');
      } else {
        alert('Unable to get your location. Try again.');
      }
    }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
  }

  function updateDistancesOnCards() {
    if (!userLatLng) return;
    var cards = document.querySelectorAll('#place-cards .card');
    var cardsData = window.__placeCardsData;
    if (!cardsData) return;
    cards.forEach(function(card, idx) {
      var p = cardsData[idx];
      if (!p || p.lat == null || p.lng == null) return;
      var dist = distanceMeters(userLatLng.lat, userLatLng.lng, p.lat, p.lng);
      var distEl = card.querySelector('.distance-from-user');
      if (distEl) distEl.textContent = formatDistance(dist);
      else {
        var titleWrap = card.querySelector('.card-title');
        if (titleWrap) {
          var span = document.createElement('span');
          span.className = 'distance-from-user text-primary small ms-2';
          span.textContent = formatDistance(dist);
          titleWrap.appendChild(span);
        }
      }
    });
  }

  function fetchWeatherForModal(lat, lon) {
    var wrap = document.getElementById('detail-weather-wrap');
    var loadingEl = document.getElementById('detail-weather-loading');
    var errorEl = document.getElementById('detail-weather-error');
    var tempEl = document.getElementById('detail-weather-temp');
    var descEl = document.getElementById('detail-weather-desc');
    var metaEl = document.getElementById('detail-weather-meta');
    var iconImg = document.getElementById('detail-weather-icon');
    var iconEmoji = document.getElementById('detail-weather-emoji');
    if (!wrap || !tempEl) return;
    loadingEl.classList.remove('d-none');
    if (errorEl) { errorEl.classList.add('d-none'); errorEl.textContent = ''; }
    tempEl.textContent = '—';
    descEl.textContent = '—';
    if (metaEl) metaEl.textContent = '—';
    iconImg.classList.add('d-none');
    iconEmoji.classList.add('d-none');
    fetch('/api/weather?lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lon))
      .then(function(res) { return res.json(); })
      .then(function(data) {
        loadingEl.classList.add('d-none');
        if (data.error) {
          if (errorEl) { errorEl.textContent = data.error || 'Could not load weather'; errorEl.classList.remove('d-none'); }
          return;
        }
        tempEl.textContent = Math.round(data.temp) + '°C';
        descEl.textContent = data.description ? (data.description.charAt(0).toUpperCase() + data.description.slice(1)) : '—';
        var meta = [];
        if (data.feels_like != null) meta.push('Feels like ' + Math.round(data.feels_like) + '°C');
        if (data.humidity != null) meta.push(data.humidity + '% humidity');
        if (data.wind_speed != null) meta.push(data.wind_speed + ' m/s wind');
        if (metaEl) metaEl.textContent = meta.length ? meta.join(' · ') : '—';
        if (data.icon) {
          iconImg.src = 'https://openweathermap.org/img/wn/' + data.icon + '@2x.png';
          iconImg.alt = data.description || 'Weather';
          iconImg.classList.remove('d-none');
        } else {
          iconEmoji.textContent = '☀️';
          iconEmoji.classList.remove('d-none');
        }
      })
      .catch(function() {
        loadingEl.classList.add('d-none');
        if (errorEl) { errorEl.textContent = 'Could not load weather'; errorEl.classList.remove('d-none'); }
      });
  }


  // Locate button: attach immediately so it works even with no locations
  var locateBtn = document.getElementById('locate-me');
  if (locateBtn) {
    locateBtn.addEventListener('click', function(e){
      e.preventDefault();
      if (window.authRequiredForLocate && window.loginUrl) {
        window.location.href = window.loginUrl;
        return;
      }
      locateUser();
    });
  }

  // Fetch saved locations from API and render markers
  fetch('/api/locations')
    .then(function(res){ return res.json(); })
    .then(function(locations){
      locations = locations || [];
      window.__placeCardsData = locations;

      var markers = [];
      locations.forEach(function(p){
        var marker = L.marker([p.lat, p.lng]).addTo(map);
        var popupHtml = '<div style="display:flex;align-items:center">'
          + (p.image? '<img src="'+p.image+'" style="width:120px;height:80px;object-fit:cover;border-radius:4px;margin-right:8px">' : '')
          + '<div><div class="title">'+(p.name||'Untitled')+'</div><div class="small text-muted">'+(p.address||'')+'</div></div></div>';
        marker.bindPopup(popupHtml);
        markers.push(marker);
      });

      // Populate the left-side cards with the admin locations
      var cardsContainer = document.getElementById('place-cards');
      if (cardsContainer) {
        cardsContainer.innerHTML = '';
        if (locations.length === 0) {
          var empty = document.createElement('div');
          empty.className = 'landing-empty-state';
          empty.textContent = 'No places to show yet. Check back later or try adjusting filters.';
          cardsContainer.appendChild(empty);
        }
        locations.forEach(function(p, idx){
          var imgHtml = p.image ? '<img src="'+p.image+'" class="card-img-top card-photo"/>' : '<div class="card-photo" style="background:linear-gradient(135deg,#e0e7ff 0%,#c7d2fe 100%)"></div>';
          var ratingStars = '';
          if (p.rating !== null && p.rating !== undefined) {
            var r = Math.max(0, Math.min(5, parseInt(p.rating)));
            for (var i=0;i<r;i++) ratingStars += '★';
            for (var i=r;i<5;i++) ratingStars += '☆';
          }

          var card = document.createElement('div');
          card.className = 'card mb-3';
          card.style.cursor = 'pointer';
          card.innerHTML = '<div>'+imgHtml+'</div>'
            + '<div class="card-body">'
            + '<h5 class="card-title">'+(p.name||'Untitled')+'</h5>'
            + (ratingStars? '<div class="rating mb-2">'+ratingStars+' <span class="text-muted ms-2">'+(p.reviews||'')+'</span></div>' : '')
            + '<p class="card-text text-muted small">'+(p.description||p.address||'')+'</p>'
            + '<div class="d-flex justify-content-between align-items-center">'
            + '<div>'
            + '<button class="btn btn-sm btn-outline-primary btn-view-details">View details</button>'
            + '</div>'
            + '<div>'
            + '<button class="btn btn-sm btn-outline-secondary btn-focus-map">Show on map</button>'
            + '</div>'
            + '</div>'
            + '</div>';

          (function(i){
            card.querySelector('.btn-focus-map').addEventListener('click', function(e){
              e.stopPropagation();
              var m = markers[i];
              if (m) { map.setView(m.getLatLng(), 14); m.openPopup(); }
            });

            card.querySelector('.btn-view-details').addEventListener('click', function(e){
              e.stopPropagation();
              var data = locations[i];
              var img = data.image || '';
              document.getElementById('detail-image').src = img;
              document.getElementById('detail-title').textContent = data.name || '';
              document.getElementById('detail-description').textContent = data.description || '';
              setDetailRow('detail-location', data.address);
              setDetailRow('detail-fees', data.fees);
              setDetailRow('detail-facilities', data.facilities);
              setDetailRow('detail-cottage', data.cottage);
              var ratingEl = document.getElementById('detail-rating');
              ratingEl.textContent = '';
              if (data.rating !== null && data.rating !== undefined) {
                var r = Math.max(0, Math.min(5, parseInt(data.rating)));
                var s = '';
                for (var k=0;k<r;k++) s += '★';
                for (var k=r;k<5;k++) s += '☆';
                ratingEl.textContent = s;
              }
              var distEl = document.getElementById('detail-distance');
              if (distEl && userLatLng && data.lat != null && data.lng != null) {
                var d = distanceMeters(userLatLng.lat, userLatLng.lng, data.lat, data.lng);
                distEl.textContent = formatDistance(d);
                distEl.parentElement.style.display = '';
              } else if (distEl) {
                distEl.parentElement.style.display = 'none';
              }

              // Handle Google Maps embed
              var mapWrap = document.getElementById('detail-map-wrap');
              var mapEmbed = document.getElementById('detail-map-embed');
              if (data.maps_embed_url && data.maps_embed_url.trim() !== '') {
                mapWrap.style.display = '';
                mapEmbed.innerHTML = data.maps_embed_url;
              } else {
                mapWrap.style.display = 'none';
                mapEmbed.innerHTML = '';
              }

              if (data.lat != null && data.lng != null) {
                fetchWeatherForModal(data.lat, data.lng);
              }

              var modalEl = document.getElementById('detailModal');
              var modal = new bootstrap.Modal(modalEl);
              modal.show();
            });
          })(idx);

          cardsContainer.appendChild(card);
        });
        updateDistancesOnCards();
      }

      if (locations.length) {
        var phil = locations.find(function(l){ return l.lat >= 4 && l.lat <= 21 && l.lng >= 115 && l.lng <= 130; });
        if (phil) {
          map.setView([phil.lat, phil.lng], 13);
        } else {
          var group = new L.featureGroup(markers);
          map.fitBounds(group.getBounds().pad(0.2));
        }
      }

      // Setup search functionality after locations are loaded
      var searchInput = document.querySelector('.search-box input');
      if (searchInput) {
        searchInput.addEventListener('input', function(e){
          var searchQuery = e.target.value.toLowerCase().trim();
          var cards = document.querySelectorAll('#place-cards .card');
          var cardsData = window.__placeCardsData;
          
          if (!cardsData || !cardsData.length) return;
          
          var firstMatchIndex = -1;
          var firstMatchLocation = null;
          
          cards.forEach(function(card, idx){
            var location = cardsData[idx];
            if (!location) return;
            
            var matchesSearch = (
              location.name.toLowerCase().includes(searchQuery) ||
              (location.address && location.address.toLowerCase().includes(searchQuery)) ||
              (location.description && location.description.toLowerCase().includes(searchQuery))
            );
            
            card.style.display = matchesSearch || searchQuery === '' ? '' : 'none';
            
            // Track the first match for zoom
            if (matchesSearch && firstMatchIndex === -1) {
              firstMatchIndex = idx;
              firstMatchLocation = location;
            }
          });
          
          // Zoom to first match if search is not empty
          if (searchQuery !== '' && firstMatchLocation && firstMatchLocation.lat != null && firstMatchLocation.lng != null) {
            map.setView([firstMatchLocation.lat, firstMatchLocation.lng], 14);
            
            // Open the marker popup for the matching location
            if (firstMatchIndex >= 0 && markers[firstMatchIndex]) {
              markers[firstMatchIndex].openPopup();
            }
            
            // Scroll to the first matching card
            if (firstMatchIndex >= 0) {
              var firstCard = cards[firstMatchIndex];
              if (firstCard) {
                firstCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
              }
            }
          } else if (searchQuery === '') {
            // Close all popups when search is cleared
            markers.forEach(function(marker){
              marker.closePopup();
            });
          }
          
          // Show/hide empty state if no results
          var visibleCards = Array.from(cards).filter(function(card){ return card.style.display !== 'none'; });
          var cardsContainer = document.getElementById('place-cards');
          var emptyState = cardsContainer.querySelector('.landing-empty-state');
          
          if (visibleCards.length === 0 && searchQuery !== '') {
            if (!emptyState) {
              var empty = document.createElement('div');
              empty.className = 'landing-empty-state';
              empty.textContent = 'No places match your search.';
              empty.id = 'search-empty-state';
              cardsContainer.appendChild(empty);
            }
          } else if (emptyState && emptyState.id === 'search-empty-state') {
            emptyState.remove();
          }
        });
      }
    })
    .catch(function(err){
      console.error('Failed to load locations:', err);
      window.__placeCardsData = [];
    });
});
