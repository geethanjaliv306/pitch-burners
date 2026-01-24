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
    margin-bottom: 8px;
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
            <h3 class="text-center">PRIVACY</h3>
            <p>Last Revised: 24 December 2024</p>
            <p>Your privacy is important to us. This Privacy Policy outlines how PitchBurners.com (the "Platform") collects, uses, stores, and protects your information. By using the Platform, you agree to the terms of this Privacy Policy.</p>
            <ul class="terms-ul">
                <li><strong>Information We Collect</strong>
                    <ul>
                        <li>Personal Information: Name, email address, and phone number when you register as an admin and users.</li>
                        <li>Usage Data: Browser type, device information, IP address, and usage patterns.</li>
                        <li>Match Data: Team and player details, scores, and match statistics.</li>
                    </ul>
                </li>
                <li><strong>How We Use Your Information</strong>
                    <ul>
                        <li>Facilitate match administration and scorekeeping.</li>
                        <li>Provide live match updates and statistics to users.</li>
                        <li>Improve platform functionality and user experience.</li>
                        <li>Communicate with users regarding updates or technical issues.</li>
                    </ul>
                </li>
                <li><strong>Information Sharing</strong>
                    <ul>
                        <li>We do not sell or rent your information to third parties. Your information may be shared with:</li>
                        <ul>
                            <li>Service Providers: Trusted third parties assisting with hosting, analytics, or support.</li>
                            <li>Legal Authorities: When required by law or to protect our rights.</li>
                        </ul>
                    </ul>
                </li>
                <li><strong>Cookies and Tracking Technologies</strong>
                    <p>We use cookies and similar technologies to enhance your experience. You can manage your cookie preferences through your browser settings.</p>
                </li>
                <li><strong>Data Security</strong>
                    <p>We implement industry-standard security measures to protect your data. However, no method of transmission over the internet is completely secure, and we cannot guarantee absolute security.</p>
                </li>
                <li><strong>Third-Party Links</strong>
                    <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these external sites and recommend reviewing their policies.</p>
                </li>
                <li><strong>Children’s Privacy</strong>
                    <p>Our platform is not intended for users under the age of 13. We do not knowingly collect personal information from children.</p>
                </li>
                <li><strong>Your Rights</strong>
                    <ul>
                        <li>Access or update your information.</li>
                        <li>Request deletion of your personal data, subject to legal obligations.</li>
                        <li>Opt out of marketing communications.</li>
                    </ul>
                </li>
                <li><strong>Changes to This Policy</strong>
                    <p>We may update this Privacy Policy periodically. Changes will be posted on this page with the "Effective Date" updated accordingly.</p>
                </li>
                <li><strong>Contact Us</strong>
                    <p>If you have any questions or concerns regarding this Privacy Policy, please contact us at <a href="mailto:cricket@pitchburners.com">cricket@pitchburners.com</a></p>
                </li>
            </ul>
        </div>

    </div>
  </div>
</section>


@endsection
