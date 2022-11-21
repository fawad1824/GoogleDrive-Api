<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">


</head>

<body class="antialiased">

    <div class="container">
        <div class="card mt-5">
            <div class="card-header">
                File Upload Google Drive
            </div>
            <div class="card-body">
                <form action="{{ route('google.drive.file.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleFormControlInput1" class="form-label">Picture</label>
                        <input class="form-control" accept="image/*" name="image" type="file">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <br><br>

        <div class="row">
            @foreach ($images as $index => $image)
                <div class="col-lg-3 p-1">
                    <div class="card">
                        <img src="https://drive.google.com/uc?export=view&id={{ $image->pic }}">
                        <div class="card-body">
                            <h5 class="card-title">Card title {{ $index + 1 }}</h5>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk
                                of the card's content {{ $index + 1 }}.</p>
                            <a class="btn btn-success" type="button" href="edit/{{ $image->id }}">Edit</a>
                            <a class="btn btn-danger" type="button" href="delete/{{ $image->pic }}">Delete</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>

</body>

</html>
