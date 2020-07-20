<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('css\bootstrap.min.css')}}">

    <!-- lightgallary -->
    <link rel="stylesheet" href="{{asset('plugins\lightGallery\dist\css\lightgallery.min.css')}}">

    <!-- Dropify -->
    <link rel="stylesheet" href="{{asset('plugins\dropify\dist\css\dropify.min.css')}}">

    <!--custom css -->
    <link rel="stylesheet" href="{{asset('css\style.css')}}">

    <!--alertify -->
    <link rel="stylesheet" href="{{'plugins\alertifyjs\css\alertify.min.css'}}">



    <title>Image Upload</title>
</head>

<body>


    <div class="container" id="containerDiv">
        {{-- <h1>Hello, world!</h1> --}}
        <div>

        </div>
        <h1 class="font-weight-light text-center text-lg-left mt-4 mb-2">Your Images</h1>
        <div class="row">
            <div class="col-sm-2 mb-2">
                <button class="btn btn-success" data-toggle="modal" data-target="#uploadModal">Upload Image</button>
            </div>

            <div class="col-sm-10 form-group has-search">
                <input type="text" class="form-control" placeholder="Search">
            </div>
        </div>


        <hr class="mt-2 mb-5">

        <div class="row text-center text-lg-left" id="gallery">

        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="uploadModal" tabindex="-1" role="dialog"
        aria-labelledby="uploadModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Upload Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="imageUploadForm">
                        <div class="form-group form-my-error">
                            <input type="file" id="image" name="image" class="dropify form-control"
                                data-allowed-file-extensions="png" data-height="200" data-max-file-size="5M"
                                accept="image/png" required />

                        </div>
                        <div class="form-group row">
                            <label for="inputPassword" class="col-sm-2 col-form-label">Image Title*</label>
                            <div class="col-sm-10 form-my-error">
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Image Title" required minlength="3">

                            </div>
                        </div>
                        <div class="col text-center">
                            <button type="submit" id="upload-btn" class="btn btn-primary text-center"><span
                                    id="uploadText">Upload</span></button>
                        </div>
                        <hr class="mt-2 mb-5">
                        <!-- Progress bar -->
                        <div class="progress">
                            <div class="progress-bar"></div>
                        </div>

                    </form>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('js\jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('js\popper.min.js')}}"></script>
    <script src="{{asset('js\bootstrap.min.js')}}"></script>

    <!-- Light Gallery -->
    <script src="{{asset('plugins\lightGallery\dist\js\lightgallery-all.min.js')}}"></script>
    <!-- Dropify -->
    <script src="{{asset('plugins\dropify\dist\js\dropify.min.js')}}"></script>

    <!--jquery validate -->
    <script src="{{asset('plugins\jquery-validation\dist\jquery.validate.min.js')}}"></script>
    <script src="{{asset('plugins\jquery-validation\dist\additional-methods.min.js')}}"></script>

    <!-- alertify -->
    <script src="{{asset('plugins\alertifyjs\alertify.min.js')}}"></script>







    <script>
        $(document).ready(function () {
            //init ajax setup for csrf
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //initial data load
            initiateContent();

            //init Light Gallery
            // $("#gallery").lightGallery({
            //     thumbnail: true,
            //     selector: 'a'
            // });

            //init Dropify
            $('.dropify').dropify();

            // jquery image size validation
            $.validator.addMethod('filesize', function (value, element, arg) {
                var minsize = 1000; // min 1kb
                var filesize = element.files[0].size;
                //console.log(filesize);
                if ((filesize > minsize) && (filesize <= arg)) {
                    return true;
                } else {
                    return false;
                }
            });

            /**
             * @name form onsubmit
             * @description override the default form submission and submit the form manually.
             *              also validate with .validate() method from jquery validation
             * @parameter event
             * @return 
             */
            $('#imageUploadForm').submit(function (e) {
                e.preventDefault();
            }).validate({
                rules: {
                    'image': {
                        required: true,
                        accept: "image/png",
                        filesize: 5120000,
                    }
                },
                messages: {
                    'image': {
                        filesize: "Image size must be less than 5 MB.",
                        accept: "Please upload png formate image file.",
                        required: "Please upload Image."
                    }
                },
                highlight: function (input) {
                    $(input).parents('.form-group').addClass('error');
                    $(input).addClass('select-class');
                },
                unhighlight: function (input) {
                    $(input).parents('.form-group').removeClass('error');
                    $(input).removeClass('select-class');
                },
                errorPlacement: function (error, element) {
                    $(element).parents('.form-my-error').append(error);
                },
                errorClass: 'help-block',
                submitHandler: function (form) {
                    //console.log(form);

                    $("#adminAddModal").modal('hide');
                    var formData = new FormData(form);
                    $.ajax({
                        xhr: function () {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = Math.round((evt
                                        .loaded / evt
                                        .total) * 100);

                                    $(".progress-bar").width(percentComplete +
                                        '%');
                                    $(".progress-bar").html(percentComplete +
                                        '%');
                                }
                            }, false);
                            return xhr;
                        },
                        url: "{{ url('image/upload') }}",
                        method: "POST",
                        data: formData,
                        enctype: 'multipart/form-data',
                        processData: false,
                        cache: false,
                        contentType: false,
                        timeout: 600000,
                        beforeSend: function () {
                            $(".progress-bar").width('0%');
                            $("#uploadText").html("Uploading....");
                            $("#upload-btn").attr('disabled', true);
                        },
                        success: function (result) {
                            if (typeof result.errors !== 'undefined') {
                                // the variable is defined
                                //console.log(result.errors);
                                $.each(result.errors, function (index, val) {
                                    //console.log(index, val)
                                    $.each(val, function (index, val) {
                                        //console.log(index, val)
                                        alertify.notify(val, 'error',
                                            5);
                                    });
                                });
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else {
                                $(form).trigger('reset');
                                resetDropify("#image");
                                $(".progress-bar").width('0%');
                                $(".progress-bar").html('');
                                alertify.notify("Image Upload Successfull", 'success',
                                    5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                                initiateContent();
                                $("#containerDiv").load(location.href +
                                    " #containerDiv");

                            }

                        },
                        error: function (jqXHR, exception) {
                            var msg = '';
                            if (jqXHR.status === 0) {
                                msg = 'Not connect.Verify Network.';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);

                            } else if (jqXHR.status == 404) {
                                msg = 'Requested page not found. [404]';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else if (jqXHR.status == 413) {
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else if (jqXHR.status == 419) {
                                msg = 'CSRF error or Unknown Status [419]';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else if (jqXHR.status == 500) {
                                msg = 'Internal Server Error [500].';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else if (exception === 'parsererror') {
                                msg = 'Requested JSON parse failed.';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else if (exception === 'timeout') {
                                msg = 'Time out error.';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else if (exception === 'abort') {
                                msg = 'Ajax request aborted.';
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            } else {
                                msg = 'Uncaught Error.\n' + jqXHR.responseText;
                                alertify.notify(msg, 'error', 5);
                                $("#uploadText").html("Upload");
                                $("#upload-btn").attr('disabled', false);
                            }

                        }
                    });
                }
            });
            //console.log("validation success");




        });

        function initiateContent() {
            var images = [];
            var html = "";
            $.getJSON("{{asset('data/imageData.json')}}", function (json) {
                images = JSON.parse(JSON.stringify(json));
                images.reverse();
                $.each(images, function (index, value) {
                    //console.log(value);
                    // var image_path = "{{asset("+value.image_path+")}}";
                    // console.log(image_path);
                    html += '<div class="col-lg-3 col-md-4 col-6 mb-2">';
                    html += '<a href="' + value.image_path + '" data-sub-html="' + value.title + '">';

                    html += '<img height="300" width="300" class="img-fluid img-thumbnail" src="' +
                        value.image_path +
                        '" alt=""></a>';
                    html += '<p class="text-truncate">' + value.title + '</p>';
                    html += '<div class="col text-center">';   
                    html += "<button class='btn btn-danger' data-id='"+value.id+"' onclick='deleteImage(this)'>Remove</button>";
                    html += '</div></div>';


                });
                // console.log(html);
                $("#gallery").empty();
                $("#gallery").append(html);
                //init Light Gallery
                $("#gallery").lightGallery({
                    thumbnail: true,
                    selector: 'a'
                });

            });
        }

        function deleteImage(context) {
            id = $(context).attr('data-id');
            console.log(id);
            alertify.confirm('Are You Sure?', 'The image will be deleted! ',
                function () {
                    $.ajax({
                        url: "{{ url('image/delete') }}",
                        method: "POST",
                        data: {
                            id:id
                        },
                        success: function (result) {
                            if (typeof result.errors !== 'undefined') {
                                        alertify.notify(val, 'error',5);
                            } else {
                                
                                alertify.notify("Image Delete Successfull", 'success',5);
                                initiateContent();
                                $("#containerDiv").load(location.href +" #containerDiv");

                            }

                        },
                        error: function (jqXHR, exception) {
                            var msg = '';
                            if (jqXHR.status === 0) {
                                msg = 'Not connect.Verify Network.';
                                alertify.notify(msg, 'error', 5);
                            } else if (jqXHR.status == 404) {
                                msg = 'Requested page not found. [404]';
                                alertify.notify(msg, 'error', 5);
                            } else if (jqXHR.status == 413) {
                                alertify.notify(msg, 'error', 5);
                            } else if (jqXHR.status == 419) {
                                msg = 'CSRF error or Unknown Status [419]';
                                alertify.notify(msg, 'error', 5);
                            } else if (jqXHR.status == 500) {
                                msg = 'Internal Server Error [500].';
                                alertify.notify(msg, 'error', 5);
                            } else if (exception === 'parsererror') {
                                msg = 'Requested JSON parse failed.';
                                alertify.notify(msg, 'error', 5);
                            } else if (exception === 'timeout') {
                                msg = 'Time out error.';
                                alertify.notify(msg, 'error', 5);
                            } else if (exception === 'abort') {
                                msg = 'Ajax request aborted.';
                                alertify.notify(msg, 'error', 5);
                            } else {
                                msg = 'Uncaught Error.\n' + jqXHR.responseText;
                                alertify.notify(msg, 'error', 5);
                            }

                        }
                    });
                    
                },
                function () {
                    alertify.error('Operation Canceled!')
                });

        }

        function resetDropify(id) {
            var drEvent = $(id).dropify();
            drEvent = drEvent.data('dropify');
            drEvent.resetPreview();
            drEvent.clearElement();
        }

    </script>
</body>

</html>
