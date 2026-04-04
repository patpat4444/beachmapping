document.addEventListener('DOMContentLoaded', function () {
  // Default center: Binongkalan area (focused on the 5 beach resorts)
  var defaultCenter = [10.634, 124.028];
  var defaultZoom = 16;

  var __lastWeather = null;
  var __lastWeatherCoords = { lat: defaultCenter[0], lon: defaultCenter[1] };
  var __weatherIntervalId = null;

  // Remove theme-related code - always use light mode

  // No bounds restriction - allow free navigation
  var map = L.map('map', { 
    zoomControl: false,
    minZoom: 9,
    maxZoom: 19
  }).setView(defaultCenter, defaultZoom);
  
  // Add zoom control to bottom left (above the layer control)
  L.control.zoom({ position: 'bottomleft' }).addTo(map);

  // Define tile layers - CartoDB Light and Dark for auto theme switching
  var cartoLightLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
  });
  
  var cartoDarkLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/dark_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
  });

  var satelliteMapLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 17,
    attribution: 'Tiles © Esri'
  });
  
  // Auto-switch map tiles based on application's data-theme attribute
  var currentBaseLayer = cartoLightLayer;
  var htmlElement = document.documentElement;
  var isDarkMode = htmlElement.getAttribute('data-theme') === 'dark';
  
  if (isDarkMode) {
    cartoDarkLayer.addTo(map);
    currentBaseLayer = cartoDarkLayer;
  } else {
    cartoLightLayer.addTo(map);
  }
  
  // Listen for data-theme changes using MutationObserver
  var observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === 'data-theme') {
        var newTheme = htmlElement.getAttribute('data-theme');
        if (newTheme === 'dark') {
          map.removeLayer(cartoLightLayer);
          cartoDarkLayer.addTo(map);
          currentBaseLayer = cartoDarkLayer;
        } else {
          map.removeLayer(cartoDarkLayer);
          cartoLightLayer.addTo(map);
          currentBaseLayer = cartoLightLayer;
        }
      }
    });
  });
  observer.observe(htmlElement, { attributes: true });

  // Add layer control - 2D View (auto light/dark), Satellite
  var baseLayers = {
    '2D View': currentBaseLayer,
    'Satellite': satelliteMapLayer
  };
  L.control.layers(baseLayers, null, { position: 'bottomleft' }).addTo(map);

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

      // Refresh the right-side weather widget using your current location
      fetchWeatherForWidget(lat, lng);
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
    var cards = document.querySelectorAll('#place-cards .beach-card');
    var cardsData = window.__placeCardsData;
    if (!cardsData) return;
    cards.forEach(function(card, idx) {
      var p = cardsData[idx];
      if (!p || p.lat == null || p.lng == null) return;
      var dist = distanceMeters(userLatLng.lat, userLatLng.lng, p.lat, p.lng);
      var distEl = card.querySelector('.distance');
      if (distEl) {
        distEl.innerHTML = '<i class="fa-solid fa-location-dot"></i> ' + formatDistance(dist);
      }
    });
  }

  function fetchWeatherForModal(lat, lon, locationName) {
    var wrap = document.getElementById('detail-weather-wrap');
    var loadingEl = document.getElementById('detail-weather-loading');
    var errorEl = document.getElementById('detail-weather-error');
    var tempEl = document.getElementById('detail-weather-temp');
    var descEl = document.getElementById('detail-weather-desc');
    var locEl = document.getElementById('detail-weather-location');
    var wavesEl = document.getElementById('detail-weather-waves');
    var windEl = document.getElementById('detail-weather-wind');
    var humEl = document.getElementById('detail-weather-humidity');
    var uvEl = document.getElementById('detail-weather-uv');
    var emojiEl = document.getElementById('detail-weather-emoji');
    var marineTideSection = document.getElementById('detail-marine-tide');
    var marineWavesEl = document.getElementById('detail-marine-waves');
    var tideHighEl = document.getElementById('detail-tide-next-high');
    
    if (!wrap || !tempEl) return;
    
    // Reset UI
    loadingEl.classList.remove('d-none');
    if (errorEl) { errorEl.classList.add('d-none'); errorEl.textContent = ''; }
    tempEl.textContent = '—';
    descEl.textContent = '—';
    if (locEl) locEl.textContent = locationName || '—';
    if (wavesEl) wavesEl.textContent = '—';
    if (windEl) windEl.textContent = '—';
    if (humEl) humEl.textContent = '—';
    if (uvEl) uvEl.textContent = '—';
    if (marineTideSection) marineTideSection.classList.add('d-none');
    
    // Fetch comprehensive weather data
    fetch('/api/weather/comprehensive?lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lon))
      .then(function(res) { 
        if (!res.ok) throw new Error('API error: ' + res.status);
        return res.json(); 
      })
      .then(function(data) {
        loadingEl.classList.add('d-none');
        
        var current = data.current || {};
        var marine = data.marine || {};
        var tides = data.tides || null;
        
        // Update main weather display
        if (tempEl) tempEl.textContent = (current.temp != null) ? (Math.round(current.temp) + '°C') : '—°C';
        if (descEl) descEl.textContent = current.description ? (current.description.charAt(0).toUpperCase() + current.description.slice(1)) : '—';
        if (locEl) locEl.textContent = locationName || data.location?.name || '—';
        if (wavesEl) wavesEl.textContent = (marine.wave_height != null) ? (marine.wave_height.toFixed(1) + 'm') : '—';
        if (windEl) windEl.textContent = (current.wind_speed != null) ? (current.wind_speed + ' m/s') : '—';
        if (humEl) humEl.textContent = (current.humidity != null) ? (current.humidity + '%') : '—';
        if (uvEl) uvEl.textContent = (current.uv_index != null) ? String(current.uv_index) : '—';
        
        // Update weather emoji
        if (emojiEl) {
          var icon = 'fa-cloud-sun';
          var color = '#7ecce0';
          if (current.description) {
            var d = current.description.toLowerCase();
            if (d.includes('rain')) { icon = 'fa-cloud-rain'; color = '#7ecce0'; }
            else if (d.includes('cloud')) { icon = 'fa-cloud'; color = '#7ecce0'; }
            else if (d.includes('clear')) { icon = 'fa-sun'; color = '#f4a460'; }
            else if (d.includes('storm') || d.includes('thunder')) { icon = 'fa-bolt'; color = '#ffd700'; }
          }
          emojiEl.innerHTML = '<i class="fa-solid ' + icon + '" style="color: ' + color + ';"></i>';
        }
        
        // Update marine & tide section if data available
        if (marineTideSection && (marine.wave_height != null || tides)) {
          marineTideSection.classList.remove('d-none');
          if (marineWavesEl) marineWavesEl.textContent = (marine.wave_height != null) ? (marine.wave_height.toFixed(1) + ' m') : '—';
          if (tideHighEl) tideHighEl.textContent = tides?.next_high || '—';
          // Update tide marker position
          updateDetailTideMarker();
        }

        // Update forecast if available
        if (data.forecast && data.forecast.length > 0) {
          updateDetailForecastDisplay(data.forecast);
        }
      })
      .catch(function(err) {
        console.error('Weather fetch error:', err);
        loadingEl.classList.add('d-none');
        if (errorEl) { errorEl.textContent = 'Could not load weather'; errorEl.classList.remove('d-none'); }
      });
  }

  function fetchWeatherForWidget(lat, lon) {
    var widget = document.getElementById('weather');
    if (!widget) {
      console.log('Weather widget not found');
      return;
    }

    var tempEl = document.getElementById('weather-temp');
    var descEl = document.getElementById('weather-desc');
    var wavesEl = document.getElementById('weather-waves');
    var windEl = document.getElementById('weather-wind');
    var humEl = document.getElementById('weather-humidity');
    var uvEl = document.getElementById('weather-uv');
    
    console.log('Fetching comprehensive weather for:', lat, lon);

    // Try backend first, then fall back to demo data immediately if it fails
    fetch('/api/weather/comprehensive?lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lon))
      .then(function(res) { 
        console.log('Backend API status:', res.status);
        if (!res.ok) {
          throw new Error('Backend API error: ' + res.status);
        }
        return res.json(); 
      })
      .then(function(data) {
        console.log('Backend weather data:', data);
        
        // Check if we got valid current weather data
        var current = data.current || {};
        if (!current.temp && !data.temp) {
          console.log('No current weather data from backend, using demo...');
          useDemoWeatherData(lat, lon);
          return;
        }

        __lastWeather = data || null;
        __lastWeatherCoords = { lat: lat, lon: lon };

        // Current weather data
        if (tempEl) tempEl.textContent = (current.temp != null) ? (Math.round(current.temp) + '°C') : '—°C';
        if (descEl) descEl.textContent = current.description ? (current.description.charAt(0).toUpperCase() + current.description.slice(1)) : '—';
        if (uvEl) uvEl.textContent = (current.uv_index != null) ? String(current.uv_index) : '—';
        if (humEl) humEl.textContent = (current.humidity != null) ? (current.humidity + '%') : '—';
        if (windEl) windEl.textContent = (current.wind_speed != null) ? (current.wind_speed + ' m/s') : '—';

        // Marine/Wave data from Open-Meteo
        var marine = data.marine || {};
        if (wavesEl) {
          if (marine.wave_height != null) {
            wavesEl.textContent = marine.wave_height.toFixed(1) + 'm';
          } else {
            wavesEl.textContent = '—';
          }
        }

        // Update forecast if available
        if (data.forecast && data.forecast.length > 0) {
          updateForecastDisplay(data.forecast);
        }

        // Store tide info for modal
        __lastWeather.tides = data.tides || null;

        syncWeatherModalFromLast();
      })
      .catch(function(err) {
        console.error('Backend fetch failed:', err);
        console.log('Using demo weather data as fallback...');
        useDemoWeatherData(lat, lon);
      });
  }

  // Demo weather data for Catmon, Cebu when APIs fail
  function useDemoWeatherData(lat, lon) {
    console.log('Loading demo weather data for Catmon...');
    
    var demoData = {
      location: { name: 'Catmon, Cebu', lat: lat, lng: lon },
      current: {
        temp: 31,
        feels_like: 35,
        humidity: 78,
        pressure: 1010,
        description: 'partly cloudy',
        icon: '02d',
        wind_speed: 2.5,
        wind_direction: 120,
        clouds: 45,
        visibility: 10000,
        uv_index: 7
      },
      marine: {
        wave_height: 0.4,
        sea_temp: 28.5,
        wave_direction: 90,
        wave_period: 6
      },
      tides: {
        status: 'rising',
        next_high: '6:00 AM',
        next_low: '12:00 PM',
        note: 'Approximate times based on lunar cycle'
      },
      forecast: [
        { day_name: 'Today', temp_max: 32, temp_min: 26, icon: '02d', description: 'partly cloudy' },
        { day_name: 'Fri', temp_max: 33, temp_min: 27, icon: '01d', description: 'sunny' },
        { day_name: 'Sat', temp_max: 31, temp_min: 25, icon: '10d', description: 'light rain' },
        { day_name: 'Sun', temp_max: 30, temp_min: 25, icon: '02d', description: 'partly cloudy' },
        { day_name: 'Mon', temp_max: 34, temp_min: 27, icon: '01d', description: 'clear sky' },
        { day_name: 'Tue', temp_max: 33, temp_min: 26, icon: '01d', description: 'sunny' },
        { day_name: 'Wed', temp_max: 29, temp_min: 24, icon: '10d', description: 'rain' }
      ],
      updated_at: new Date().toISOString()
    };

    __lastWeather = demoData;
    __lastWeatherCoords = { lat: lat, lon: lon };

    // Update UI
    var tempEl = document.getElementById('weather-temp');
    var descEl = document.getElementById('weather-desc');
    var wavesEl = document.getElementById('weather-waves');
    var windEl = document.getElementById('weather-wind');
    var humEl = document.getElementById('weather-humidity');
    var uvEl = document.getElementById('weather-uv');

    if (tempEl) tempEl.textContent = '31°C';
    if (descEl) descEl.textContent = 'Partly Cloudy';
    if (wavesEl) wavesEl.textContent = '0.4m';
    if (uvEl) uvEl.textContent = '7';
    if (humEl) humEl.textContent = '78%';
    if (windEl) windEl.textContent = '2.5 m/s';

    // Update forecast
    updateForecastDisplay(demoData.forecast);

    // Update modal
    syncWeatherModalFromLast();

    console.log('Demo weather data loaded successfully!');
  }

  // Direct Open-Meteo API call (FREE, no API key needed!)
  function fetchOpenMeteoDirectly(lat, lon) {
    console.log('Fetching from Open-Meteo directly...');
    
    // Current weather + marine
    var urls = [
      'https://api.open-meteo.com/v1/forecast?latitude=' + lat + '&longitude=' + lon + '&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,wind_direction_10m,pressure_msl,cloud_cover,uv_index&timezone=auto',
      'https://marine-api.open-meteo.com/v1/marine?latitude=' + lat + '&longitude=' + lon + '&current=wave_height,sea_surface_temperature,wave_direction,wave_period&timezone=auto'
    ];

    Promise.all(urls.map(function(url) {
      return fetch(url).then(function(r) { return r.json(); });
    })).then(function(results) {
      var weather = results[0];
      var marine = results[1];

      if (weather.current) {
        var current = weather.current;
        var weatherCodes = {
          0: 'clear sky', 1: 'mainly clear', 2: 'partly cloudy', 3: 'overcast',
          45: 'fog', 48: 'fog',
          51: 'light drizzle', 53: 'moderate drizzle', 55: 'dense drizzle',
          61: 'slight rain', 63: 'moderate rain', 65: 'heavy rain',
          80: 'rain showers', 81: 'moderate showers', 82: 'violent showers'
        };

        var data = {
          location: { name: 'Catmon, Cebu', lat: lat, lng: lon },
          current: {
            temp: current.temperature_2m,
            feels_like: current.apparent_temperature,
            humidity: current.relative_humidity_2m,
            pressure: current.pressure_msl,
            description: weatherCodes[current.weather_code] || 'partly cloudy',
            wind_speed: current.wind_speed_10m,
            wind_direction: current.wind_direction_10m,
            clouds: current.cloud_cover,
            uv_index: current.uv_index
          },
          marine: marine.current ? {
            wave_height: marine.current.wave_height,
            sea_temp: marine.current.sea_surface_temperature,
            wave_direction: marine.current.wave_direction,
            wave_period: marine.current.wave_period
          } : null,
          updated_at: new Date().toISOString()
        };

        __lastWeather = data;
        updateWeatherUI(data);
      }
    }).catch(function(err) {
      console.error('Open-Meteo failed:', err);
      useDemoWeatherData(lat, lon);
    });
  }

  // Update tide graph marker position based on current time
  function updateTideGraphMarker() {
    var marker = document.getElementById('tide-marker');
    if (!marker) return;
    
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var totalMinutes = hours * 60 + minutes;
    
    // Convert to percentage of 24 hours (0-100%)
    var percentage = (totalMinutes / 1440) * 100;
    marker.style.left = percentage + '%';
  }

  // Update detail modal tide marker
  function updateDetailTideMarker() {
    var marker = document.getElementById('detail-tide-marker');
    if (!marker) return;
    
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var totalMinutes = hours * 60 + minutes;
    
    // Convert to percentage of 24 hours (0-100%)
    var percentage = (totalMinutes / 1440) * 100;
    marker.style.left = percentage + '%';
  }

  // Update UI with weather data
  function updateWeatherUI(data) {
    var current = data.current || {};
    var marine = data.marine || {};
    var tides = data.tides || null;

    var tempEl = document.getElementById('weather-temp');
    var descEl = document.getElementById('weather-desc');
    var wavesEl = document.getElementById('weather-waves');
    var windEl = document.getElementById('weather-wind');
    var humEl = document.getElementById('weather-humidity');
    var uvEl = document.getElementById('weather-uv');

    if (tempEl) tempEl.textContent = (current.temp != null) ? (Math.round(current.temp) + '°C') : '—°C';
    if (descEl) descEl.textContent = current.description ? (current.description.charAt(0).toUpperCase() + current.description.slice(1)) : '—';
    if (uvEl) uvEl.textContent = (current.uv_index != null) ? String(current.uv_index) : '—';
    if (humEl) humEl.textContent = (current.humidity != null) ? (current.humidity + '%') : '—';
    if (windEl) windEl.textContent = (current.wind_speed != null) ? (current.wind_speed + ' m/s') : '—';

    if (wavesEl) {
      if (marine.wave_height != null) {
        wavesEl.textContent = marine.wave_height.toFixed(1) + 'm';
      } else {
        wavesEl.textContent = '—';
      }
    }

    // Update tide info in modal if available
    if (tides) {
      var tideSection = document.getElementById('tide-info-section');
      var tideStatus = document.getElementById('tide-current-status');
      var tideNextHigh = document.getElementById('tide-next-high');
      var tideNextLow = document.getElementById('tide-next-low');
      var tideNote = document.getElementById('tide-note');
      
      if (tideSection) tideSection.classList.remove('d-none');
      if (tideStatus) tideStatus.textContent = tides.status || '—';
      if (tideNextHigh) tideNextHigh.textContent = tides.next_high || '—';
      if (tideNextLow) tideNextLow.textContent = tides.next_low || '—';
      if (tideNote) {
        tideNote.textContent = tides.note || '';
        tideNote.classList.toggle('d-none', !tides.note);
      }
      
      // Update marker position
      updateTideGraphMarker();
    }

    // Update marine info in modal if available
    if (marine.wave_height != null) {
      var marineSection = document.getElementById('marine-info-section');
      var waveHeight = document.getElementById('marine-wave-height');
      var seaTemp = document.getElementById('marine-sea-temp');
      var wavePeriod = document.getElementById('marine-wave-period');
      
      if (marineSection) marineSection.classList.remove('d-none');
      if (waveHeight) waveHeight.textContent = marine.wave_height.toFixed(1) + ' m';
      if (seaTemp) seaTemp.textContent = marine.sea_temp ? marine.sea_temp.toFixed(1) + '°C' : '—';
      if (wavePeriod) wavePeriod.textContent = marine.wave_period ? marine.wave_period + 's' : '—';
    }

    syncWeatherModalFromLast();
  }
  function fetchWeatherForWidgetLegacy(lat, lon) {
    fetch('/api/weather?lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lon))
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data && data.error) return;
        __lastWeather = data || null;
        __lastWeatherCoords = { lat: lat, lon: lon };
        
        var tempEl = document.getElementById('weather-temp');
        var descEl = document.getElementById('weather-desc');
        var wavesEl = document.getElementById('weather-waves');
        var windEl = document.getElementById('weather-wind');
        var humEl = document.getElementById('weather-humidity');
        var uvEl = document.getElementById('weather-uv');

        if (tempEl) tempEl.textContent = (data.temp != null) ? (Math.round(data.temp) + '°C') : '—°C';
        if (descEl) descEl.textContent = data.description ? (data.description.charAt(0).toUpperCase() + data.description.slice(1)) : '—';
        if (wavesEl) wavesEl.textContent = data.wave_height ? (data.wave_height + 'm') : '—';
        if (uvEl) uvEl.textContent = (data.uv_index != null) ? String(data.uv_index) : '—';
        if (humEl) humEl.textContent = (data.humidity != null) ? (data.humidity + '%') : '—';
        if (windEl) windEl.textContent = (data.wind_speed != null) ? (data.wind_speed + ' m/s') : '—';
        
        syncWeatherModalFromLast();
      })
      .catch(function(err) {
        console.error('Legacy weather fetch error:', err);
      });
  }

  // Update 7-day forecast display
  function updateForecastDisplay(forecast) {
    var forecastList = document.querySelector('.forecast-list');
    if (!forecastList || !forecast || forecast.length === 0) return;

    // Clear existing forecast
    forecastList.innerHTML = '';

    // Map weather icons
    var iconMap = {
      '01d': 'fa-sun', '01n': 'fa-moon',
      '02d': 'fa-cloud-sun', '02n': 'fa-cloud-moon',
      '03d': 'fa-cloud', '03n': 'fa-cloud',
      '04d': 'fa-cloud', '04n': 'fa-cloud',
      '09d': 'fa-cloud-showers-heavy', '09n': 'fa-cloud-showers-heavy',
      '10d': 'fa-cloud-rain', '10n': 'fa-cloud-rain',
      '11d': 'fa-bolt', '11n': 'fa-bolt',
      '13d': 'fa-snowflake', '13n': 'fa-snowflake',
      '50d': 'fa-smog', '50n': 'fa-smog'
    };

    forecast.forEach(function(day) {
      var icon = iconMap[day.icon] || 'fa-cloud-sun';
      var item = document.createElement('div');
      item.className = 'forecast-item';
      item.innerHTML = 
        '<span class="day">' + day.day_name + '</span>' +
        '<i class="fa-solid ' + icon + ' forecast-icon"></i>' +
        '<span class="temps">' + day.temp_max + '° / ' + day.temp_min + '°</span>';
      forecastList.appendChild(item);
    });

    // Also update detail modal forecast
    updateDetailForecastDisplay(forecast);
  }

  // Update forecast in detail modal
  function updateDetailForecastDisplay(forecast) {
    var detailForecastList = document.getElementById('detail-forecast-list');
    if (!detailForecastList || !forecast || forecast.length === 0) return;

    // Clear existing forecast
    detailForecastList.innerHTML = '';

    // Map weather icons
    var iconMap = {
      '01d': 'fa-sun', '01n': 'fa-moon',
      '02d': 'fa-cloud-sun', '02n': 'fa-cloud-moon',
      '03d': 'fa-cloud', '03n': 'fa-cloud',
      '04d': 'fa-cloud', '04n': 'fa-cloud',
      '09d': 'fa-cloud-showers-heavy', '09n': 'fa-cloud-showers-heavy',
      '10d': 'fa-cloud-rain', '10n': 'fa-cloud-rain',
      '11d': 'fa-bolt', '11n': 'fa-bolt',
      '13d': 'fa-snowflake', '13n': 'fa-snowflake',
      '50d': 'fa-smog', '50n': 'fa-smog'
    };

    // Show only first 5 days
    var forecastDays = forecast.slice(0, 5);
    
    forecastDays.forEach(function(day) {
      var icon = iconMap[day.icon] || 'fa-cloud-sun';
      var item = document.createElement('div');
      item.className = 'detail-forecast-item';
      item.innerHTML = 
        '<div class="detail-forecast-day">' + day.day_name + '</div>' +
        '<i class="fa-solid ' + icon + ' detail-forecast-icon"></i>' +
        '<div class="detail-forecast-temps"><span class="detail-forecast-temps-max">' + Math.round(day.temp_max) + '°</span> / ' + Math.round(day.temp_min) + '°</div>';
      detailForecastList.appendChild(item);
    });
  }

  function syncWeatherModalFromLast() {
    var modalEl = document.getElementById('weatherModal');
    if (!modalEl) return;

    var data = __lastWeather;
    if (!data) return;

    // Handle both old format (flat) and new format (nested)
    var current = data.current || data;
    var marine = data.marine || {};
    var tides = data.tides || null;

    // Main weather display
    var tempEl = document.getElementById('weather-temp-more');
    var descEl = document.getElementById('weather-desc-more');
    var locEl = document.getElementById('weather-location-more');
    var emojiEl = document.getElementById('weather-emoji-more');

    if (tempEl) tempEl.textContent = (current.temp != null) ? (Math.round(current.temp) + '°C') : '—°C';
    if (descEl) descEl.textContent = current.description ? (current.description.charAt(0).toUpperCase() + current.description.slice(1)) : '—';
    if (locEl) locEl.textContent = data.location?.name || current.name || 'Catmon, Cebu';

    // Weather emoji
    if (emojiEl) {
      emojiEl.innerHTML = '<i class="fa-solid fa-cloud-sun" style="color: #7ecce0; font-size: 2rem;"></i>';
      if (current.description) {
        var d = current.description.toLowerCase();
        if (d.includes('rain')) emojiEl.innerHTML = '<i class="fa-solid fa-cloud-rain" style="color: #7ecce0; font-size: 2rem;"></i>';
        else if (d.includes('cloud')) emojiEl.innerHTML = '<i class="fa-solid fa-cloud" style="color: #7ecce0; font-size: 2rem;"></i>';
        else if (d.includes('clear')) emojiEl.innerHTML = '<i class="fa-solid fa-sun" style="color: #f4a460; font-size: 2rem;"></i>';
        else if (d.includes('storm') || d.includes('thunder')) emojiEl.innerHTML = '<i class="fa-solid fa-bolt" style="color: #ffd700; font-size: 2rem;"></i>';
      }
    }

    // Update metric boxes
    var metricMap = {
      'weather-feels-more': current.feels_like ? Math.round(current.feels_like) + '°C' : '—',
      'weather-humidity-more': current.humidity ? current.humidity + '%' : '—',
      'weather-wind-more': current.wind_speed ? current.wind_speed + ' m/s' : '—',
      'weather-uv-more': current.uv_index != null ? current.uv_index : '—',
      'weather-pressure-more': current.pressure ? current.pressure + ' hPa' : '—',
      'weather-visibility-more': current.visibility ? (current.visibility / 1000).toFixed(1) + ' km' : '—',
      'weather-clouds-more': current.clouds != null ? current.clouds + '%' : '—',
      'weather-updated-more': data.updated_at ? new Date(data.updated_at).toLocaleTimeString() : new Date().toLocaleTimeString()
    };

    Object.keys(metricMap).forEach(function(id) {
      var el = document.getElementById(id);
      if (el) el.textContent = metricMap[id];
    });

    // Update tide info section
    var tideSection = document.getElementById('tide-info-section');
    if (tideSection && tides) {
      tideSection.classList.remove('d-none');
      
      var tideStatus = document.getElementById('tide-current-status');
      var tideNextHigh = document.getElementById('tide-next-high');
      var tideNextLow = document.getElementById('tide-next-low');
      var tideNote = document.getElementById('tide-note');
      
      if (tideStatus) tideStatus.textContent = tides.status || '—';
      if (tideNextHigh) tideNextHigh.textContent = tides.next_high || '—';
      if (tideNextLow) tideNextLow.textContent = tides.next_low || '—';
      if (tideNote) {
        tideNote.textContent = tides.note || '';
        tideNote.classList.toggle('d-none', !tides.note);
      }
      
      // Update marker position
      updateTideGraphMarker();
    }

    // Update marine info section
    var marineSection = document.getElementById('marine-info-section');
    if (marineSection && marine.wave_height != null) {
      marineSection.classList.remove('d-none');
      
      var waveHeight = document.getElementById('marine-wave-height');
      var seaTemp = document.getElementById('marine-sea-temp');
      var wavePeriod = document.getElementById('marine-wave-period');
      
      if (waveHeight) waveHeight.textContent = marine.wave_height.toFixed(1) + ' m';
      if (seaTemp) seaTemp.textContent = marine.sea_temp ? marine.sea_temp.toFixed(1) + '°C' : '—';
      if (wavePeriod) wavePeriod.textContent = marine.wave_period ? marine.wave_period + 's' : '—';
    }

    // Hide loading
    var loadingEl = document.getElementById('weather-loading-more');
    if (loadingEl) loadingEl.style.display = 'none';
  }

  // Generate monthly tide calendar
  function generateMonthlyTides() {
    var container = document.getElementById('monthly-tides-content');
    if (!container) return;

    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
    var now = new Date();
    var currentMonth = monthNames[now.getMonth()];
    var daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
    
    var html = '<div style="margin-bottom: 1rem;">';
    html += '<h6 style="color: var(--page-text); margin-bottom: 1rem;">' + currentMonth + ' ' + now.getFullYear() + ' - Estimated Tide Times</h6>';
    html += '<div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem; font-size: 0.75rem;">';
    
    // Header
    var weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    weekdays.forEach(function(day) {
      html += '<div style="text-align: center; font-weight: 600; color: var(--muted-text); padding: 0.5rem;">' + day + '</div>';
    });
    
    // Calculate approximate tides for each day
    var firstDay = new Date(now.getFullYear(), now.getMonth(), 1).getDay();
    
    // Empty cells for days before month starts
    for (var i = 0; i < firstDay; i++) {
      html += '<div style="padding: 0.5rem;"></div>';
    }
    
    // Days of month
    for (var day = 1; day <= daysInMonth; day++) {
      var isToday = day === now.getDate();
      var baseHigh1 = 6; // 6 AM
      var baseHigh2 = 18; // 6 PM
      var baseLow1 = 12; // 12 PM
      var baseLow2 = 0; // 12 AM
      
      // Adjust by moon phase (rough approximation)
      var moonOffset = ((day % 14) / 14) * 2; // shifts by up to 2 hours
      
      var high1 = Math.floor(baseHigh1 + moonOffset);
      var high2 = Math.floor(baseHigh2 + moonOffset);
      var low1 = Math.floor(baseLow1 + moonOffset);
      var low2 = Math.floor(baseLow2 + moonOffset);
      
      var bgStyle = isToday ? 'background: rgba(126,204,224,0.3); border: 1px solid #7ecce0;' : 'background: var(--glass-bg); border: 1px solid var(--glass-border);';
      
      html += '<div style="' + bgStyle + ' border-radius: 8px; padding: 0.5rem; text-align: center; min-height: 80px;">';
      html += '<div style="font-weight: 700; color: var(--page-text); margin-bottom: 0.25rem;">' + day + '</div>';
      html += '<div style="font-size: 0.625rem; color: #22c55e;">↑' + formatTideTime(high1) + '</div>';
      html += '<div style="font-size: 0.625rem; color: #ef4444;">↓' + formatTideTime(low1) + '</div>';
      html += '</div>';
    }
    
    html += '</div>';
    html += '<div style="margin-top: 1rem; padding: 1rem; background: var(--glass-bg-2); border-radius: 8px; font-size: 0.75rem; color: var(--muted-text);">';
    html += '<div style="margin-bottom: 0.5rem;"><span style="color: #22c55e;">↑</span> = High Tide</div>';
    html += '<div><span style="color: #ef4444;">↓</span> = Low Tide</div>';
    html += '<div style="margin-top: 0.5rem; font-style: italic;">These are approximate times based on lunar cycle calculations. For precise tide data, please consult local tide tables.</div>';
    html += '</div>';
    html += '</div>';
    
    container.innerHTML = html;
  }

  // Format tide time
  function formatTideTime(hour) {
    var h = Math.floor(hour) % 24;
    var ampm = h >= 12 ? 'PM' : 'AM';
    var displayHour = h % 12;
    if (displayHour === 0) displayHour = 12;
    return displayHour + ampm;
  }

  // View Monthly Tides button handler (both in weather modal and detail modal)
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('#view-monthly-tides-btn, #view-monthly-tides-detail-btn');
    if (btn) {
      generateMonthlyTides();
      var modal = new bootstrap.Modal(document.getElementById('monthlyTidesModal'));
      modal.show();
    }
  });

  function refreshWeatherNow() {
    fetchWeatherForWidget(__lastWeatherCoords.lat, __lastWeatherCoords.lon);
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

  // AI Chat button on home page sidebar
  var openAiChatBtn = document.getElementById('open-ai-chat-btn');
  if (openAiChatBtn && chatToggle) {
    openAiChatBtn.addEventListener('click', function() {
      chatWidget.classList.add('expanded');
      if (chatInput) chatInput.focus();
    });
  }

  // Fetch saved locations from API and render markers
  var controller = new AbortController();
  var timeoutId = setTimeout(function() { controller.abort(); }, 3000); // 3 second timeout
  
  fetch('/api/locations', { signal: controller.signal })
    .then(function(res){ 
      clearTimeout(timeoutId);
      return res.json(); 
    })
    .then(function(locations){
      locations = locations || [];
      window.__placeCardsData = locations;

      var markers = [];
      locations.forEach(function(p){
        var marker = L.circleMarker([p.lat, p.lng], {
          radius: 6,
          color: 'rgba(126, 204, 224, 0.9)',
          weight: 2,
          fillColor: 'rgba(126, 204, 224, 0.55)',
          fillOpacity: 1,
        }).addTo(map);
        var popupHtml = '<div style="display:flex;align-items:center"'
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
          // Determine status based on some property or default to Open
          var status = p.status || 'Open';
          var statusClass = status.toLowerCase() === 'open' ? 'open' : 'closed';
          
          // Generate star rating HTML using Font Awesome
          var ratingStars = '';
          if (p.rating !== null && p.rating !== undefined) {
            var r = Math.max(0, Math.min(5, parseInt(p.rating)));
            for (var i = 0; i < 5; i++) {
              if (i < r) {
                ratingStars += '<i class="fa-solid fa-star"></i>';
              } else {
                ratingStars += '<i class="fa-regular fa-star"></i>';
              }
            }
          } else {
            ratingStars = '<i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>';
          }
          
          // Determine tags based on location properties
          var tags = [];
          if (p.type) tags.push(p.type);
          if (p.facilities && p.facilities.toLowerCase().includes('snorkel')) tags.push('Snorkel');
          if (p.fees && p.fees.toLowerCase().includes('public')) tags.push('Public');
          if (p.fees && p.fees.toLowerCase().includes('resort')) tags.push('Resort');
          if (tags.length === 0) tags.push('Beach');
          
          var tagsHtml = tags.map(function(tag) {
            var tagClass = tag.toLowerCase();
            return '<span class="tag ' + tagClass + '">' + tag + '</span>';
          }).join('');
          
          // Calculate initial distance placeholder
          var distanceText = '— km away';
          if (userLatLng && p.lat != null && p.lng != null) {
            var dist = distanceMeters(userLatLng.lat, userLatLng.lng, p.lat, p.lng);
            distanceText = formatDistance(dist);
          }

          var card = document.createElement('div');
          card.className = 'beach-card';
          card.dataset.index = idx;
          card.innerHTML = 
            '<div class="card-header">' +
              '<h3 class="beach-name">' + (p.name || 'Untitled') + '</h3>' +
              '<span class="status-badge ' + statusClass + '">' + status + '</span>' +
            '</div>' +
            '<div class="card-photo">' +
              '<div class="photo-placeholder">' +
                '<i class="fa-regular fa-image"></i>' +
                '<span>Beach photo here</span>' +
              '</div>' +
            '</div>' +
            '<div class="card-footer">' +
              '<div class="beach-meta">' +
                '<div class="rating">' + ratingStars + '</div>' +
                '<div class="distance" data-lat="' + (p.lat || '') + '" data-lng="' + (p.lng || '') + '">' +
                  '<i class="fa-solid fa-location-dot"></i> ' + distanceText +
                '</div>' +
              '</div>' +
              '<div class="beach-tags">' + tagsHtml + '</div>' +
              '<div class="card-actions">' +
                '<button class="btn btn-view" onclick="window.location.href=\'/beach/' + p.id + '\'">View Details</button>' +
                '<button class="btn btn-map">Show on Map</button>' +
              '</div>' +
            '</div>';

          (function(i){
            card.querySelector('.btn-map').addEventListener('click', function(e){
              e.stopPropagation();
              var m = markers[i];
              if (m) { map.setView(m.getLatLng(), 14); m.openPopup(); }
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
      var searchInput = document.getElementById('search-input');
      if (searchInput) {
        searchInput.addEventListener('input', function(e){
          var searchQuery = e.target.value.toLowerCase().trim();
          var cards = document.querySelectorAll('#place-cards .beach-card');
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
      console.log('Using local beach data...');
      
      // Local beach data (Binongkalan beaches)
      var beachLocations = [
        { id: 1, name: 'Ranola Beach Resort', lat: 10.631678, lng: 124.028392, rating: 5, address: '10°37\'54.04"N 124°01\'42.21"E, Binongkalan, Catmon, Cebu', description: 'Beautiful beach resort in Binongkalan, Catmon, Cebu with clear blue waters', fees: '₱100 entrance', facilities: 'Cottages, Restaurant, Swimming', type: 'Resort' },
        { id: 2, name: 'Lite Bay Resort', lat: 10.634139, lng: 124.028370, rating: 4, address: '10°38\'02.90"N 124°01\'42.13"E, Catmon, Cebu', description: 'Peaceful beach resort with serene atmosphere', fees: '₱80 entrance', facilities: 'Cottages, Restaurant', type: 'Resort' },
        { id: 3, name: 'Majestique View Beach Resort', lat: 10.634681, lng: 124.027848, rating: 5, address: '10°38\'04.85"N 124°01\'40.25"E, Catmon, Cebu', description: 'Stunning beach resort with majestic views', fees: '₱150 entrance', facilities: 'Cottages, Restaurant, Swimming', type: 'Resort' },
        { id: 4, name: 'Turtle Point Beach Resort', lat: 10.634739, lng: 124.027580, rating: 5, address: '10°38\'05.06"N 124°01\'39.29"E, Catmon, Cebu', description: 'Marine sanctuary with sea turtles and coral reefs', fees: '₱150 with guide', facilities: 'Snorkeling, Diving, Turtle watching', type: 'Sanctuary' },
        { id: 5, name: 'Hinagdan Beach Resort', lat: 10.636244, lng: 124.026389, rating: 5, address: '10°38\'10.48"N 124°01\'35.00"E, Catmon, Cebu', description: 'Famous cave beach with crystal clear waters and beautiful rock formations', fees: '₱50 entrance', facilities: 'Swimming, Cave tours, Restaurant', type: 'Resort' }
      ];
      
      window.__placeCardsData = beachLocations;
      
      // Render beach cards
      var cardsContainer = document.getElementById('place-cards');
      if (cardsContainer) {
        cardsContainer.innerHTML = '';
        var beachMarkers = [];
        
        beachLocations.forEach(function(p, idx) {
          var card = document.createElement('div');
          card.className = 'beach-card';
          card.dataset.index = idx;
          card.innerHTML = 
            '<div class="card-header">' +
              '<h3 class="beach-name">' + p.name + '</h3>' +
              '<span class="status-badge open">Open</span>' +
            '</div>' +
            '<div class="card-photo">' +
              '<div class="photo-placeholder">' +
                '<i class="fa-regular fa-image"></i>' +
                '<span>Beach photo</span>' +
              '</div>' +
            '</div>' +
            '<div class="card-footer">' +
              '<div class="beach-meta">' +
                '<div class="rating"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>' +
                '<div class="distance"><i class="fa-solid fa-location-dot"></i> — km away</div>' +
              '</div>' +
              '<div class="beach-tags"><span class="tag">' + p.type + '</span></div>' +
              '<div class="card-actions">' +
                '<button class="btn btn-view" onclick="window.location.href=\'/beach/' + p.id + '\'">View Details</button>' +
                '<button class="btn btn-map">Show on Map</button>' +
              '</div>' +
            '</div>';
          
          (function(idx, marker){
            card.querySelector('.btn-map').addEventListener('click', function(e){
              e.stopPropagation();
              if (marker) { map.setView(marker.getLatLng(), 14); marker.openPopup(); }
            });
          })(idx, beachMarkers[idx]);
          
          cardsContainer.appendChild(card);
        });
      }
      
      // Render beach pins on map
      beachLocations.forEach(function(p) {
        var pinIcon = L.icon({
          iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
          iconSize: [32, 32],
          iconAnchor: [16, 32]
        });
        // Compact popup styled like sidebar card
        var popupHtml = '<div class="beach-popup" style="min-width:180px;font-family:Segoe UI,sans-serif;">' +
          '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">' +
            '<h3 style="margin:0;font-size:14px;font-weight:700;color:#1a2a3a;">' + p.name + '</h3>' +
            '<span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;">Open</span>' +
          '</div>' +
          '<div style="background:#f1f5f9;border-radius:6px;height:60px;display:flex;align-items:center;justify-content:center;margin-bottom:8px;">' +
            '<i class="fa-regular fa-image" style="color:#94a3b8;font-size:20px;"></i>' +
          '</div>' +
          '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">' +
            '<span style="color:#fbbf24;font-size:11px;">' + '<i class="fa-solid fa-star"></i>'.repeat(5) + '</span>' +
            '<span style="color:#64748b;font-size:11px;"><i class="fa-solid fa-location-dot"></i> — km</span>' +
          '</div>' +
          '<span style="background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:4px;font-size:10px;display:inline-block;margin-bottom:8px;">' + p.type + '</span>' +
          '<button onclick="window.location.href=\'/beach/' + p.id + '\'" style="width:100%;padding:6px 12px;background:#1e293b;color:white;border:1px solid #ffffff;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background=\'#334155\'" onmouseout="this.style.background=\'#1e293b\'">View Details</button>' +
        '</div>';
        var marker = L.marker([p.lat, p.lng], { icon: pinIcon }).addTo(map).bindPopup(popupHtml);
        
        // Add permanent label with beach name - stagger directions and keep close to pins
        var labelDirection = (p.id % 2 === 0) ? 'right' : 'left';
        var labelOffset = (p.id % 2 === 0) ? [5, -5] : [-5, -5];
        marker.bindTooltip(p.name, {
          permanent: true,
          direction: labelDirection,
          offset: labelOffset,
          className: 'beach-label'
        });
        
        beachMarkers.push(marker);
      });

      // Setup search for beach data
      var searchInput = document.getElementById('search-input');
      if (searchInput) {
        searchInput.addEventListener('input', function(e){
          var searchQuery = e.target.value.toLowerCase().trim();
          var cards = document.querySelectorAll('#place-cards .beach-card');
          
          cards.forEach(function(card, idx){
            var location = beachLocations[idx];
            if (!location) return;
            
            var matches = location.name.toLowerCase().includes(searchQuery) ||
                         location.address.toLowerCase().includes(searchQuery);
            card.style.display = matches || searchQuery === '' ? '' : 'none';
          });
        });
      }
    });

  // ===== AI Chat Widget =====
  var chatWidget = document.getElementById('ai-chat-widget');
  var chatToggle = document.getElementById('ai-chat-toggle');
  var chatClose = document.getElementById('ai-chat-close');
  var chatInput = document.getElementById('ai-chat-input');
  var chatSend = document.getElementById('ai-chat-send');
  var chatMessages = document.getElementById('ai-chat-messages');
  var chatPanel = document.getElementById('ai-chat-panel');

  // Toggle chat widget
  if (chatToggle) {
    chatToggle.addEventListener('click', function() {
      chatPanel.classList.add('active');
      if (chatInput) chatInput.focus();
    });
  }

  // Close chat widget
  if (chatClose) {
    chatClose.addEventListener('click', function() {
      chatPanel.classList.remove('active');
    });
  }

  // Close on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && chatPanel.classList.contains('active')) {
      chatPanel.classList.remove('active');
    }
  });

  // Send message function
  function sendAiMessage(message) {
    if (!message.trim() || !chatMessages) return;

    // Add user message
    var userBubble = document.createElement('div');
    userBubble.className = 'ai-message-bubble ai-user';
    userBubble.innerHTML = '<i class="fa-solid fa-quote-left quote-icon"></i>' + escapeHtml(message.trim());
    chatMessages.appendChild(userBubble);

    // Clear input
    if (chatInput) chatInput.value = '';

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Simulate AI response (replace with actual API call)
    setTimeout(function() {
      var botBubble = document.createElement('div');
      botBubble.className = 'ai-message-bubble ai-bot';
      botBubble.innerHTML = '<i class="fa-solid fa-quote-left quote-icon"></i>Thanks for your question! I\'m here to help you with beach information. (API integration needed)';
      chatMessages.appendChild(botBubble);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 1000);
  }

  // Send on button click
  if (chatSend) {
    chatSend.addEventListener('click', function() {
      if (chatInput) sendAiMessage(chatInput.value);
    });
  }

  // Send on enter key
  if (chatInput) {
    chatInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        sendAiMessage(chatInput.value);
      }
    });
  }

  // Escape HTML helper
  function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Global function for suggestion chips
  window.sendAiQuestion = function(question) {
    if (!chatWidget.classList.contains('expanded')) {
      chatWidget.classList.add('expanded');
    }
    sendAiMessage(question);
  };
  
  // Test function to verify modal works
  window.testModal = function() {
    console.log('Testing modal...');
    var modalEl = document.getElementById('detailModal');
    if (modalEl) {
      document.getElementById('detail-title').textContent = 'Test Beach';
      var modal = new bootstrap.Modal(modalEl);
      modal.show();
      console.log('Modal opened!');
    } else {
      console.error('Modal not found');
    }
  };
});

