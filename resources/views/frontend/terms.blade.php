@extends('layouts.app')

@section('content')
<style>
    .gallery-info{
        padding: 0px;
    }
    .terms-ul li{
        font-size: 20px !important;
    font-family: "Saira", Arial, Helvetica, sans-serif !important;
    line-height: 35px !important;
    margin-bottom: 6px;
    }
</style>
<section class="gallery-info">
    <div class="container h-100">
      <div class="row h-100 justify-content-end">
          <div class="col-12 col-lg-6 ">
            <div class="about-info">
            </div>
          </div>
      </div>
    </div>
  </section>
<section class="objective">
  <div class="container">
    <div class="row">
        <div class="col-12">
            <h3 class="text-center">Terms</h3>
            <p>Last Revised: 24 December 2024</p>
            <p>Welcome to PitchBurners.com. By using our website or associated mobile application, you agree to comply with the following terms and conditions. Please read them carefully.</p>
            <ul class="terms-ul">
                <li><strong>Acceptance of Terms</strong>
                    <ul>
                        <li>By accessing or using our services, you acknowledge that you have read, understood, and agree to these terms.</li>
                        <li>If you do not agree, please refrain from using the platform.</li>
                    </ul>
                </li>
                <li><strong>User Responsibilities</strong>
                    <ul>
                        <li>Users must provide accurate and up-to-date information during registration or team participation.</li>
                        <li>Unauthorized access or use of the platform for fraudulent activities is strictly prohibited.</li>
                        <li>Admins must safeguard their login credentials and ensure match data is entered accurately.</li>
                    </ul>
                </li>
                <li><strong>Prohibited Activities</strong>
                    <ul>
                        <li>Misuse of platform features, such as falsifying scores or standings.</li>
                        <li>Uploading harmful or malicious content.</li>
                        <li>Engaging in activities that disrupt the operation of the platform.</li>
                    </ul>
                </li>
                <li><strong>Content Ownership</strong>
                    <ul>
                        <li>All content, including team data, scores, and statistics, is owned by Pitch Burners Sports Foundation.</li>
                        <li>Users may not replicate or redistribute content without permission.</li>
                    </ul>
                </li>
                <li><strong>Limitation of Liability</strong>
                    <ul>
                        <li>The platform is provided "as is," and we make no guarantees regarding uninterrupted access or error-free functionality.</li>
                        <li>We are not liable for any losses or damages arising from your use of the platform.</li>
                    </ul>
                </li>
                <li><strong>Changes to Terms</strong>
                    <ul>
                        <li>We reserve the right to modify these terms at any time.</li>
                        <li>Continued use of the platform after updates signifies your acceptance of the revised terms.</li>
                    </ul>
                </li>
                <li><strong>Contact Us</strong>
                    <ul>
                        <li>If you have any questions or concerns regarding these terms, please contact us at <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a></li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>
  </div>
</section>


@endsection
