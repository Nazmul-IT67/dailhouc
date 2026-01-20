 <link href="{{ asset('backend/css/bootstrap.min.css') }}" rel="stylesheet">

 <style>
     body {
         background: linear-gradient(135deg, #6f42c1, #6610f2, #0d6efd);
         min-height: 100vh;
         display: flex;
         align-items: center;
         justify-content: center;
     }

     .login-card {
         background: #fff;
         border-radius: 15px;
         box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
         padding: 2rem;
         width: 100%;
         max-width: 400px;
     }

     .login-card h3 {
         font-weight: 700;
         margin-bottom: 1rem;
         color: #333;
     }

     .form-control:focus {
         border-color: #6610f2;
         box-shadow: 0 0 0 0.2rem rgba(102, 16, 242, .25);
     }

     .btn-custom {
         background: #6610f2;
         color: #fff;
         transition: 0.3s;
     }

     .btn-custom:hover {
         background: #520dc2;
     }

     .forgot-link {
         font-size: 0.9rem;
     }
 </style>
