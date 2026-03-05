@extends('admin.layouts.app')

@section('title', 'Add Blog')

@section('content')

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Add Blog</h4>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.blog.store') }}">
                        @csrf

                        <div class="form-group">
                            <label class="col-md-12">Title</label>
                            <div class="col-md-12">
                                <input
                                    type="text"
                                    name="title"
                                    class="form-control form-control-line"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Image</label>
                            <div class="col-md-12">

                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="image"
                                        id="image"
                                        class="form-control form-control-line"
                                    >
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        onclick="openCKFinder()">
                                        Choose
                                    </button>
                                </div>

                                <div class="mt-2">
                                    <img
                                        id="preview-image"
                                        style="max-width: 200px; display: none;"
                                    >
                                </div>

                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-12">Description</label>
                            <div class="col-md-12">
                                <textarea
                                    name="description"
                                    id="description"
                                    rows="6"
                                    class="form-control form-control-line">
                                </textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button class="btn btn-success text-white">
                                    Save
                                </button>
                                <a href="{{ route('admin.blog.index') }}"
                                   class="btn btn-secondary">
                                    Back
                                </a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection


@push('scripts')
<script src="/ckfinder/ckfinder.js"></script>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description', {
        filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
        filebrowserUploadUrl: '/ckfinder/connector/php/connector.php?command=QuickUpload&type=Files'
    });

    function openCKFinder() {
        CKFinder.popup({
            chooseFiles: true,
            width: 800,
            height: 600,
            onInit: function (finder) {
                finder.on('files:choose', function (evt) {
                    const file = evt.data.files.first();
                    const url = file.getUrl();

                    document.getElementById('image').value = url;
                    document.getElementById('preview-image').src = url;
                    document.getElementById('preview-image').style.display = 'block';
                });

                finder.on('file:choose:resizedImage', function (evt) {
                    const url = evt.data.resizedUrl;

                    document.getElementById('image').value = url;
                    document.getElementById('preview-image').src = url;
                    document.getElementById('preview-image').style.display = 'block';
                });
            }
        });
    }
</script>
@endpush

