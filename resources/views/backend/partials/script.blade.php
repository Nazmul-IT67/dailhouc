<!-- latest jquery-->
<script src="{{ asset('backend/js/jquery.min.js') }}"></script>
{{-- dashboard --}}
<script src="{{ asset('backend/js/bootstrap.bundle.min.js') }}"></script>
<!-- Bootstrap js-->
<script src="{{ asset('backend/js/bootstrap.bundle.min.js') }}"></script>
<!-- feather icon js-->
<script src="{{ asset('backend/js/dropify.min.js') }}"></script>
<script src="{{ asset('backend/js/ckeditor.js') }}"></script>
<script src="{{ asset('backend/js/feather.min.js') }}"></script>
<script src="{{ asset('backend/js/feather-icon.js') }}"></script>
<script src="{{ asset('backend/js/datatables.min.js') }}"></script>
<!-- scrollbar js-->
<script src="{{ asset('backend/js/simplebar.js') }}"></script>
<script src="{{ asset('backend/js/custom.js') }}"></script>
<!-- Sidebar jquery-->
<script src="{{ asset('backend/js/config.js') }}"></script>
<!-- Plugins JS start-->
<script src="{{ asset('backend/js/sidebar-menu.js') }}"></script>
<script src="{{ asset('backend/js/sidebar-pin.js') }}"></script>
<script src="{{ asset('backend/js/slick.min.js') }}"></script>
<script src="{{ asset('backend/js/slick.js') }}"></script>
<script src="{{ asset('backend/js/header-slick.js') }}"></script>
<script src="{{ asset('backend/js/stock-prices.js') }}"></script>
<script src="{{ asset('backend/js/moment.min.js') }}"></script>
<script src="{{ asset('backend/js/esl.js') }}"></script>
<script src="{{ asset('backend/js/echart_config.js') }}"></script>
<script src="{{ asset('backend/js/facePrint.js') }}"></script>
<script src="{{ asset('backend/js/testHelper.js') }}"></script>
<script src="{{ asset('backend/js/custom-transition-texture.js') }}"></script>
<script src="{{ asset('backend/js/symbols.js') }}"></script>
<!-- calendar js-->
<script src="{{ asset('backend/js/datepicker.js') }}"></script>
<script src="{{ asset('backend/js/datepicker.en.js') }}"></script>
<script src="{{ asset('backend/js/datepicker.custom.js') }}"></script>
{{-- <script src="{{ asset('backend/js/dashboard_3.js') }}"></script> --}}
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{ asset('backend/js/script.js') }}"></script>
{{-- <script src="{{ asset('backend/js/customizer.js') }}"></script> --}}
{{-- dropify start --}}

{{-- @vite(['resources/js/app.js']) --}}

<script>
    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@if (session('t-validation'))
    <script>
        let messages = @json(session('t-validation'));
        messages.forEach(function(msg) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: msg,
            });
        });
    </script>
@endif
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif
@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    </script>
@endif
{{-- dropify end --}}

@stack('script')
