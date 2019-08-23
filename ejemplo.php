<script>
    /**
 * Sugerencias y correcciones por Jonathan (zerokilled)
 * http://www.forosdelweb.com/miembros/zerokilled/
 */
window.onload = function(){
    var hexVal = "0123456789ABCDEF".split("");
    var defaultColor = '#f00';
    var options = {
        zoom: 9
        , center: new google.maps.LatLng(-16.48349760264812, -68.13858032226562)
        , mapTypeId: google.maps.MapTypeId.ROADMAP
        , draggableCursor: 'default'
        , draggingCursor: 'default'
    };

    var map = new google.maps.Map(document.getElementById('map'), options);

    var polyline = new google.maps.Polyline({
        path: new google.maps.MVCArray()
        , map: map
        , strokeColor: defaultColor
        , strokeWeight: 3
        , strokeOpacity: 0.5
    });

    function makeColor(){

        //Otra forma de crear un color aleatoriamente:

        for(var color = Math.floor(Math.random()*0xffffff).toString(16); color.length < 6; color = '0'+color);
            alert(color);
        return ('#' + color);

        /*return '#' + hexVal.sort(function(){
            return (Math.round(Math.random())-0.5);
        }).slice(0,6).join('');*/
    }

    polyline.currentColor = makeColor();
    google.maps.event.addListener(polyline, 'click', function(e){
        polyline.setOptions({strokeColor: polyline.currentColor});
        //polyline = this, this.setOptions({strokeColor: defaultColor});
    });

    google.maps.event.addListener(map, 'rightclick', function(){
        alert(polyline.currentColor);
        polyline.setOptions({strokeColor: polyline.currentColor});
        polyline = new google.maps.Polyline({
            path: new google.maps.MVCArray()
            , map: map
            , strokeColor: defaultColor
            , strokeWeight: 3
            , strokeOpacity: 0.5
        });

        polyline.currentColor = makeColor();
        google.maps.event.addListener(polyline, 'click', function(){
            polyline.setOptions({strokeColor: polyline.currentColor});
            //polyline = this, this.setOptions({strokeColor: defaultColor});
        });
    });

    google.maps.event.addListener(map, 'click', function(e){
        //alert(e.latLng);
        polyline.setOptions({strokeColor: polyline.currentColor});
        polyline.getPath().push(e.latLng);
    });
};
</script>
<style>
    .map{
        height: 500px;
    }
</style>
<div class="map" id="map">

</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7FN4j43pO5hJesGiaTGDqShcxqzcZLZ8&callback&pb=!1m18!1m12!1m3!1d122407.17394951348!2d-68.1182436037446!3d-16.514775899328214!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915edf0a04f5a40f%3A0x57dbfc76b4458ab3!2sLa+Paz!5e0!3m2!1ses-419!2sbo!4v1565064332758!5m2!1ses-419!2sbo" async defer></script>
