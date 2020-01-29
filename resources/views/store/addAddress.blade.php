@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li><a href="{{route('store.index')}}" style="color: white">Manage Service Providers</a></li>
    <li><a href="{{route('store.show',['id'=>$store->id])}}" style="color: white">Store Provider Details</a></li>
    <li class="active" style="color: white;font-size: larger">Add New Address </li>
@endsection
@section('content')
    @if(session()->has('success'))
        <h2 class="page-header">Manage In-App Content</h2>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i></h4>
            {{session()->get('success')}}
        </div>
    @endif
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add New Address </h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="{{route('store.save',['id'=>$store->id])}}" method="post">
                    @csrf

                    <div class="form-group">
                        <label for="store">Enter Address Location</label>
                        <div id="map" style="width:100%;height:300px;margin-top:15px;text-align: center">
                        </div>

                        <input type="text" name="address" class="form-control" placeholder="address">
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                    </div>

                    <div class="form-group">
                        <label for="desc">Description</label>
                        <textarea name="desc" id="" cols="30" rows="10" class="form-control"></textarea>

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
