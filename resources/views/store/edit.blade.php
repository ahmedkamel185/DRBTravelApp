@extends('admin.index')
@section('cs')

@endsection
@section('content')
    <div class="" style="text-align: center; border: 2px solid #CCCCCC">


        <div  id="map" style="width:100%;height:400px;margin-top:15px;text-align: center">
        </div>

        <div>
            <form action="{{route('store.update',['id'=>$place->id])}}" method="post">
                @csrf
                <input type="text" name="address" class="form-control" placeholder="address">
                <textarea name="desc"cols="5" rows="5" class="form-control">{{$place->desc}}</textarea>
                <input type="hidden" id="lat" name="lat">
                <input type="hidden" id="lng" name="lng">
                <input type="submit" value="send data">
            </form>

        </div>

    </div>



@endsection
@section('js')

    <script type="text/javascript">
        function initMap() {
            var latlng = new google.maps.LatLng('30.044281', '31.340002');
            var map = new google.maps.Map(document.getElementById('map'), {
                center: latlng,
                zoom: 15,
                disableDefaultUI: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });


            var marker = new google.maps.Marker({
                position: latlng,
                animation: google.maps.Animation.DROP,
                map: map,
                draggable: true
            });


            google.maps.event.addListener(marker, 'dragend', function (event) {
                document.getElementById("lat").value = this.getPosition().lat();
                document.getElementById("lng").value = this.getPosition().lng();
                var latitude = roundOf(this.getPosition().lat(), 4);
                var longitude = roundOf(this.getPosition().lng(), 4);

                function roundOf(n, p) {
                    const n1 = n * Math.pow(10, p + 1);
                    const n2 = Math.floor(n1 / 10);
                    if (n1 >= (n2 * 10 + 5)) {
                        return (n2 + 1) / Math.pow(10, p);
                    }
                    return n2 / Math.pow(10, p);
                }

                var urlTo = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," +
                    longitude + "&key=AIzaSyC5uC_mExFIMSehvCgsjegxcF7mTpKmI4w&sensor=true&language=ar";
                $.ajax({
                    url: [urlTo],
                    cache: false,
                    success: function (data) {
                        var address = data.results[0].formatted_address;
                        console.log(latitude)
                        $("input[name='lat']").val(latitude);
                        $("input[name='lng']").val(longitude);
                        $("input[name='address']").val(address);
                        $('.loc').html(address).fadeIn();
                    }
                });
            });

        }
    </script>


    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB90FxtYG_ybAYXGkz0ybkmkboE2nEbezI&callback=initMap&libraries=places">
    </script>



@endsection