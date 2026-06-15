document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('getLocation');
  if (btn) btn.addEventListener('click', function(){
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(pos){
        document.getElementById('lat').value = pos.coords.latitude;
        document.getElementById('lng').value = pos.coords.longitude;
      }, function(err){ alert('Error GPS: '+err.message); });
    } else alert('Geolocalización no soportada');
  });
});
