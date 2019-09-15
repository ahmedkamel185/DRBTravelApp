@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li><a href="{{route('store.index')}}" style="color: white">Manage Suggested Places</a></li>
    <li class="active" style="color: white;font-size: larger"> Edit  Place </li>
@endsection

@section('content')

    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Place</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="{{route('place.update',['id'=>$suggest->id])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label for="desc">Enter Description</label>
                            <input type="text" name="desc" class="form-control" id="desc"
                                   placeholder="Description" value="{{$suggest->desc}}">
                        </div>


                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" class="form-control" id="image"
                                   value="{{$suggest->image}}">
                        </div>




                        <div class="form-group">
                            <label for="store">Enter Address Location</label>
                            <div id="map" style="width:100%;height:300px;margin-top:15px;text-align: center">
                            </div>

                            <input type="text" name="address" class="form-control" placeholder="address">
                            <input type="hidden" id="lat" name="lat">
                            <input type="hidden" id="lng" name="lng">
                        </div>


                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
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
