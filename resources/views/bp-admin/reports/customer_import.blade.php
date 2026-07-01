<!DOCTYPE html>

<html>

<head>

    <title>Laravel 5.8 Import Export Excel to database Example - ItSolutionStuff.com</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

</head>

<body>

   

<div class="container">

    <div class="card bg-light mt-3">

        <div class="card-header">

           Import Export Excel to database Example 

        </div>

        <div class="card-body">

            <form action="{{ url('/bp-admin/reports/customer-import') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <input type="file" name="file" class="form-control">

                <br>

                <button class="btn btn-success">Import User Data</button>


            </form>

        </div>

    </div>

</div>

   

</body>

</html>