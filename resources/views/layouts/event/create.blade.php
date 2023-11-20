@extends('layouts.app')

@section('content')

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">Create New Event</div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Event Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="image_url">Event Image:</label>
                                <input type="file" class="form-control" id="image_url" name="image_url" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="category_id">Category</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Select a Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>

                            <div class="form-row d-flex justify-content-around mb-3">
                                <div class="form-group col-md-5 me-1">
                                    <label for="start_date">Start Date:</label>
                                    <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                           required>
                                </div>
                                <div class="form-group col-md-5 ms-1">
                                    <label for="end_date">End Date:</label>
                                    <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                           required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="start_tickets_qty">Start Tickets Quantity:</label>
                                <input type="number" class="form-control" id="start_tickets_qty"
                                       name="start_tickets_qty"
                                       required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="current_price">Ticket Price:</label>
                                <input type="number" step="0.01" class="form-control" id="current_price"
                                       name="current_price" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="address_search">Search Address (City, State, Country): </label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="address_search" name="address_search"
                                           placeholder="Type address..." required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="searchInMap()">
                                            Search in Map
                                        </button>
                                    </div>
                                </div>

                                <div id="preview-map" style="height: 400px; margin-top: 20px;"></div>

                                <!-- Hidden input for latitude -->
                                <input type="hidden" id="lat" name="lat">

                                <!-- Hidden input for longitude -->
                                <input type="hidden" id="lon" name="lon">
                            </div>

                            <div class="form-group mb-3">
                                <label for="address">Address Description (Number, Neighborhood, Reference):</label>
                                <input type="text" class="form-control" id="address" name="address"
                                       placeholder="Type address's number, neighborhood, reference..." required>
                            </div>


                            @if(!$hasLegalId)
                                <div class="form-group mb-3">
                                    <label for="legal_id">Legal ID:</label>
                                    <input type="text" class="form-control" id="legal_id" name="legal_id"
                                           placeholder="Enter your legal ID">
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary">Create Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let mapPreview;
            let mapMarker;

            function initMap() {
                mapPreview = L.map('preview-map').setView([-23.550520, -46.633308], 12); // Coordenadas iniciais (São Paulo, por exemplo)

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(mapPreview);

                mapMarker = L.marker([-23.550520, -46.633308], {draggable: true}).addTo(mapPreview);

                mapMarker.on('dragend', function (event) {
                    updateLatAndLonInputs(mapMarker.getLatLng().lat, mapMarker.getLatLng().lng);
                });
            }

            function searchInMap() {
                const address = document.getElementById('address_search').value;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${address}`)
                    .then(response => response.json())
                    .then(data => {
                        const location = data[0];
                        mapPreview.setView([location.lat, location.lon], 12);
                        mapMarker.setLatLng([location.lat, location.lon]);
                        updateLatAndLonInputs(location.lat, location.lon);
                    })
                    .catch(error => {
                        console.error('Error searching:', error);
                    });
            }

            function updateLatAndLonInputs(lat, lng) {
                document.getElementById('lat').value = lat;
                document.getElementById('lon').value = lng;
            }

            window.onload = initMap;
        </script>

    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

@endsection